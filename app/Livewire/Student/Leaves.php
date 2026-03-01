<?php

namespace App\Livewire\Student;

use App\Livewire\Student\Concerns\ResolvesActiveTenant;
use App\Models\StudentLeave;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Leaves extends Component
{
    use WithPagination;
    use ResolvesActiveTenant;

    public string $startDate = '';
    public string $endDate = '';
    public string $reason = '';
    public string $filterStatus = 'all';

    public function mount(): void
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function submitLeaveRequest(): void
    {
        $validated = $this->validate([
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        StudentLeave::create([
            'tenant_id' => $this->resolveActiveTenantId(),
            'user_id' => Auth::id(),
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'reason' => trim($validated['reason']),
            'status' => 'pending',
        ]);

        $this->reset('reason');
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();

        session()->flash('message', 'Leave request submitted successfully.');
    }

    public function render()
    {
        $tenantId = $this->resolveActiveTenantId();

        $leaves = StudentLeave::query()
            ->where('user_id', Auth::id())
            ->where('tenant_id', $tenantId)
            ->when($this->filterStatus !== 'all', fn ($query) => $query->where('status', $this->filterStatus))
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.student.leaves', [
            'leaves' => $leaves,
            'activeTenant' => $this->getStudentMemberships()->firstWhere('tenant_id', $tenantId)?->tenant,
        ])->layout('layouts.app', [
            'header' => 'My Leave Requests',
        ]);
    }
}
