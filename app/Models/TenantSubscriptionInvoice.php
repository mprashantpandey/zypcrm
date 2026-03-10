<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscriptionInvoice extends Model
{
    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'subscription_plan_id',
        'invoice_no',
        'amount',
        'currency_code',
        'due_date',
        'status',
        'payment_method',
        'paid_at',
        'receipt_emailed_at',
        'receipt_email_attempts',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'receipt_emailed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
