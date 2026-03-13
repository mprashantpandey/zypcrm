<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Reports & Analytics</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor your platform's growth, revenue, and overall usage metrics.
            </p>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Metric Card 1 -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div> Total Libraries
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $totalTenants }}</dd>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">All onboarded tenant accounts</span>
                </div>
            </div>

            <!-- Metric Card 2 -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div> Active Subscriptions
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $activeTenants }}</dd>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">Based on accounts</span>
                </div>
            </div>

            <!-- Metric Card 3 -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15.21 12.236a5.5 5.5 0 11-7.42 0 4.5 4.5 0 017.42 0zM21 15v.5a9.5 9.5 0 01-9 9.5 9.5 0 01-9-9.5V15" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div> End-Users (Students)
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $totalStudents }}</dd>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">Unique student accounts across libraries</span>
                </div>
            </div>

            <!-- Metric Card 4 -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div> Monthly Recurring (MRR)
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $global_currency }}{{ number_format($mrr, 2) }}</dd>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">Estimated current run rate</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <dt class="text-sm font-medium text-gray-500">Total Fees Collected (Paid)</dt>
                <dd class="mt-2 text-3xl font-bold text-emerald-600">{{ $global_currency }}{{ number_format($totalRevenueCollected, 2) }}</dd>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <dt class="text-sm font-medium text-gray-500">Outstanding Fees (Pending + Overdue)</dt>
                <dd class="mt-2 text-3xl font-bold text-amber-600">{{ $global_currency }}{{ number_format($pendingRevenue, 2) }}</dd>
            </div>
        </div>

        <!-- Charts / Extended Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 lg:col-span-2 p-6 h-96">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Revenue Trend (Last 12 Months)</h3>
                    <div class="flex flex-col items-end">
                        <span class="text-xs text-gray-500">Subscriptions + Fees</span>
                        <span class="mt-1 text-[11px] text-gray-400">
                            Note: Subscription lines use plan prices (estimated), not invoice totals.
                        </span>
                    </div>
                </div>
                <canvas id="adminReportsRevenueChart" height="120"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight uppercase">Recent Subscriptions</h3>
                </div>
                <div class="p-0 flex-1 overflow-y-auto">
                    @forelse($recentSubscriptions as $sub)
                    <div
                        class="px-6 py-4 border-b border-gray-50 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                {{ substr(optional($sub->tenant)->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ optional($sub->tenant)->name ?? 'Unknown
                                    Library' }}</p>
                                <p class="text-xs text-gray-500">{{ optional($sub->plan)->name ?? 'Custom Plan' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sub->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ $sub->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 flex flex-col items-center justify-center text-center h-full">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm text-gray-500">No new subscriptions this week.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 tracking-tight uppercase">Expiring Student Subscriptions (Next 7 Days)</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentExpiringSubscriptions as $sub)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $sub->user?->name ?? 'Unknown Student' }}</p>
                        <p class="text-xs text-gray-500">{{ $sub->tenant?->name ?? 'Unknown Library' }} · {{ $sub->plan?->name ?? 'Plan' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-amber-700">{{ \Carbon\Carbon::parse($sub->end_date)->format('M d, Y') }}</span>
                </div>
                @empty
                <p class="px-6 py-6 text-sm text-gray-500">No student subscriptions expiring in next 7 days.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script type="application/json" id="admin-reports-chart-data">@json($revenueChart)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const renderAdminReportsChart = () => {
        const tag = document.getElementById('admin-reports-chart-data');
        const canvas = document.getElementById('adminReportsRevenueChart');
        if (!tag || !canvas) return;

        const chartData = JSON.parse(tag.textContent);
        const ctx = canvas.getContext('2d');

        if (window.__adminReportsRevenueChart) {
            window.__adminReportsRevenueChart.destroy();
        }

        window.__adminReportsRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Subscriptions',
                        data: chartData.subscriptionRevenue,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.15)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Fees Collected',
                        data: chartData.feesCollected,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.12)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Total',
                        data: chartData.total,
                        borderColor: '#0f172a',
                        backgroundColor: 'rgba(15, 23, 42, 0.08)',
                        fill: false,
                        borderWidth: 2,
                        tension: 0.35,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: { y: { beginAtZero: true } },
            },
        });
    };

    document.addEventListener('DOMContentLoaded', renderAdminReportsChart);
    document.addEventListener('livewire:navigated', renderAdminReportsChart);
</script>
