<?php

namespace App\Livewire\Student;

use App\Models\FeePayment;
use App\Models\Seat;
use App\Models\StudentAttendance;
use App\Models\StudentSubscription;
use App\Livewire\Student\Concerns\ResolvesActiveTenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use ResolvesActiveTenant;

    public ?int $activeTenantId = null;

    public function mount(): void
    {
        $this->activeTenantId = $this->resolveActiveTenantId();
    }

    public function render()
    {
        $student = Auth::user();
        $this->activeTenantId = $this->resolveActiveTenantId();

        $memberships = $this->getStudentMemberships();
        $activeMembership = $memberships->firstWhere('tenant_id', $this->activeTenantId);
        $activeTenant = $activeMembership?->tenant;

        $activeSeat = Seat::query()
            ->where('tenant_id', $this->activeTenantId)
            ->where('user_id', $student->id)
            ->first();

        $activeSubscription = StudentSubscription::query()
            ->with('plan')
            ->where('tenant_id', $this->activeTenantId)
            ->where('user_id', $student->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        $todayAttendance = StudentAttendance::query()
            ->where('user_id', $student->id)
            ->where('tenant_id', $this->activeTenantId)
            ->whereDate('date', Carbon::today())
            ->first();

        $monthlyAttendance = StudentAttendance::query()
            ->where('user_id', $student->id)
            ->where('tenant_id', $this->activeTenantId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $presentCount = (int) ($monthlyAttendance['present'] ?? 0);
        $absentCount = (int) ($monthlyAttendance['absent'] ?? 0);
        $leaveCount = (int) ($monthlyAttendance['leave'] ?? 0);
        $attendanceTotal = $presentCount + $absentCount + $leaveCount;
        $attendanceRate = $attendanceTotal > 0 ? round(($presentCount / $attendanceTotal) * 100, 1) : 0;

        $feesPaid = (float) FeePayment::query()
            ->where('user_id', $student->id)
            ->where('tenant_id', $this->activeTenantId)
            ->where('status', 'paid')
            ->sum('amount');

        $feesDue = (float) FeePayment::query()
            ->where('user_id', $student->id)
            ->where('tenant_id', $this->activeTenantId)
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');

        $recentFeePayments = FeePayment::query()
            ->where('user_id', $student->id)
            ->where('tenant_id', $this->activeTenantId)
            ->latest('payment_date')
            ->take(5)
            ->get();

        return view('livewire.student.dashboard', [
            'student' => $student,
            'todayAttendance' => $todayAttendance,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'leaveCount' => $leaveCount,
            'attendanceRate' => $attendanceRate,
            'feesPaid' => $feesPaid,
            'feesDue' => $feesDue,
            'recentFeePayments' => $recentFeePayments,
            'memberships' => $memberships,
            'activeTenant' => $activeTenant,
            'activeSeat' => $activeSeat,
            'activeSubscription' => $activeSubscription,
        ])->layout('layouts.app', [
            'header' => 'Student Dashboard',
        ]);
    }
}
