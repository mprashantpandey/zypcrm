<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceActionLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'operator_id',
        'date',
        'action',
        'status',
        'success',
        'message',
        'ip_address',
        'device_hash',
        'latitude',
        'longitude',
        'meta',
    ];

    protected $casts = [
        'date' => 'date',
        'success' => 'boolean',
        'meta' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
