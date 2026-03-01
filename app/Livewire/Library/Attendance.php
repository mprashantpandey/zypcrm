<?php

namespace App\Livewire\Library;

use App\Models\User;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Attendance extends Component
{
    public $attendanceDate;
    public $search = '';

    public function mount()
    {
        $this->attendanceDate = date('Y-m-d');
    }

    public function markAttendance($studentId, $status)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $attendance = StudentAttendance::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'user_id' => $studentId,
                'date' => $this->attendanceDate,
            ],
            [
                'status' => $status,
            ]
        );

        if ($status === 'present' && empty($attendance->check_in)) {
            $attendance->update(['check_in' => Carbon::now()->format('H:i')]);
        } elseif ($status === 'absent' || $status === 'leave') {
            $attendance->update(['check_in' => null, 'check_out' => null]);
        }
        
        $this->dispatch('notify', type: 'success', message: 'Attendance marked as ' . ucfirst($status) . '.');
    }

    public function updateTime($studentId, $field, $value)
    {
        $tenantId = Auth::user()->tenant_id;
        
        // Only update time if the status exists and isn't absent
        $attendance = StudentAttendance::where('tenant_id', $tenantId)
            ->where('user_id', $studentId)
            ->where('date', $this->attendanceDate)
            ->first();

        if ($attendance) {
            $attendance->update([$field => empty($value) ? null : $value]);
            $this->dispatch('notify', type: 'success', message: ucfirst(str_replace('_', ' ', $field)) . ' updated.');
        }
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $studentsQuery = User::where('role', 'student')
            ->where('tenant_id', $tenantId);

        if (!empty($this->search)) {
            $studentsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $students = $studentsQuery->orderBy('name')->get();

        $existingAttendances = StudentAttendance::where('tenant_id', $tenantId)
            ->where('date', $this->attendanceDate)
            ->get()
            ->keyBy('user_id');

        return view('livewire.library.attendance', [
            'students' => $students,
            'existingAttendances' => $existingAttendances,
        ])->layout('layouts.app', [
            'header' => 'Attendance Management'
        ]);
    }
}