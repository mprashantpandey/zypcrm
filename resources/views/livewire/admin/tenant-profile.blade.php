<div class="py-10 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session()->has('message'))
            <div class="rounded-lg bg-green-50 p-4 border border-green-100 text-sm text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.tenants') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">&larr; Back to Tenants</a>
                <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                <p class="text-sm text-gray-500">Library profile and operational details.</p>
            </div>
            <button wire:click="toggleStatus"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-white {{ $tenant->status === 'active' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                {{ $tenant->status === 'active' ? 'Suspend Library' : 'Activate Library' }}
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Students</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['students'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Membership Links</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['memberships'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Active Subscriptions</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-xs uppercase tracking-wide text-gray-500">Paid This Month</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $global_currency }}{{ number_format($stats['monthly_revenue'], 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Library Details</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="mt-1 font-medium text-gray-900 capitalize">{{ $tenant->status }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Public Page</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $tenant->public_page_enabled ? 'Enabled' : 'Disabled' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $tenant->email ?: 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Phone</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $tenant->phone ?: 'Not set' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Address</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $tenant->address ?: 'Not set' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Public URL</p>
                        @if($tenant->public_slug)
                            <a href="{{ route('public.library', $tenant->public_slug) }}" target="_blank" class="mt-1 inline-flex text-indigo-600 hover:text-indigo-700 font-medium">
                                {{ route('public.library', $tenant->public_slug) }}
                            </a>
                        @else
                            <p class="mt-1 font-medium text-gray-900">Not configured</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Library Owner(s)</h2>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($tenant->users as $owner)
                        <div class="rounded-lg border border-gray-100 p-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $owner->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $owner->email }}</p>
                            <p class="text-xs text-gray-500">{{ $owner->phone ?: 'No phone' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No owner assigned.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Manage Subscription</h2>
                    @if($tenant->currentSubscription)
                        <span class="inline-flex px-2 py-1 rounded-md text-xs font-medium {{ $tenant->currentSubscription->status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($tenant->currentSubscription->status === 'past_due' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($tenant->currentSubscription->status) }}
                        </span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="mb-4 text-sm text-gray-600">
                        Current:
                        <span class="font-medium text-gray-900">{{ $tenant->currentSubscription->plan->name ?? 'No active plan' }}</span>
                        @if($tenant->currentSubscription?->ends_at)
                            <span class="text-gray-500">• expires {{ $tenant->currentSubscription->ends_at->format('M d, Y') }}</span>
                        @endif
                    </div>

                    <form wire:submit="saveSubscription" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Plan</label>
                            <select wire:model="subscriptionPlanId" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select plan</option>
                                @foreach($platformPlans as $plan)
                                    <option value="{{ $plan['id'] }}">{{ $plan['name'] }} ({{ $global_currency }}{{ number_format($plan['price'], 0) }}/{{ $plan['billing_cycle'] }})</option>
                                @endforeach
                            </select>
                            @error('subscriptionPlanId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Status</label>
                            <select wire:model="subscriptionStatus" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active">Active</option>
                                <option value="past_due">Past Due</option>
                                <option value="canceled">Canceled</option>
                            </select>
                            @error('subscriptionStatus') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Expiry Date</label>
                            <input type="date" wire:model="subscriptionEndsAt" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('subscriptionEndsAt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-3 flex flex-wrap items-center gap-3 pt-1">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Save Subscription
                            </button>
                            <button type="button" wire:click="extendSubscription(30)" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200">
                                Extend +30 days
                            </button>
                            <button type="button" wire:click="extendSubscription(90)" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200">
                                Extend +90 days
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Recent Subscription History</h2>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($subscriptionHistory as $entry)
                        <div class="rounded-lg border border-gray-100 p-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $entry->plan->name ?? 'Unknown plan' }}</p>
                            <p class="mt-1 text-xs text-gray-500">Status: {{ ucfirst($entry->status) }}</p>
                            <p class="text-xs text-gray-500">Ends: {{ $entry->ends_at?->format('M d, Y') ?? 'Not set' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Updated {{ $entry->updated_at?->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No subscription records found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Create Invoice / Collect Payment</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Amount</label>
                        <input type="number" step="0.01" min="1" wire:model="invoiceAmount"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('invoiceAmount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Currency</label>
                        <input type="text" wire:model="invoiceCurrencyCode"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('invoiceCurrencyCode') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Due Date</label>
                        <input type="date" wire:model="invoiceDueDate"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('invoiceDueDate') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Method</label>
                        <select wire:model="invoicePaymentMethod"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="stripe">Stripe</option>
                            <option value="razorpay">Razorpay</option>
                            <option value="manual">Manual</option>
                        </select>
                        @error('invoicePaymentMethod') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Notes</label>
                        <input type="text" wire:model="invoiceNotes" placeholder="Optional notes"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('invoiceNotes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button type="button" wire:click="createInvoice"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200">
                        Create Invoice
                    </button>
                    <button type="button" wire:click="collectPaymentNow"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                        Collect Payment Now
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Invoice & Payment History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Invoice</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Plan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Due</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Method</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($invoiceHistory as $invoice)
                            @php
                                $isOverdue = $invoice->status === 'pending' && $invoice->due_date && $invoice->due_date->isPast();
                            @endphp
                            <tr>
                                <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $invoice->invoice_no }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $invoice->plan->name ?? 'N/A' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $invoice->currency_code }} {{ number_format((float) $invoice->amount, 2) }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $invoice->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-5 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 rounded-md text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : ($isOverdue ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')) }}">
                                        {{ $isOverdue ? 'Overdue' : ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $invoice->payment_method ? ucfirst(str_replace('_', ' ', $invoice->payment_method)) : 'N/A' }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.tenants.invoices.pdf', ['tenant' => $tenant, 'invoice' => $invoice]) }}"
                                            target="_blank"
                                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200">
                                            PDF
                                        </a>

                                        <form method="POST" action="{{ route('admin.tenants.invoices.email', ['tenant' => $tenant, 'invoice' => $invoice]) }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200">
                                                Email
                                            </button>
                                        </form>

                                        @if($invoice->status !== 'paid')
                                            <button type="button" wire:click="markInvoicePaid({{ $invoice->id }})"
                                                class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200">
                                                Mark Paid
                                            </button>
                                            <button type="button" wire:click="cancelInvoice({{ $invoice->id }})"
                                                class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300">
                                                Cancel
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-500">Paid {{ $invoice->paid_at?->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                    @if($invoice->receipt_emailed_at)
                                        <p class="mt-1 text-xs text-gray-500">Emailed {{ $invoice->receipt_emailed_at->diffForHumans() }} ({{ $invoice->receipt_email_attempts }} attempt{{ $invoice->receipt_email_attempts == 1 ? '' : 's' }})</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-6 text-sm text-gray-500">No invoices created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Published Library Plans</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Plan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Duration</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Timings</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($tenant->libraryPlans as $plan)
                            <tr>
                                <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $plan->name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $global_currency }}{{ number_format((float) $plan->price, 0) }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ $plan->duration_days }} days</td>
                                <td class="px-5 py-3 text-sm text-gray-700">
                                    {{ $plan->start_time ? \Carbon\Carbon::parse($plan->start_time)->format('h:i A') : 'Flexible' }}
                                    -
                                    {{ $plan->end_time ? \Carbon\Carbon::parse($plan->end_time)->format('h:i A') : 'Anytime' }}
                                </td>
                                <td class="px-5 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 rounded-md text-xs font-medium {{ $plan->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-sm text-gray-500">No plans created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
