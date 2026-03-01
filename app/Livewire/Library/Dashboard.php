<?php

namespace App\Livewire\Library;

use App\Models\FeePayment;
use App\Models\Seat;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $activeStudents = User::query()
            ->where([['role', '=', 'student']])
            ->where([['tenant_id', '=', $tenantId]])
            ->count();

        $totalSeats = Seat::query()->where([['tenant_id', '=', $tenantId]])->count();
        $occupiedSeats = Seat::query()->where([['tenant_id', '=', $tenantId]])->whereNotNull('user_id')->count();
        $vacantSeats = $totalSeats - $occupiedSeats;

        $revenueToday = FeePayment::query()
            ->where([['tenant_id', '=', $tenantId]])
            ->whereDate('payment_date', Carbon::today())
            ->where([['status', '=', 'paid']])
            ->sum('amount');

        $recentPayments = FeePayment::query()
            ->with('student')
            ->where([['tenant_id', '=', $tenantId]])
            ->where([['status', '=', 'paid']])
            ->latest('payment_date')
            ->take(5)
            ->get();

        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();
        $days = collect(range(6, 0))->map(fn (int $daysAgo) => Carbon::today()->subDays($daysAgo));

        $revenueByDate = FeePayment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('payment_date, SUM(amount) as total')
            ->groupBy('payment_date')
            ->pluck('total', 'payment_date');

        $presentByDate = StudentAttendance::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'present')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('date, COUNT(DISTINCT user_id) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $attendanceRows = StudentAttendance::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('date, status, COUNT(*) as total')
            ->groupBy('date', 'status')
            ->get();

        $attendanceMatrix = [];
        foreach ($attendanceRows as $row) {
            $attendanceMatrix[$row->date][$row->status] = (int) $row->total;
        }

        $labels = $days->map(fn (Carbon $day) => $day->format('D'))->toArray();
        $revenueSeries = $days->map(fn (Carbon $day) => (float) ($revenueByDate[$day->toDateString()] ?? 0))->toArray();
        $seatUtilizationSeries = $days->map(function (Carbon $day) use ($presentByDate, $totalSeats) {
            $present = (int) ($presentByDate[$day->toDateString()] ?? 0);

            return $totalSeats > 0 ? round(($present / $totalSeats) * 100, 2) : 0;
        })->toArray();
        $attendancePresent = $days->map(fn (Carbon $day) => (int) ($attendanceMatrix[$day->toDateString()]['present'] ?? 0))->toArray();
        $attendanceAbsent = $days->map(fn (Carbon $day) => (int) ($attendanceMatrix[$day->toDateString()]['absent'] ?? 0))->toArray();
        $attendanceLeave = $days->map(fn (Carbon $day) => (int) ($attendanceMatrix[$day->toDateString()]['leave'] ?? 0))->toArray();

        $chartData = [
            'labels' => $labels,
            'weeklyRevenue' => $revenueSeries,
            'seatUtilization' => $seatUtilizationSeries,
            'attendancePresent' => $attendancePresent,
            'attendanceAbsent' => $attendanceAbsent,
            'attendanceLeave' => $attendanceLeave,
        ];

        return view('livewire.library.dashboard', [
            'activeStudents' => $activeStudents,
            'totalSeats' => $totalSeats,
            'occupiedSeats' => $occupiedSeats,
            'vacantSeats' => $vacantSeats,
            'revenueToday' => $revenueToday,
            'recentPayments' => $recentPayments,
            'chartData' => $chartData,
        ])->layout('layouts.app', [
            'header' => 'Library Overview',
        ]);
    }
}
