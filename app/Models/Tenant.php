<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant): void {
            if (! Schema::hasColumn('tenants', 'public_slug')) {
                return;
            }

            if (! empty($tenant->public_slug)) {
                return;
            }

            $base = Str::slug((string) $tenant->name);
            if ($base === '') {
                $base = 'library';
            }

            $slug = $base;
            $counter = 1;
            while (static::query()->where('public_slug', $slug)->exists()) {
                $slug = $base.'-'.$counter;
                $counter++;
            }

            $tenant->public_slug = $slug;
        });
    }

    protected $fillable = [
        'name',
        'status',
        'email',
        'phone',
        'address',
        'operating_hours',
        'attendance_security_settings',
        'attendance_registered_device_hash',
        'onboarding_completed_at',
        'onboarding_dismissed_at',
        'public_slug',
        'public_description',
        'public_page_enabled',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'attendance_security_settings' => 'array',
        'onboarding_completed_at' => 'datetime',
        'onboarding_dismissed_at' => 'datetime',
        'public_page_enabled' => 'boolean',
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

    public function leads()
    {
        return $this->hasMany(LibraryLead::class);
    }

    public function images()
    {
        return $this->hasMany(TenantImage::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function subscriptionInvoices()
    {
        return $this->hasMany(TenantSubscriptionInvoice::class)->latest();
    }
}
