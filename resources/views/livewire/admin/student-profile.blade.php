<div class="py-10 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div>
            <a href="{{ route('admin.students') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">&larr; Back to Students</a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
            <p class="text-sm text-gray-500">Student profile and cross-library activity overview.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Library Memberships</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['memberships'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Active Subscriptions</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Total Paid</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $global_currency }}{{ number_format($stats['total_payments'], 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Present Days (30d)</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['present_days_30'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-900">Profile Details</h2></div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Email</p><p class="mt-1 font-medium text-gray-900">{{ $student->email ?: 'Not set' }}</p></div>
                    <div><p class="text-gray-500">Phone</p><p class="mt-1 font-medium text-gray-900">{{ $student->phone ?: 'Not set' }}</p></div>
                    <div><p class="text-gray-500">Primary Tenant</p><p class="mt-1 font-medium text-gray-900">{{ $student->tenant?->name ?: 'Not set' }}</p></div>
                    <div><p class="text-gray-500">Joined</p><p class="mt-1 font-medium text-gray-900">{{ $student->created_at?->format('M d, Y') }}</p></div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Memberships</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse($student->memberships as $m)
                                <span class="inline-flex items-center rounded-full bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">
                                    {{ $m->tenant?->name ?? 'Unknown' }} ({{ $m->status }})
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">No memberships.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-900">Recent Subscriptions</h2></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tenant</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Plan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Seat</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Period</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($subscriptions as $row)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $row->tenant?->name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $row->plan?->name ?? 'N/A' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $row->seat?->name ?? 'Unassigned' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $row->start_date?->format('M d, Y') }} - {{ $row->end_date?->format('M d, Y') }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ ucfirst($row->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-sm text-gray-500">No subscriptions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-900">Recent Payments</h2></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tenant</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($payments as $row)
                                <tr>
                                    <td class="px-5 py-3 text-sm text-gray-900">{{ $row->tenant?->name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700">{{ $global_currency }}{{ number_format((float)$row->amount, 0) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700">{{ ucfirst($row->status) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700">{{ \Illuminate\Support\Carbon::parse($row->payment_date)->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-6 text-sm text-gray-500">No payments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-900">Recent Leaves</h2></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tenant</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dates</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($leaves as $row)
                                <tr>
                                    <td class="px-5 py-3 text-sm text-gray-900">{{ $row->tenant?->name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700">{{ $row->start_date?->format('M d') }} - {{ $row->end_date?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700">{{ ucfirst($row->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-6 text-sm text-gray-500">No leaves found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

