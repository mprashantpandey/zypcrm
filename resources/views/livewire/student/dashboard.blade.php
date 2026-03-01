<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900">Welcome, {{ $student->name }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ $activeTenant?->name ? 'Library: ' . $activeTenant->name : 'Your student portal overview' }}
        </p>
        @if($memberships->count() > 1)
        <div class="mt-4 max-w-sm">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Switch Library Context</label>
            <select wire:model.live="activeTenantId" wire:change="switchTenantContext($event.target.value)"
                class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($memberships as $membership)
                <option value="{{ $membership->tenant_id }}">{{ $membership->tenant?->name ?? 'Unknown Library' }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="mt-4 flex flex-wrap gap-2">
            <span class="inline-flex items-center rounded-full bg-indigo-50 text-indigo-700 px-3 py-1 text-xs font-semibold">
                Plan: {{ optional($activeSubscription?->plan)->name ?? 'Not Assigned' }}
            </span>
            <span class="inline-flex items-center rounded-full bg-sky-50 text-sky-700 px-3 py-1 text-xs font-semibold">
                Seat: {{ $activeSeat?->name ?? 'Unassigned' }}
            </span>
            <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-semibold">
                Today: {{ ucfirst($todayAttendance?->status ?? 'not marked') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attendance Rate</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $attendanceRate }}%</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Present (This Month)</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $presentCount }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Absent / Leave</p>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ $absentCount + $leaveCount }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pending / Overdue Fees</p>
            <p class="mt-2 text-2xl font-bold text-rose-600">{{ $global_currency }}{{ number_format($feesDue, 2) }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-900">Recent Fee Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Method</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($recentFeePayments as $payment)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $global_currency }}{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($payment->status === 'overdue' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $payment->payment_method ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No fee records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
