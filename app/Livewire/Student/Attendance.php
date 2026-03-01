<?php

namespace App\Livewire\Student;

use App\Livewire\Student\Concerns\ResolvesActiveTenant;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Attendance extends Component
{
    use WithPagination;
    use ResolvesActiveTenant;

    public string $status = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $studentId = Auth::id();
        $tenantId = $this->resolveActiveTenantId();

        $attendanceRecords = StudentAttendance::query()
            ->where('user_id', $studentId)
            ->where('tenant_id', $tenantId)
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->latest('date')
            ->paginate(12);

        return view('livewire.student.attendance', [
            'attendanceRecords' => $attendanceRecords,
            'activeTenant' => $this->getStudentMemberships()->firstWhere('tenant_id', $tenantId)?->tenant,
        ])->layout('layouts.app', [
            'header' => 'My Attendance',
        ]);
    }
}
