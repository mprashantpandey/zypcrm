<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Fee Collection</h1>
                <p class="mt-1 text-sm text-gray-500">Record payments, track outstanding fees, and monitor monthly
                    revenue.</p>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                    Strict Invoicing Active
                </span>
                <button wire:click="exportCSV"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="-ml-0.5 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </button>
                <button wire:click="openModal"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                    <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Invoice
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
            <div
                class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm border border-green-100 sm:p-6 relative">
                <div class="absolute right-0 top-0 w-2 h-full bg-green-500"></div>
                <dt class="truncate text-sm font-medium text-green-600 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    Total Collected
                </dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $global_currency }}{{
                    number_format($stats['total_collected'], 2) }}</dd>
            </div>

            <div
                class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm border border-indigo-100 sm:p-6 relative">
                <div class="absolute right-0 top-0 w-2 h-full bg-indigo-500"></div>
                <dt class="truncate text-sm font-medium text-indigo-600 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                    </svg>
                    Collected This Month
                </dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $global_currency }}{{
                    number_format($stats['this_month'],
                    2) }}</dd>
            </div>

            <div class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm border border-red-100 sm:p-6 relative">
                <div class="absolute right-0 top-0 w-2 h-full bg-red-500"></div>
                <dt class="truncate text-sm font-medium text-red-600 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Pending / Overdue
                </dt>
                <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $global_currency }}{{
                    number_format($stats['pending_amount'], 2) }}</dd>
            </div>
        </dl>

        <!-- Search + Filters -->
        <div class="mb-6 grid grid-cols-1 lg:grid-cols-5 gap-3 items-end">
            <div class="lg:col-span-2">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input wire:model.live="search" type="text"
                        class="block w-full rounded-lg border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Search by student name or phone...">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select wire:model.live="filterStatus"
                    class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="all">All Status</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                <input type="date" wire:model.live="fromDate"
                    class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            <div class="flex items-end gap-2">
                <div class="w-full">
                    <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                    <input type="date" wire:model.live="toDate"
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <button wire:click="clearFilters"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Clear
                </button>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow-sm ring-1 ring-black ring-opacity-5 rounded-xl bg-white">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Student</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Amount</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Date</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Method</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Remarks</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($payments as $payment)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-600 font-medium text-sm">{{
                                                        substr($payment->user->name ?? '?', 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900">{{ $payment->user->name ??
                                                    'Unknown' }}</div>
                                                <div class="text-gray-500">{{ $payment->user->phone ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                        {{ $global_currency }}{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if($payment->status === 'paid')
                                        <span
                                            class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Paid</span>
                                        @elseif($payment->status === 'pending')
                                        <span
                                            class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Pending</span>
                                        @else
                                        <span
                                            class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">Overdue</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="capitalize">{{ $payment->payment_method }}</span>
                                        @if($payment->transaction_id)
                                        <div class="text-xs text-gray-400">Ref: {{ $payment->transaction_id }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $payment->remarks ?: '-' }}
                                    </td>
                                    <td
                                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end gap-3">
                                            @if($payment->status !== 'paid')
                                            <div x-data="{ copied: false, link: '{{ route('public.pay', $payment->slug) }}' }"
                                                class="relative flex items-center">
                                                <button
                                                    @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="text-indigo-600 hover:text-indigo-900 focus:outline-none"
                                                    title="Copy Payment Link">
                                                    <span x-show="!copied">Copy Link</span>
                                                    <span x-show="copied" class="text-green-600"
                                                        style="display: none;">Copied!</span>
                                                </button>
                                            </div>
                                            @endif
                                            <button wire:click="edit({{ $payment->id }})"
                                                class="text-gray-600 hover:text-indigo-900">Edit</button>
                                            <button wire:click="delete({{ $payment->id }})"
                                                wire:confirm="Are you sure you want to delete this payment record?"
                                                class="text-red-600 hover:text-red-900">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
                                        <p class="mt-1 text-sm text-gray-500">Get started by recording a new student
                                            payment.</p>
                                        <div class="mt-6">
                                            <p class="text-xs text-gray-500 italic">Invoices are automatically generated
                                                when you assign a Library Plan to a student in the Students tab.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
        @endif

        <!-- Add/Edit Payment Modal -->
        <div x-data="{ open: @entangle('isModalOpen') }" x-show="open" style="display: none;" class="relative z-50"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="open" @click.away="open = false; $wire.closeModal()"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md sm:p-6 text-left">

                        <div class="absolute right-0 top-0 pr-4 pt-4">
                            <button type="button" wire:click="closeModal"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 transition-colors">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="sm:flex sm:items-start text-left w-full">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                    {{ $paymentId ? 'Process Invoice Payment' : 'Create Plan Invoice' }}
                                </h3>

                                <form wire:submit="save" class="mt-6 space-y-4">
                                    <div>
                                        <label for="user_id"
                                            class="block text-sm font-medium leading-6 text-gray-900">Student <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model="user_id" id="user_id"
                                            class="mt-2 block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            @if($paymentId) disabled class="bg-gray-50 text-gray-500" @endif required>
                                            <option value="">Select a student...</option>
                                            @foreach($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->phone
                                                ?? 'No Phone' }})</option>
                                            @endforeach
                                        </select>
                                        @error('user_id') <span class="text-sm text-red-500 mt-1 block">{{ $message
                                            }}</span> @enderror
                                    </div>

                                    @if(!$paymentId)
                                    <div>
                                        <label for="library_plan_id"
                                            class="block text-sm font-medium leading-6 text-gray-900">Select Library
                                            Plan <span class="text-red-500">*</span></label>
                                        <select wire:model.live="library_plan_id" id="library_plan_id"
                                            class="mt-2 block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            required>
                                            <option value="">Select an active plan...</option>
                                            @foreach($libraryPlans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $global_currency }}{{
                                                number_format($plan->price, 2) }})</option>
                                            @endforeach
                                        </select>
                                        @error('library_plan_id') <span class="text-sm text-red-500 mt-1 block">{{
                                            $message
                                            }}</span> @enderror
                                    </div>
                                    @endif

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="amount"
                                                class="block text-sm font-medium leading-6 text-gray-900">Amount ({{
                                                $global_currency }})
                                                <span class="text-red-500">*</span></label>
                                            <div class="mt-2 text-left relative">
                                                <input type="number" step="0.01" wire:model="amount" id="amount"
                                                    class="block w-full rounded-lg border-0 py-2 pl-3 text-gray-500 bg-gray-50 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6"
                                                    readonly required>
                                            </div>
                                            @error('amount') <span class="text-sm text-red-500 mt-1 block">{{ $message
                                                }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="payment_date"
                                                class="block text-sm font-medium leading-6 text-gray-900">Payment Date
                                                <span class="text-red-500">*</span></label>
                                            <div class="mt-2 text-left">
                                                <input type="date" wire:model="payment_date" id="payment_date"
                                                    class="block w-full rounded-lg border-0 py-2 pl-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                    required>
                                            </div>
                                            @error('payment_date') <span class="text-sm text-red-500 mt-1 block">{{
                                                $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="status"
                                                class="block text-sm font-medium leading-6 text-gray-900">Payment Status
                                                <span class="text-red-500">*</span></label>
                                            <select wire:model="status" id="status"
                                                class="mt-2 block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                required>
                                                <option value="paid">Paid</option>
                                                <option value="pending">Pending</option>
                                                <option value="overdue">Overdue</option>
                                            </select>
                                            @error('status') <span class="text-sm text-red-500 mt-1 block">{{ $message
                                                }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="payment_method"
                                                class="block text-sm font-medium leading-6 text-gray-900">Payment Method
                                                <span class="text-red-500">*</span></label>
                                            <select wire:model="payment_method" id="payment_method"
                                                class="mt-2 block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                required>
                                                <option value="cash">Cash</option>
                                                <option value="online">Online</option>
                                            </select>
                                            @error('payment_method') <span class="text-sm text-red-500 mt-1 block">{{
                                                $message
                                                }}</span> @enderror
                                        </div>
                                    </div>

                                    <div x-show="$wire.payment_method === 'online'" style="display: none;">
                                        <label for="transaction_id"
                                            class="block text-sm font-medium leading-6 text-gray-900">Transaction
                                            ID</label>
                                        <div class="mt-2 text-left">
                                            <input type="text" wire:model="transaction_id" id="transaction_id"
                                                class="block w-full rounded-lg border-0 py-2 pl-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                placeholder="e.g. pi_3MtwBwLkdIwHu7ix28a3tqD0">
                                        </div>
                                        @error('transaction_id') <span class="text-sm text-red-500 mt-1 block">{{
                                            $message
                                            }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="remarks"
                                            class="block text-sm font-medium leading-6 text-gray-900">Remarks
                                            (Optional)</label>
                                        <div class="mt-2 text-left">
                                            <textarea wire:model="remarks" id="remarks" rows="2"
                                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-colors"
                                                placeholder="E.g., Cash payment, partial payment, etc."></textarea>
                                        </div>
                                        @error('remarks') <span class="text-sm text-red-500 mt-1 block text-left">{{
                                            $message }}</span> @enderror
                                    </div>

                                    <div class="mt-6 sm:mt-8 sm:flex sm:flex-row-reverse border-t border-gray-100 pt-4">
                                        <button type="submit"
                                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                                            {{ $paymentId ? 'Save Changes' : 'Record Payment' }}
                                        </button>
                                        <button type="button" wire:click="closeModal"
                                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
