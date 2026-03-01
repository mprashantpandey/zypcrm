<x-slot name="header">
    <div class="flex items-center">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Overview') }}
        </h2>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Welcome Header -->
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Good morning, {{ auth()->user()->name }}</h3>
            <p class="text-gray-500 mt-1">Here's what's happening with your SaaS platform today.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Quick Link Card: Subscriptions -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:border-gray-300 transition-colors group">
                <div class="p-6">
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center mb-5 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-indigo-600 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-base font-semibold text-gray-900 mb-1">Subscription Plans</h4>
                    <p class="text-sm text-gray-500 mb-6 line-clamp-2">Manage pricing tiers, subscription packages, and
                        limits for Library Owners.</p>

                    <a href="{{ route('admin.plans') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                        Manage plans
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Live Card: Analytics -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative group hover:border-indigo-300 transition-colors">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ App\Models\Setting::getCurrencySymbol() }}{{
                            number_format($mrr, 2) }}</span>
                    </div>
                    <h4 class="text-base font-semibold text-gray-900 mb-1">Monthly Recurring Revenue</h4>
                    <p class="text-sm text-gray-500 mb-6">Total committed revenue from active library subscriptions.</p>

                    <a href="{{ route('admin.reports') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                        View financial reports
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Live Card: Tenants -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative group hover:border-indigo-300 transition-colors">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-gray-900">{{ $activeTenants }}</span>
                            <span class="text-sm font-medium text-gray-500">/ {{ $totalTenants }} active</span>
                        </div>
                    </div>
                    <h4 class="text-base font-semibold text-gray-900 mb-1">Registered Libraries</h4>
                    <p class="text-sm text-gray-500 mb-6">Serving <span class="font-medium text-gray-900">{{
                            $totalStudents }}</span> total students across all tenants.</p>

                    <a href="{{ route('admin.tenants') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                        Manage libraries
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Revenue Growth Chart</h4>
                        <p class="text-sm text-gray-500">Historical platform revenue (last 12 months)</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">MoM Growth</div>
                        <div
                            class="text-sm font-semibold {{ $revenueChart['growth'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $revenueChart['growth'] >= 0 ? '+' : '' }}{{ number_format($revenueChart['growth'], 2)
                            }}%
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <canvas id="adminRevenueGrowthChart" height="110"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="admin-revenue-chart-data">@json($revenueChart)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const renderRevenueChart = () => {
            const dataTag = document.getElementById('admin-revenue-chart-data');
            const canvas = document.getElementById('adminRevenueGrowthChart');
            if (!dataTag || !canvas || !window.Chart) return;

            const data = JSON.parse(dataTag.textContent);
            const currency = @json($global_currency);

            if (window.__adminRevenueChart) {
                window.__adminRevenueChart.destroy();
            }

            window.__adminRevenueChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: data.series,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.14)',
                        fill: true,
                        tension: 0.32,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${currency}${Number(ctx.raw).toFixed(2)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => `${currency}${value}`,
                            },
                        },
                    },
                },
            });
        };

        document.addEventListener('DOMContentLoaded', renderRevenueChart);
        document.addEventListener('livewire:navigated', renderRevenueChart);
    })();
</script>
