<?php

namespace App\Livewire\Library;

use App\Models\StudentLeave;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Leaves extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = 'all'; // all, pending, approved, rejected

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updateStatus($leaveId, $newStatus)
    {
        $tenantId = Auth::user()->tenant_id;

        $leave = StudentLeave::query()->where([['tenant_id', '=', $tenantId]])
            ->where([['id', '=', $leaveId]])
            ->first();

        if ($leave) {
            $leave->update(['status' => $newStatus]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Leave status updated to '.ucfirst($newStatus).'.']);
        }
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $leavesQuery = StudentLeave::query()->with('user')
            ->where([['tenant_id', '=', $tenantId]]);

        if ($this->filterStatus !== 'all') {
            $leavesQuery->where([['status', '=', $this->filterStatus]]);
        }

        if (! empty($this->search)) {
            $leavesQuery->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        $leaves = $leavesQuery->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.library.leaves', [
            'leaves' => $leaves,
        ])->layout('layouts.app', [
            'header' => 'Leave Management',
        ]);
    }
}
