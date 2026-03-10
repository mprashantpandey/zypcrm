<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            if (empty($user->referral_code)) {
                do {
                    $code = strtoupper(\Illuminate\Support\Str::random(8));
                } while (User::query()->where('referral_code', $code)->exists());
                $user->forceFill(['referral_code' => $code])->saveQuietly();
            }

            if ($user->role === 'student' && $user->tenant_id) {
                StudentMembership::updateOrCreate(
                    ['user_id' => $user->id, 'tenant_id' => $user->tenant_id],
                    ['status' => 'active', 'joined_at' => $user->created_at]
                );
            }
        });

        static::updated(function (User $user): void {
            if ($user->role === 'student' && $user->tenant_id && $user->wasChanged('tenant_id')) {
                StudentMembership::updateOrCreate(
                    ['user_id' => $user->id, 'tenant_id' => $user->tenant_id],
                    ['status' => 'active', 'joined_at' => $user->updated_at]
                );
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'referral_code',
        'firebase_uid',
        'fcm_token',
        'password',
        'role',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function memberships()
    {
        return $this->hasMany(StudentMembership::class, 'user_id');
    }

    public function memberTenants()
    {
        return $this->belongsToMany(Tenant::class, 'student_memberships', 'user_id', 'tenant_id')
            ->withPivot(['status', 'joined_at'])
            ->withTimestamps();
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function ticketReplies()
    {
        return $this->hasMany(SupportTicketReply::class);
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

    public function assignedSeat()
    {
        return $this->hasOne(Seat::class , 'user_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(StudentSubscription::class , 'user_id')->where('status', 'active')->latestOfMany();
    }
}
