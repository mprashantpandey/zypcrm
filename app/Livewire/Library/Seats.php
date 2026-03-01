<?php

namespace App\Livewire\Library;

use App\Models\Seat;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Seats extends Component
{
    use WithPagination;

    public $search = '';

    public $occupancyFilter = 'all';

    // Single Add/Edit Modal
    public $isModalOpen = false;

    public $seatId = null;

    public $name = '';

    // Bulk Generate Modal
    public $isBulkModalOpen = false;

    public $prefix = 'Seat-';

    public $count = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOccupancyFilter()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function openBulkModal()
    {
        $this->isBulkModalOpen = true;
        $this->prefix = 'S-';
        $this->count = 10;
    }

    public function closeBulkModal()
    {
        $this->isBulkModalOpen = false;
    }

    public function resetInputFields()
    {
        $this->seatId = null;
        $this->name = '';
    }

    public function edit($id)
    {
        $seat = Seat::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $this->seatId = $seat->id;
        $this->name = $seat->name;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenantId = Auth::user()->tenant_id;

        // Check if name already exists for this tenant
        $exists = Seat::where('tenant_id', $tenantId)
            ->where('name', $this->name)
            ->where('id', '!=', $this->seatId)
            ->exists();

        if ($exists) {
            $this->addError('name', 'A seat with this name already exists.');

            return;
        }

        if ($this->seatId) {
            $seat = Seat::where('tenant_id', $tenantId)->findOrFail($this->seatId);
            $seat->update([
                'name' => $this->name,
            ]);
        } else {
            Seat::create([
                'name' => $this->name,
                'tenant_id' => $tenantId,
            ]);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->seatId ? 'Seat updated successfully.' : 'Seat created successfully.',
        ]);
        $this->closeModal();
    }

    public function generateBulk()
    {
        $this->validate([
            'prefix' => 'required|string|max:50',
            'count' => 'required|integer|min:1|max:100',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $createdCount = 0;

        for ($i = 1; $i <= $this->count; $i++) {
            $seatName = $this->prefix.str_pad($i, 2, '0', STR_PAD_LEFT);

            // Only create if it doesn't already exist
            $exists = Seat::where('tenant_id', $tenantId)->where('name', $seatName)->exists();
            if (! $exists) {
                Seat::create([
                    'name' => $seatName,
                    'tenant_id' => $tenantId,
                ]);
                $createdCount++;
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "$createdCount seats were securely generated.",
        ]);
        $this->closeBulkModal();
    }

    public function delete($id)
    {
        $seat = Seat::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);

        // You generally shouldn't delete a seat if it's currently occupied,
        // but for now we simply unassign it by deleting (database will handle if cascade, else we need to free it).
        // Since seats table has user_id, deleting it removes the assignment.
        $seat->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Seat deleted successfully.',
        ]);
    }

    public function unassign($id)
    {
        $seat = Seat::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $before = $seat->only(['id', 'tenant_id', 'name', 'status', 'user_id']);
        $seat->update(['user_id' => null, 'status' => 'available']);

        app(AuditLogService::class)->log(
            action: 'seat.unassigned',
            entityType: Seat::class,
            entityId: $seat->id,
            oldValues: $before,
            newValues: $seat->fresh()->only(['id', 'tenant_id', 'name', 'status', 'user_id']),
            actor: Auth::user(),
            tenantId: Auth::user()->tenant_id,
            request: request()
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Seat has been marked as Vacant.',
        ]);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $seats = Seat::where('tenant_id', $tenantId)
            ->when($this->occupancyFilter === 'occupied', function ($query) {
                $query->whereNotNull('user_id');
            })
            ->when($this->occupancyFilter === 'vacant', function ($query) {
                $query->whereNull('user_id');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($innerQuery) {
                    $innerQuery->where('name', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($q) {
                            $q->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->with('user')
            ->orderBy('name')
            ->paginate(24);

        $stats = [
            'total' => Seat::where('tenant_id', $tenantId)->count(),
            'occupied' => Seat::where('tenant_id', $tenantId)->whereNotNull('user_id')->count(),
            'vacant' => Seat::where('tenant_id', $tenantId)->whereNull('user_id')->count(),
        ];

        return view('livewire.library.seats', [
            'seats' => $seats,
            'stats' => $stats,
        ])->layout('layouts.app', [
            'header' => 'Seating Arrangement',
        ]);
    }
}
