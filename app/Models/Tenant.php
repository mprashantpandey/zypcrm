<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'status',
        'email',
        'phone',
        'address',
        'operating_hours',
    ];

    protected $casts = [
        'operating_hours' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->ofMany(['id' => 'max'], function ($query) {
            $query->where('status', 'active');
        });
    }

    public function libraryPlans()
    {
        return $this->hasMany(LibraryPlan::class);
    }

    public function studentSubscriptions()
    {
        return $this->hasMany(StudentSubscription::class);
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function studentLeaves()
    {
        return $this->hasMany(StudentLeave::class);
    }

    public function studentMemberships()
    {
        return $this->hasMany(StudentMembership::class);
    }
}
