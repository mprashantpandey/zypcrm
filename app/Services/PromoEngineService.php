<?php

namespace App\Services;

use App\Models\FeePayment;
use App\Models\PromoCode;
use App\Models\Referral;
use App\Models\ReferralCredit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PromoEngineService
{
    public function applyPromo(?int $tenantId, ?string $code, float $grossAmount): array
    {
        $payload = [
            'promo' => null,
            'discount' => 0.0,
            'net' => max(0, $grossAmount),
            'error' => null,
        ];

        $normalized = strtoupper(trim((string) $code));
        if ($normalized === '') {
            return $payload;
        }

        $promo = PromoCode::query()
            ->where('code', $normalized)
            ->where('is_active', true)
            ->where(function ($q) use ($tenantId): void {
                $q->whereNull('tenant_id');
                if ($tenantId) {
                    $q->orWhere('tenant_id', $tenantId);
                }
            })
            ->first();

        if (! $promo) {
            $payload['error'] = 'Invalid promo code.';

            return $payload;
        }

        if ($promo->starts_at && now()->lt($promo->starts_at)) {
            $payload['error'] = 'Promo code is not active yet.';

            return $payload;
        }

        if ($promo->ends_at && now()->gt($promo->ends_at)) {
            $payload['error'] = 'Promo code expired.';

            return $payload;
        }

        if ($promo->max_uses !== null && $promo->used_count >= $promo->max_uses) {
            $payload['error'] = 'Promo code usage limit reached.';

            return $payload;
        }

        $discount = 0.0;
        if ($promo->discount_type === 'percent') {
            $discount = round(($grossAmount * (float) $promo->discount_value) / 100, 2);
        } else {
            $discount = round((float) $promo->discount_value, 2);
        }

        if ($promo->max_discount_amount !== null) {
            $discount = min($discount, (float) $promo->max_discount_amount);
        }

        $discount = min($discount, max(0, $grossAmount));

        $payload['promo'] = $promo;
        $payload['discount'] = $discount;
        $payload['net'] = round(max(0, $grossAmount - $discount), 2);

        return $payload;
    }

    public function applyReferralCredit(int $tenantId, int $studentId, float $amountAfterPromo): array
    {
        $credit = ReferralCredit::query()
            ->where('tenant_id', $tenantId)
            ->where('user_id', $studentId)
            ->where('status', 'available')
            ->where(function ($q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->orderBy('created_at')
            ->first();

        if (! $credit || $amountAfterPromo <= 0) {
            return ['used' => 0.0, 'net' => max(0, $amountAfterPromo)];
        }

        $usable = min((float) $credit->remaining_amount, $amountAfterPromo);
        if ($usable <= 0) {
            return ['used' => 0.0, 'net' => max(0, $amountAfterPromo)];
        }

        DB::transaction(function () use ($credit, $usable): void {
            $remaining = round((float) $credit->remaining_amount - $usable, 2);
            $credit->remaining_amount = max(0, $remaining);
            if ($credit->remaining_amount <= 0) {
                $credit->status = 'used';
                $credit->used_at = now();
            }
            $credit->save();
        });

        return [
            'used' => $usable,
            'net' => round(max(0, $amountAfterPromo - $usable), 2),
        ];
    }

    public function registerReferralIfApplicable(int $tenantId, int $referredStudentId, ?string $referralCode): void
    {
        $code = strtoupper(trim((string) $referralCode));
        if ($code === '') {
            return;
        }

        $referred = User::query()->find($referredStudentId);
        if (! $referred) {
            return;
        }

        $referrer = User::query()
            ->where('role', 'student')
            ->where('referral_code', $code)
            ->whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId)->where('status', 'active'))
            ->first();

        if (! $referrer || $referrer->id === $referredStudentId) {
            return;
        }

        Referral::query()->firstOrCreate([
            'tenant_id' => $tenantId,
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $referredStudentId,
        ], [
            'referral_code' => $code,
            'status' => 'pending',
        ]);
    }

    public function markConversionAndIssueCredit(FeePayment $payment): void
    {
        $referral = Referral::query()
            ->where('tenant_id', $payment->tenant_id)
            ->where('referred_user_id', $payment->user_id)
            ->where('status', 'pending')
            ->first();

        if (! $referral) {
            return;
        }

        DB::transaction(function () use ($payment, $referral): void {
            $referral->status = 'converted';
            $referral->converted_at = now();
            $referral->save();

            $creditAmount = (float) max(50, round(((float) $payment->amount) * 0.10, 2));
            ReferralCredit::create([
                'tenant_id' => $payment->tenant_id,
                'user_id' => $referral->referrer_user_id,
                'amount' => $creditAmount,
                'remaining_amount' => $creditAmount,
                'status' => 'available',
                'source_type' => 'referral',
                'source_id' => $referral->id,
                'expires_at' => now()->addMonths(6),
            ]);
        });
    }

    public function registerPromoUse(?PromoCode $promo): void
    {
        if (! $promo) {
            return;
        }

        $promo->increment('used_count');
    }
}
