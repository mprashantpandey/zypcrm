<?php

namespace App\Livewire\Admin;

use App\Models\PromoCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CampaignManager extends Component
{
    public string $code = '';
    public string $discountType = 'fixed';
    public float $discountValue = 0;
    public ?float $maxDiscountAmount = null;
    public ?int $maxUses = null;
    public bool $isActive = true;
    public string $startsAt = '';
    public string $endsAt = '';

    public function savePromoCode(): void
    {
        $this->validate([
            'code' => 'required|string|max:40',
            'discountType' => 'required|in:fixed,percent',
            'discountValue' => 'required|numeric|min:0.01',
            'maxDiscountAmount' => 'nullable|numeric|min:0',
            'maxUses' => 'nullable|integer|min:1',
            'startsAt' => 'nullable|date',
            'endsAt' => 'nullable|date|after_or_equal:startsAt',
        ]);

        PromoCode::updateOrCreate([
            'code' => strtoupper(trim($this->code)),
        ], [
            'tenant_id' => null,
            'discount_type' => $this->discountType,
            'discount_value' => $this->discountValue,
            'max_discount_amount' => $this->maxDiscountAmount,
            'max_uses' => $this->maxUses,
            'is_active' => $this->isActive,
            'starts_at' => $this->startsAt !== '' ? $this->startsAt : null,
            'ends_at' => $this->endsAt !== '' ? $this->endsAt : null,
            'created_by' => auth()->id(),
        ]);

        $this->reset(['code', 'discountType', 'discountValue', 'maxDiscountAmount', 'maxUses', 'isActive', 'startsAt', 'endsAt']);
        $this->discountType = 'fixed';
        $this->discountValue = 0;
        $this->isActive = true;
        $this->dispatch('notify', type: 'success', message: 'Promo code saved.');
    }

    public function togglePromo(int $id): void
    {
        $promo = PromoCode::findOrFail($id);
        $promo->update(['is_active' => ! $promo->is_active]);
        $this->dispatch('notify', type: 'success', message: 'Promo status updated.');
    }

    public function render()
    {
        $promos = collect();
        $metrics = [
            'promo_uses' => 0,
            'discount_given' => 0,
            'referral_conversions' => 0,
            'referral_credits_issued' => 0,
        ];

        if (Schema::hasTable('promo_codes')) {
            $promos = PromoCode::query()->latest()->paginate(10);
        }

        if (Schema::hasTable('fee_payments')) {
            if (Schema::hasColumn('fee_payments', 'promo_code_id')) {
                $promoUsesQuery = DB::table('fee_payments')->whereNotNull('promo_code_id');
                $metrics['promo_uses'] = (clone $promoUsesQuery)->count();
            }
            if (Schema::hasColumn('fee_payments', 'discount_amount')) {
                $metrics['discount_given'] = (float) DB::table('fee_payments')->sum('discount_amount');
            }
        }

        if (Schema::hasTable('referrals')) {
            $metrics['referral_conversions'] = DB::table('referrals')->where('status', 'converted')->count();
        }

        if (Schema::hasTable('referral_credits')) {
            $metrics['referral_credits_issued'] = (float) DB::table('referral_credits')->sum('amount');
        }

        return view('livewire.admin.campaign-manager', [
            'promos' => $promos,
            'metrics' => $metrics,
        ]);
    }
}
