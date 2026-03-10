<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Leads</h1>
            <p class="mt-1 text-sm text-slate-500">Capture and track inquiries from your public library page.</p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total</p>
                <p class="mt-1 text-xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">New</p>
                <p class="mt-1 text-xl font-bold text-indigo-600">{{ $stats['new'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Contacted</p>
                <p class="mt-1 text-xl font-bold text-amber-600">{{ $stats['contacted'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">Converted</p>
                <p class="mt-1 text-xl font-bold text-emerald-600">{{ $stats['converted'] }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <h2 class="text-base font-semibold text-slate-900">Add Lead Manually</h2>
            <form wire:submit.prevent="saveLead" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
                    <input type="text" wire:model="name"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</label>
                    <input type="text" wire:model="phone"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                    <input type="email" wire:model="email"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                    <textarea rows="3" wire:model="message"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('message') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Save Lead
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 gap-3 border-b border-slate-200 p-4 sm:grid-cols-3 sm:p-5">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name / phone / email"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <select wire:model.live="statusFilter"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="converted">Converted</option>
                    <option value="closed">Closed</option>
                </select>
                <div class="text-xs text-slate-500 flex items-center">Public form submissions + manual entries</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Lead</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Source</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($leads as $lead)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <p class="font-semibold text-slate-900">{{ $lead->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($lead->message, 100) ?: 'No message' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <p>{{ $lead->phone ?: '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $lead->email ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</td>
                                <td class="px-4 py-3">
                                    <select wire:change="changeStatus({{ $lead->id }}, $event.target.value)"
                                        class="rounded-lg border-slate-300 text-xs font-semibold shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="new" @selected($lead->status === 'new')>New</option>
                                        <option value="contacted" @selected($lead->status === 'contacted')>Contacted</option>
                                        <option value="converted" @selected($lead->status === 'converted')>Converted</option>
                                        <option value="closed" @selected($lead->status === 'closed')>Closed</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $lead->created_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">No leads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 p-4">
                {{ $leads->links() }}
            </div>
        </div>
    </div>
</div>
