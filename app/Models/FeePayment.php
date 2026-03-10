<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'amount',
        'gross_amount',
        'discount_amount',
        'referral_credit_used',
        'promo_code_id',
        'payment_date',
        'payment_method',
        'transaction_id',
        'status',
        'remarks',
        'platform_fee_amount',
        'net_amount',
        'slug',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = \Illuminate\Support\Str::random(12);
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }
}
