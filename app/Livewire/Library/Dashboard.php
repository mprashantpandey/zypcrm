<?php

namespace App\Livewire\Library;

use App\Models\FeePayment;
use App\Models\LibraryPlan;
use App\Models\Seat;
use App\Models\StudentAttendance;
use App\Models\StudentMembership;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Dashboard extends Component
{
    public function dismissOnboarding(): void
    {
        $tenant = Auth::user()->tenant;
        if (! $tenant) {
            return;
        }
        if (! Schema::hasColumn('tenants', 'onboarding_dismissed_at')) {
            $this->dispatch('notify', type: 'error', message: 'Onboarding requires latest migrations.');

            return;
        }

        $tenant->update(['onboarding_dismissed_at' => now()]);
        $this->dispatch('notify', type: 'success', message: 'Onboarding checklist hidden. You can still complete setup anytime.');
    }

    public function markOnboardingComplete(): void
    {
        $tenant = Auth::user()->tenant;
        if (! $tenant) {
            return;
        }
        if (! Schema::hasColumn('tenants', 'onboarding_completed_at')) {
            $this->dispatch('notify', type: 'error', message: 'Onboarding requires latest migrations.');

            return;
        }

        $tenant->update([
            'onboarding_completed_at' => now(),
            'onboarding_dismissed_at' => null,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Onboarding marked as complete.');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        /** @var Tenant|null $tenant */
        $tenant = Auth::user()->tenant;

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
            ->with('user')
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
            $dateKey = $row->date instanceof Carbon ? $row->date->toDateString() : (string) $row->date;
            $attendanceMatrix[$dateKey][$row->status] = (int) $row->total;
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

        $profileCompleted = (bool) ($tenant && filled($tenant->name) && filled($tenant->phone) && filled($tenant->address));
        $hoursCompleted = (bool) ($tenant && is_array($tenant->operating_hours) && count($tenant->operating_hours) > 0);
        $planCompleted = LibraryPlan::query()->where('tenant_id', $tenantId)->where('is_active', true)->exists();
        $studentCompleted = StudentMembership::query()->where('tenant_id', $tenantId)->where('status', 'active')->exists();
        $seatCompleted = Seat::query()->where('tenant_id', $tenantId)->exists();

        $onboardingSteps = [
            [
                'label' => 'Complete library profile',
                'done' => $profileCompleted,
                'route' => route('library.settings'),
            ],
            [
                'label' => 'Set operating hours',
                'done' => $hoursCompleted,
                'route' => route('library.settings'),
            ],
            [
                'label' => 'Create at least one plan',
                'done' => $planCompleted,
                'route' => route('library.plans'),
            ],
            [
                'label' => 'Add first student',
                'done' => $studentCompleted,
                'route' => route('library.students'),
            ],
            [
                'label' => 'Configure seats',
                'done' => $seatCompleted,
                'route' => route('library.seats'),
            ],
        ];

        $completedCount = collect($onboardingSteps)->where('done', true)->count();
        $onboardingProgress = (int) round(($completedCount / max(count($onboardingSteps), 1)) * 100);
        $hasOnboardingColumns = Schema::hasColumn('tenants', 'onboarding_completed_at') && Schema::hasColumn('tenants', 'onboarding_dismissed_at');
        $onboardingComplete = $hasOnboardingColumns ? ($onboardingProgress === 100 || ! empty($tenant?->onboarding_completed_at)) : true;
        $onboardingDismissed = $hasOnboardingColumns ? ! empty($tenant?->onboarding_dismissed_at) : false;

        return view('livewire.library.dashboard', [
            'activeStudents' => $activeStudents,
            'totalSeats' => $totalSeats,
            'occupiedSeats' => $occupiedSeats,
            'vacantSeats' => $vacantSeats,
            'revenueToday' => $revenueToday,
            'recentPayments' => $recentPayments,
            'chartData' => $chartData,
            'onboardingSteps' => $onboardingSteps,
            'onboardingProgress' => $onboardingProgress,
            'onboardingComplete' => $onboardingComplete,
            'onboardingDismissed' => $onboardingDismissed,
        ])->layout('layouts.app', [
            'header' => 'Library Overview',
        ]);
    }
}
