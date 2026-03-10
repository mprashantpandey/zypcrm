<?php

namespace App\Models;

use App\Models\Scopes\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'date',
        'status',
        'check_in',
        'check_out',
        'action_ip',
        'action_device',
        'action_latitude',
        'action_longitude',
        'anomaly_flags',
    ];

    protected $casts = [
        'date' => 'date',
        'anomaly_flags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
