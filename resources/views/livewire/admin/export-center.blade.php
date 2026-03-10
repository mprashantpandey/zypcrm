<div class="py-10 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session()->has('message'))
            <div class="rounded-lg bg-green-50 p-4 border border-green-100 text-sm text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Data Export Center</h1>
            <p class="mt-1 text-sm text-gray-500">One-click exports for students, attendance, leaves, payments, and invoices.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <button type="button" wire:click="$set('dataset','students')"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $dataset === 'students' ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700' }}">
                Students
            </button>
            <button type="button" wire:click="$set('dataset','attendance')"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $dataset === 'attendance' ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700' }}">
                Attendance
            </button>
            <button type="button" wire:click="$set('dataset','leaves')"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $dataset === 'leaves' ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700' }}">
                Leaves
            </button>
            <button type="button" wire:click="$set('dataset','payments')"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $dataset === 'payments' ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700' }}">
                Payments
            </button>
            <button type="button" wire:click="$set('dataset','invoices')"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $dataset === 'invoices' ? 'border-indigo-300 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-700' }}">
                Invoices
            </button>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Dataset</label>
                    <select wire:model.live="dataset" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="students">Students</option>
                        <option value="attendance">Attendance</option>
                        <option value="leaves">Leaves</option>
                        <option value="payments">Payments</option>
                        <option value="invoices">Invoices</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Tenant</label>
                    <select wire:model.live="tenantId" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Tenants</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Status</label>
                    <select wire:model.live="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Date From</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Date To</label>
                    <input type="date" wire:model.live="dateTo" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="name / email / phone"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-sm text-gray-600">
                    Matching records: <span class="font-semibold text-gray-900">{{ number_format($previewCount) }}</span>
                </p>
                <button type="button" wire:click="exportCsv"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Export CSV
                </button>
            </div>
        </div>
    </div>
</div>

