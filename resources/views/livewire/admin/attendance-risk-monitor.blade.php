<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Attendance Risk Monitor</h1>
            <p class="mt-1 text-sm text-slate-500">Monitor anomalies, suspicious operators, and missed clockout trends across libraries.</p>
        </div>

        @if(!$hasLogs)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                `attendance_action_logs` table not found. Run migrations to enable anomaly analytics.
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">From Date</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">To Date</label>
                    <input type="date" wire:model.live="dateTo" class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tenant</label>
                    <select wire:model.live="tenantFilter" class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Libraries</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="clearFilters" type="button"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Actions</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($riskSummary['total_logs']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Failed Actions</p>
                <p class="mt-1 text-2xl font-bold text-rose-600">{{ number_format($riskSummary['failed_actions']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Anomaly Actions</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ number_format($riskSummary['anomaly_actions']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Missed Clockouts</p>
                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($riskSummary['missed_clockouts']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Top Anomalies</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Failed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($topAnomalies as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ str_replace('_', ' ', ucfirst($row->action)) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $row->total }}</td>
                                    <td class="px-4 py-3 text-sm text-rose-600">{{ $row->failed }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">No anomaly actions in selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Suspicious Operators</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Operator</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Failed</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Anomaly</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($suspiciousOperators as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $row->operator_name }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $row->total_actions }}</td>
                                    <td class="px-4 py-3 text-sm text-rose-600">{{ $row->failed_actions }}</td>
                                    <td class="px-4 py-3 text-sm text-amber-600">{{ $row->anomaly_actions }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No suspicious operator activity detected.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Missed Clockout Trend</h2>
            @if($hasAttendance)
                <div class="mt-4">
                    <canvas id="missedClockoutTrendChart" height="120"></canvas>
                </div>
            @else
                <p class="mt-3 text-sm text-slate-500">Attendance table unavailable.</p>
            @endif
        </div>
    </div>
</div>

<script type="application/json" id="attendance-risk-chart-data">@json($missedClockoutTrend)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const render = () => {
            const dataTag = document.getElementById('attendance-risk-chart-data');
            const canvas = document.getElementById('missedClockoutTrendChart');
            if (!dataTag || !canvas || !window.Chart) return;

            const parsed = JSON.parse(dataTag.textContent || '{}');
            const labels = parsed.labels || [];
            const series = parsed.series || [];

            if (window.__attendanceRiskChart) {
                window.__attendanceRiskChart.destroy();
            }

            window.__attendanceRiskChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Missed Clockouts',
                        data: series,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.15)',
                        fill: true,
                        tension: 0.35,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
            });
        };

        document.addEventListener('DOMContentLoaded', render);
        document.addEventListener('livewire:navigated', render);
    })();
</script>
