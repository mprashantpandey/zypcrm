<?php

namespace App\Livewire\Library;

use App\Models\LibraryLead;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Leads extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public string $name = '';
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $message = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function saveLead(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        LibraryLead::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'message' => $validated['message'] ?? null,
            'source' => 'manual',
            'status' => 'new',
        ]);

        $this->reset(['name', 'phone', 'email', 'message']);
        $this->dispatch('notify', type: 'success', message: 'Lead added successfully.');
    }

    public function changeStatus(int $leadId, string $status): void
    {
        if (! in_array($status, ['new', 'contacted', 'converted', 'closed'], true)) {
            return;
        }

        $lead = LibraryLead::query()
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($leadId);

        $lead->update([
            'status' => $status,
            'contacted_at' => $status === 'contacted' ? now() : $lead->contacted_at,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Lead status updated.');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $query = LibraryLead::query()->where('tenant_id', $tenantId);

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        return view('livewire.library.leads', [
            'leads' => $query->latest()->paginate(12),
            'stats' => [
                'total' => LibraryLead::where('tenant_id', $tenantId)->count(),
                'new' => LibraryLead::where('tenant_id', $tenantId)->where('status', 'new')->count(),
                'contacted' => LibraryLead::where('tenant_id', $tenantId)->where('status', 'contacted')->count(),
                'converted' => LibraryLead::where('tenant_id', $tenantId)->where('status', 'converted')->count(),
            ],
        ]);
    }
}
