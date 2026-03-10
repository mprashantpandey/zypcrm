<div class="py-6 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Subscription Plans</h1>
            <p class="mt-1 text-sm text-slate-500">Manage pricing tiers, schedule windows, and validity for student memberships.</p>
        </div>

        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Plan
        </button>
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Plans</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $plans->count() }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Active Plans</p>
            <p class="mt-2 text-2xl font-bold text-emerald-900">{{ $plans->where('is_active', true)->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Inactive Plans</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $plans->where('is_active', false)->count() }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Plan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Validity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Timings</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($plans as $plan)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $plan->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">
                                {{ $global_currency }}{{ number_format($plan->price, 0) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $plan->duration_days }} days</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @if($plan->start_time && $plan->end_time)
                                    {{ \Carbon\Carbon::parse($plan->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($plan->end_time)->format('h:i A') }}
                                @else
                                    <span class="text-slate-400">24 hours / anytime</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $plan->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="editPlan({{ $plan->id }})"
                                        class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        Edit
                                    </button>

                                    <button type="button"
                                        onclick="confirm('Are you sure you want to delete this plan?') || event.stopImmediatePropagation()"
                                        wire:click="deletePlan({{ $plan->id }})"
                                        class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <p class="text-base font-semibold text-slate-800">No plans created yet</p>
                                <p class="mt-1 text-sm text-slate-500">Create your first subscription plan to start onboarding students.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showPlanModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="plan-modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showPlanModal', false)"></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <div class="inline-block w-full transform overflow-hidden rounded-2xl border border-slate-200 bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:max-w-2xl sm:align-middle">
                    <form wire:submit.prevent="savePlan">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 id="plan-modal-title" class="text-lg font-bold text-slate-900">
                                    {{ $isEditing ? 'Edit Plan' : 'Create New Plan' }}
                                </h3>
                                <button type="button" wire:click="$set('showPlanModal', false)" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-5 px-6 py-5">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Plan Name <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                    placeholder="e.g. Morning Batch">
                                @error('name') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Price ({{ $global_currency }}) <span class="text-rose-500">*</span></label>
                                    <input type="number" wire:model="price"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('price') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Validity (Days) <span class="text-rose-500">*</span></label>
                                    <input type="number" wire:model="duration_days"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('duration_days') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Start Time (Optional)</label>
                                    <input type="time" wire:model="start_time"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('start_time') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                                    <p class="mt-1 text-xs text-slate-500">Leave blank for full-day access.</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">End Time (Optional)</label>
                                    <input type="time" wire:model="end_time"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('end_time') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <label class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Active for new subscriptions
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <button type="button" wire:click="$set('showPlanModal', false)"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                Cancel
                            </button>
                            <button type="submit"
                                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                {{ $isEditing ? 'Save Changes' : 'Create Plan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
