<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Fee History</h2>
                <p class="mt-1 text-sm text-gray-500">View paid, pending, and overdue fee transactions.</p>
                @if($activeTenant)
                <p class="mt-1 text-xs font-medium text-indigo-600">Library: {{ $activeTenant->name }}</p>
                @endif
            </div>
            <div class="w-full sm:w-56">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Filter Status</label>
                <select wire:model.live="status"
                    class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Transaction</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($payments as $payment)
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
                        <td class="px-6 py-3 text-sm text-gray-500">{{ $payment->transaction_id ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No fee payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-6 py-4">
            {{ $payments->links() }}
        </div>
    </div>
</div>
