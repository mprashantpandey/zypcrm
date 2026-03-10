<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'tenant_id',
        'created_by',
        'title',
        'body',
        'level',
        'audience',
        'delivery_in_app',
        'delivery_email',
        'delivery_push',
        'delivery_whatsapp',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'delivery_in_app' => 'boolean',
        'delivery_email' => 'boolean',
        'delivery_push' => 'boolean',
        'delivery_whatsapp' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
