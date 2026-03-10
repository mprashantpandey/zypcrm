<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            data-tour="dashboard.header">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Library Overview</h1>
                <p class="mt-1 text-sm text-gray-500">Monitor your library's seating, students, and fee collections.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" x-on:click="window.dispatchEvent(new CustomEvent('start-tour', { detail: { title: 'Library Dashboard Tour', steps: [
                        'Finish setup from the onboarding checklist to unlock smooth operations.',
                        'Track students, seats, and revenue from top KPI cards.',
                        'Review weekly trends and attendance from dashboard charts.',
                        'Use quick actions for student onboarding and fee collection.'
                    ] } }))"
                    class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                    Quick Tour
                </button>
                <a href="{{ route('library.students') }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Add Student
                </a>
                <a href="{{ route('library.fees') }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Collect Fee
                </a>
            </div>
        </div>

        @if(!$onboardingComplete && !$onboardingDismissed)
        <div class="mb-8 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-5" data-tour="dashboard.onboarding">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">First-time Setup Wizard</p>
                    <h2 class="mt-1 text-lg font-bold text-indigo-900">Complete onboarding for your library</h2>
                    <p class="mt-1 text-sm text-indigo-800">Progress: {{ $onboardingProgress }}% complete</p>
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-indigo-200">
                        <div class="h-full rounded-full bg-indigo-600" style="width: {{ $onboardingProgress }}%"></div>
                    </div>
                    <ul class="mt-4 space-y-2 text-sm text-indigo-900">
                        @foreach($onboardingSteps as $step)
                        <li class="flex items-center justify-between gap-3 rounded-lg bg-white/80 px-3 py-2">
                            <span class="flex items-center gap-2">
                                @if($step['done'])
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                @else
                                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                </svg>
                                @endif
                                {{ $step['label'] }}
                            </span>
                            @if(!$step['done'])
                            <a href="{{ $step['route'] }}" wire:navigate
                                class="text-xs font-semibold text-indigo-700 hover:text-indigo-900">Open</a>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex gap-2">
                    <button wire:click="dismissOnboarding" type="button"
                        class="rounded-lg border border-indigo-300 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                        Remind Later
                    </button>
                    <button wire:click="markOnboardingComplete" type="button"
                        class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">
                        Mark Complete
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-tour="dashboard.kpis">
            <!-- Metric Card 1: Active Students -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div> Active Students
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $activeStudents }}</dd>
            </div>

            <!-- Metric Card 2: Total Seats -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div> Total Seats
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $totalSeats }}</dd>
            </div>

            <!-- Metric Card 3: Vacant Seats -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div> Vacant Seats
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $vacantSeats }}</dd>
            </div>

            <!-- Metric Card 4: Revenue Today -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <dt class="text-sm font-medium text-gray-500 truncate flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div> Revenue Today
                </dt>
                <dd class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">{{ $global_currency }}{{
                    number_format($revenueToday, 2) }}
                </dd>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Trends -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 lg:col-span-2 p-6">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="rounded-lg border border-gray-100 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Weekly Revenue</h3>
                        <canvas id="weeklyRevenueChart" height="190"></canvas>
                    </div>
                    <div class="rounded-lg border border-gray-100 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Seat Utilization Trend (%)</h3>
                        <canvas id="seatUtilizationChart" height="190"></canvas>
                    </div>
                    <div class="rounded-lg border border-gray-100 p-4 xl:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Attendance Trend</h3>
                        <canvas id="attendanceTrendChart" height="190"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 tracking-tight uppercase">Recent Payments</h3>
                </div>
                <div class="p-0 flex-1 overflow-y-auto">
                    @forelse($recentPayments as $payment)
                    <div
                        class="px-6 py-4 border-b border-gray-50 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 font-bold text-sm">
                                {{ substr(optional($payment->user)->name ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ optional($payment->user)->name ??
                                    'Unknown Student' }}</p>
                                <p class="text-xs text-gray-500">{{ $global_currency }}{{
                                    number_format($payment->amount, 2) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">
                                Paid
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{
                                \Carbon\Carbon::parse($payment->payment_date)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 flex flex-col items-center justify-center text-center h-full">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-sm text-gray-500">No recent fee collections.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script type="application/json" id="dashboard-chart-data">@json($chartData)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const renderCharts = () => {
            const dataTag = document.getElementById('dashboard-chart-data');
            if (!dataTag || !window.Chart) return;

            const chartData = JSON.parse(dataTag.textContent);
            const labels = chartData.labels || [];
            const currency = @json($global_currency);

            window.__libraryDashboardCharts = window.__libraryDashboardCharts || [];
            window.__libraryDashboardCharts.forEach((chart) => chart.destroy());
            window.__libraryDashboardCharts = [];

            const revenueCtx = document.getElementById('weeklyRevenueChart');
            const seatCtx = document.getElementById('seatUtilizationChart');
            const attendanceCtx = document.getElementById('attendanceTrendChart');
            if (!revenueCtx || !seatCtx || !attendanceCtx) return;

            window.__libraryDashboardCharts.push(new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue',
                        data: chartData.weeklyRevenue,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.15)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${currency}${Number(ctx.raw).toFixed(2)}`
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            }));

            window.__libraryDashboardCharts.push(new Chart(seatCtx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Utilization %',
                        data: chartData.seatUtilization,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            }));

            window.__libraryDashboardCharts.push(new Chart(attendanceCtx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        { label: 'Present', data: chartData.attendancePresent, backgroundColor: '#16a34a' },
                        { label: 'Absent', data: chartData.attendanceAbsent, backgroundColor: '#dc2626' },
                        { label: 'Leave', data: chartData.attendanceLeave, backgroundColor: '#ca8a04' }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true } }
                }
            }));
        };

        document.addEventListener('DOMContentLoaded', renderCharts);
        document.addEventListener('livewire:navigated', renderCharts);
    })();
</script>