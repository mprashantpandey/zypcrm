<?php

namespace App\Livewire\Admin;

use App\Models\FeePayment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;

#[Layout('layouts.app')]
class Reports extends Component
{
    public function render()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalStudents = User::where('role', 'student')->count();

        $totalRevenueCollected = FeePayment::where('status', 'paid')->sum('amount');
        $pendingRevenue = FeePayment::whereIn('status', ['pending', 'overdue'])->sum('amount');

        // MRR estimation based on active monthly subscriptions
        $mrr = Subscription::where('status', 'active')
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('subscription_plans.billing_cycle', 'monthly')
            ->sum('subscription_plans.price');

        $months = collect(range(11, 0))->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset))
            ->push(now()->startOfMonth());

        $subscriptionRevenueByMonth = Subscription::query()
            ->join('subscription_plans', 'subscription_plans.id', '=', 'subscriptions.subscription_plan_id')
            ->whereBetween('subscriptions.created_at', [
                now()->startOfMonth()->subMonths(11),
                now()->endOfMonth(),
            ])
            ->selectRaw('DATE_FORMAT(subscriptions.created_at, "%Y-%m") as ym, SUM(subscription_plans.price) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $collectedFeesByMonth = FeePayment::query()
            ->where('status', 'paid')
            ->whereBetween('payment_date', [
                now()->startOfMonth()->subMonths(11)->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $chartLabels = $months->map(fn (Carbon $month) => $month->format('M Y'))->toArray();
        $subscriptionSeries = $months->map(fn (Carbon $month) => (float) ($subscriptionRevenueByMonth[$month->format('Y-m')] ?? 0))->toArray();
        $feesSeries = $months->map(fn (Carbon $month) => (float) ($collectedFeesByMonth[$month->format('Y-m')] ?? 0))->toArray();
        $combinedSeries = $months->map(function (Carbon $month) use ($subscriptionRevenueByMonth, $collectedFeesByMonth) {
            $ym = $month->format('Y-m');

            return (float) (($subscriptionRevenueByMonth[$ym] ?? 0) + ($collectedFeesByMonth[$ym] ?? 0));
        })->toArray();

        $recentExpiringSubscriptions = \App\Models\StudentSubscription::with(['tenant', 'plan', 'user'])
            ->where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('end_date')
            ->take(8)
            ->get();

        $recentSubscriptions = Subscription::with(['tenant', 'plan'])->latest()->take(5)->get();

        return view('livewire.admin.reports', [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'totalStudents' => $totalStudents,
            'totalRevenueCollected' => $totalRevenueCollected,
            'pendingRevenue' => $pendingRevenue,
            'mrr' => $mrr,
            'recentSubscriptions' => $recentSubscriptions,
            'recentExpiringSubscriptions' => $recentExpiringSubscriptions,
            'revenueChart' => [
                'labels' => $chartLabels,
                'subscriptionRevenue' => $subscriptionSeries,
                'feesCollected' => $feesSeries,
                'total' => $combinedSeries,
            ],
        ]);
    }
}
