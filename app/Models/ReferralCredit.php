<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCredit extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'amount',
        'remaining_amount',
        'status',
        'source_type',
        'source_id',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
