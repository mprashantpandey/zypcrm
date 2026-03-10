<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantImage extends Model
{
    protected $fillable = [
        'tenant_id',
        'image_path',
        'caption',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
