<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Coupon & Referral Campaigns</h1>
            <p class="mt-1 text-sm text-slate-500">Manage promo codes, track conversions, and monitor referral economics.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Promo Uses</p>
                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($metrics['promo_uses']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Discount Given</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $global_currency }}{{ number_format($metrics['discount_given'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Referral Conversions</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">{{ number_format($metrics['referral_conversions']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Credits Issued</p>
                <p class="mt-1 text-2xl font-bold text-sky-600">{{ $global_currency }}{{ number_format($metrics['referral_credits_issued'], 2) }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Create Promo Code</h2>
            <form wire:submit.prevent="savePromoCode" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Code</label>
                    <input wire:model="code" type="text" class="w-full rounded-lg border-slate-300 text-sm shadow-sm" placeholder="WELCOME100">
                    @error('code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type</label>
                    <select wire:model="discountType" class="w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        <option value="fixed">Fixed</option>
                        <option value="percent">Percent</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Value</label>
                    <input wire:model="discountValue" type="number" step="0.01" min="0" class="w-full rounded-lg border-slate-300 text-sm shadow-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Max Uses</label>
                    <input wire:model="maxUses" type="number" min="1" class="w-full rounded-lg border-slate-300 text-sm shadow-sm" placeholder="Optional">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Max Discount</label>
                    <input wire:model="maxDiscountAmount" type="number" step="0.01" min="0" class="w-full rounded-lg border-slate-300 text-sm shadow-sm" placeholder="Optional">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Starts At</label>
                    <input wire:model="startsAt" type="date" class="w-full rounded-lg border-slate-300 text-sm shadow-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Ends At</label>
                    <input wire:model="endsAt" type="date" class="w-full rounded-lg border-slate-300 text-sm shadow-sm">
                </div>
                <div class="flex items-end gap-2">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" wire:model="isActive" class="rounded border-slate-300 text-indigo-600 shadow-sm">
                        Active
                    </label>
                    <button type="submit" class="ml-auto rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-3">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Promo Codes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Discount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usage</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Validity</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($promos as $promo)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $promo->code }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ $promo->discount_type === 'percent' ? $promo->discount_value.'%' : ($global_currency.number_format($promo->discount_value, 2)) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $promo->used_count }}{{ $promo->max_uses ? ' / '.$promo->max_uses : '' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $promo->starts_at?->format('d M Y') ?: 'Now' }} - {{ $promo->ends_at?->format('d M Y') ?: 'No expiry' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="togglePromo({{ $promo->id }})" class="rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $promo->is_active ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                        {{ $promo->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No promo codes created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($promos instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="border-t border-slate-200 p-4">{{ $promos->links() }}</div>
            @endif
        </div>
    </div>
</div>
