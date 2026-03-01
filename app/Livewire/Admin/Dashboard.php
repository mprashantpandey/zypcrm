<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where([['status', '=', 'active']])->count();

        $totalStudents = User::where([['role', '=', 'student']])->count();

        $mrr = DB::table('subscription_plans')
            ->join('subscriptions', 'subscription_plans.id', '=', 'subscriptions.subscription_plan_id')
            ->where('subscriptions.status', '=', 'active')
            ->sum('subscription_plans.price');

        $recentTenants = Tenant::with('users')
            ->latest()
            ->take(5)
            ->get();

        $months = collect(range(11, 0))->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset))
            ->push(now()->startOfMonth());

        $monthlyRevenueRaw = DB::table('subscriptions')
            ->join('subscription_plans', 'subscription_plans.id', '=', 'subscriptions.subscription_plan_id')
            ->selectRaw('DATE_FORMAT(subscriptions.created_at, "%Y-%m") as ym, SUM(subscription_plans.price) as total')
            ->whereBetween('subscriptions.created_at', [
                now()->startOfMonth()->subMonths(11),
                now()->endOfMonth(),
            ])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $revenueLabels = $months->map(fn ($month) => $month->format('M Y'))->toArray();
        $revenueSeries = $months->map(fn ($month) => (float) ($monthlyRevenueRaw[$month->format('Y-m')] ?? 0))->toArray();

        $currentMonthRevenue = (float) end($revenueSeries);
        $previousMonthRevenue = (float) ($revenueSeries[count($revenueSeries) - 2] ?? 0);
        $momGrowth = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2)
            : ($currentMonthRevenue > 0 ? 100.0 : 0.0);

        $revenueChart = [
            'labels' => $revenueLabels,
            'series' => $revenueSeries,
            'current' => $currentMonthRevenue,
            'previous' => $previousMonthRevenue,
            'growth' => $momGrowth,
        ];

        return view('livewire.admin.dashboard', [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'totalStudents' => $totalStudents,
            'mrr' => $mrr,
            'recentTenants' => $recentTenants,
            'revenueChart' => $revenueChart,
        ]);
    }
}
