<?php

namespace App\Livewire\Admin;

use App\Models\FeePayment;
use App\Models\Setting;
use App\Models\StudentMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscriptionInvoice;
use App\Models\StudentSubscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TenantProfile extends Component
{
    public Tenant $tenant;

    public array $stats = [];
    public array $platformPlans = [];

    public ?int $editingSubscriptionId = null;
    public ?int $subscriptionPlanId = null;
    public string $subscriptionStatus = 'active';
    public ?string $subscriptionEndsAt = null;

    public string $invoiceAmount = '';
    public ?string $invoiceDueDate = null;
    public string $invoicePaymentMethod = 'cash';
    public string $invoiceNotes = '';
    public string $invoiceCurrencyCode = 'INR';

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant->load([
            'users' => fn ($q) => $q->where('role', 'library_owner')->latest(),
            'currentSubscription.plan',
            'subscriptions.plan',
            'libraryPlans' => fn ($q) => $q->latest(),
        ]);

        $this->stats = [
            'students' => User::query()->where('role', 'student')->where('tenant_id', $tenant->id)->count(),
            'memberships' => StudentMembership::query()->where('tenant_id', $tenant->id)->count(),
            'active_subscriptions' => StudentSubscription::query()
                ->where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->count(),
            'monthly_revenue' => (float) FeePayment::query()
                ->where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->whereBetween('payment_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('amount'),
        ];

        $this->platformPlans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'billing_cycle'])
            ->map(fn (SubscriptionPlan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'billing_cycle' => $plan->billing_cycle,
            ])
            ->values()
            ->all();

        $latestSubscription = $this->tenant->subscriptions()->latest()->first();
        if ($latestSubscription) {
            $this->editingSubscriptionId = $latestSubscription->id;
            $this->subscriptionPlanId = (int) $latestSubscription->subscription_plan_id;
            $this->subscriptionStatus = (string) $latestSubscription->status;
            $this->subscriptionEndsAt = $latestSubscription->ends_at?->toDateString();
        } elseif (! empty($this->platformPlans)) {
            $this->subscriptionPlanId = (int) $this->platformPlans[0]['id'];
        }

        $this->invoiceCurrencyCode = Setting::getCurrencyCode('INR');
        $this->invoiceDueDate = now()->addDays(7)->toDateString();
        $this->syncInvoiceAmountFromSelectedPlan();
    }

    public function toggleStatus(): void
    {
        $old = $this->tenant->status;
        $this->tenant->status = $this->tenant->status === 'active' ? 'suspended' : 'active';
        $this->tenant->save();
        $this->tenant->refresh();

        AuditLogger::log(
            action: 'tenant.status.updated',
            tenantId: $this->tenant->id,
            entityType: Tenant::class,
            entityId: $this->tenant->id,
            oldValues: ['status' => $old],
            newValues: ['status' => $this->tenant->status]
        );

        session()->flash('message', 'Library status updated.');
    }

    public function saveSubscription(): void
    {
        $this->validate([
            'subscriptionPlanId' => ['required', 'integer', 'exists:subscription_plans,id'],
            'subscriptionStatus' => ['required', 'in:active,canceled,past_due'],
            'subscriptionEndsAt' => ['nullable', 'date'],
        ], [
            'subscriptionPlanId.required' => 'Please select a subscription plan.',
        ]);

        $endsAt = $this->subscriptionEndsAt ? Carbon::parse($this->subscriptionEndsAt)->endOfDay() : null;

        $wasCreate = false;
        if ($this->editingSubscriptionId) {
            $subscription = Subscription::query()
                ->where('id', $this->editingSubscriptionId)
                ->where('tenant_id', $this->tenant->id)
                ->first();

            if ($subscription) {
                $old = [
                    'subscription_plan_id' => $subscription->subscription_plan_id,
                    'status' => $subscription->status,
                    'ends_at' => optional($subscription->ends_at)?->toDateTimeString(),
                ];
                $subscription->update([
                    'subscription_plan_id' => $this->subscriptionPlanId,
                    'status' => $this->subscriptionStatus,
                    'ends_at' => $endsAt,
                ]);
                AuditLogger::log(
                    action: 'tenant.subscription.updated',
                    tenantId: $this->tenant->id,
                    entityType: Subscription::class,
                    entityId: $subscription->id,
                    oldValues: $old,
                    newValues: [
                        'subscription_plan_id' => $this->subscriptionPlanId,
                        'status' => $this->subscriptionStatus,
                        'ends_at' => optional($endsAt)?->toDateTimeString(),
                    ]
                );
            } else {
                $this->editingSubscriptionId = null;
            }
        }

        if (! $this->editingSubscriptionId) {
            $subscription = Subscription::query()->create([
                'tenant_id' => $this->tenant->id,
                'subscription_plan_id' => $this->subscriptionPlanId,
                'status' => $this->subscriptionStatus,
                'ends_at' => $endsAt,
            ]);
            $this->editingSubscriptionId = $subscription->id;
            $wasCreate = true;
        }

        if ($wasCreate) {
            AuditLogger::log(
                action: 'tenant.subscription.created',
                tenantId: $this->tenant->id,
                entityType: Subscription::class,
                entityId: $this->editingSubscriptionId,
                newValues: [
                    'subscription_plan_id' => $this->subscriptionPlanId,
                    'status' => $this->subscriptionStatus,
                    'ends_at' => optional($endsAt)?->toDateTimeString(),
                ]
            );
        }

        $this->tenant->refresh()->load(['currentSubscription.plan', 'subscriptions.plan']);
        $this->syncInvoiceAmountFromSelectedPlan();
        session()->flash('message', 'Tenant subscription updated.');
    }

    public function extendSubscription(int $days = 30): void
    {
        if (! $this->subscriptionPlanId) {
            session()->flash('message', 'Please select a plan before extending subscription.');
            return;
        }

        $base = $this->subscriptionEndsAt ? Carbon::parse($this->subscriptionEndsAt) : now();
        $this->subscriptionEndsAt = $base->copy()->addDays($days)->toDateString();
        $this->subscriptionStatus = 'active';
        $this->saveSubscription();
    }

    public function updatedSubscriptionPlanId(): void
    {
        $this->syncInvoiceAmountFromSelectedPlan(true);
    }

    public function createInvoice(): void
    {
        $this->validateInvoiceInput();

        $invoice = TenantSubscriptionInvoice::query()->create([
            'tenant_id' => $this->tenant->id,
            'subscription_id' => $this->editingSubscriptionId,
            'subscription_plan_id' => $this->subscriptionPlanId,
            'invoice_no' => $this->generateInvoiceNo(),
            'amount' => (float) $this->invoiceAmount,
            'currency_code' => strtoupper(trim($this->invoiceCurrencyCode)) ?: Setting::getCurrencyCode('INR'),
            'due_date' => $this->invoiceDueDate,
            'status' => 'pending',
            'notes' => trim($this->invoiceNotes) ?: null,
            'created_by' => Auth::id(),
        ]);

        AuditLogger::log(
            action: 'tenant.subscription.invoice.created',
            tenantId: $this->tenant->id,
            entityType: TenantSubscriptionInvoice::class,
            entityId: $invoice->id,
            newValues: [
                'invoice_no' => $invoice->invoice_no,
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'due_date' => optional($invoice->due_date)?->toDateString(),
            ]
        );

        $this->invoiceNotes = '';
        $this->invoiceDueDate = now()->addDays(7)->toDateString();
        session()->flash('message', 'Invoice created successfully.');
    }

    public function collectPaymentNow(): void
    {
        $this->validateInvoiceInput();

        $invoice = TenantSubscriptionInvoice::query()->create([
            'tenant_id' => $this->tenant->id,
            'subscription_id' => $this->editingSubscriptionId,
            'subscription_plan_id' => $this->subscriptionPlanId,
            'invoice_no' => $this->generateInvoiceNo(),
            'amount' => (float) $this->invoiceAmount,
            'currency_code' => strtoupper(trim($this->invoiceCurrencyCode)) ?: Setting::getCurrencyCode('INR'),
            'due_date' => $this->invoiceDueDate,
            'status' => 'paid',
            'payment_method' => trim($this->invoicePaymentMethod) !== '' ? trim($this->invoicePaymentMethod) : 'manual',
            'paid_at' => now(),
            'notes' => trim($this->invoiceNotes) ?: 'Collected by admin from tenant profile page.',
            'created_by' => Auth::id(),
        ]);

        AuditLogger::log(
            action: 'tenant.subscription.invoice.paid_direct',
            tenantId: $this->tenant->id,
            entityType: TenantSubscriptionInvoice::class,
            entityId: $invoice->id,
            newValues: [
                'invoice_no' => $invoice->invoice_no,
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'payment_method' => $invoice->payment_method,
            ]
        );

        $this->invoiceNotes = '';
        session()->flash('message', 'Payment collected and invoice marked as paid.');
    }

    public function markInvoicePaid(int $invoiceId): void
    {
        $invoice = TenantSubscriptionInvoice::query()
            ->where('tenant_id', $this->tenant->id)
            ->where('id', $invoiceId)
            ->firstOrFail();

        if ($invoice->status === 'paid') {
            session()->flash('message', 'Invoice is already marked as paid.');
            return;
        }

        $oldStatus = $invoice->status;
        $invoice->update([
            'status' => 'paid',
            'payment_method' => trim($this->invoicePaymentMethod) !== '' ? trim($this->invoicePaymentMethod) : ($invoice->payment_method ?: 'manual'),
            'paid_at' => now(),
        ]);

        AuditLogger::log(
            action: 'tenant.subscription.invoice.mark_paid',
            tenantId: $this->tenant->id,
            entityType: TenantSubscriptionInvoice::class,
            entityId: $invoice->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'paid', 'payment_method' => $invoice->payment_method]
        );

        session()->flash('message', 'Invoice marked as paid.');
    }

    public function cancelInvoice(int $invoiceId): void
    {
        $invoice = TenantSubscriptionInvoice::query()
            ->where('tenant_id', $this->tenant->id)
            ->where('id', $invoiceId)
            ->firstOrFail();

        if ($invoice->status === 'paid') {
            session()->flash('message', 'Paid invoice cannot be cancelled.');
            return;
        }

        $oldStatus = $invoice->status;
        $invoice->update(['status' => 'cancelled']);

        AuditLogger::log(
            action: 'tenant.subscription.invoice.cancelled',
            tenantId: $this->tenant->id,
            entityType: TenantSubscriptionInvoice::class,
            entityId: $invoice->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'cancelled']
        );

        session()->flash('message', 'Invoice cancelled.');
    }

    private function validateInvoiceInput(): void
    {
        $this->validate([
            'invoiceAmount' => ['required', 'numeric', 'min:1'],
            'invoiceDueDate' => ['nullable', 'date'],
            'invoicePaymentMethod' => ['nullable', 'string', 'max:40'],
            'invoiceCurrencyCode' => ['nullable', 'string', 'max:10'],
            'invoiceNotes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function syncInvoiceAmountFromSelectedPlan(bool $force = false): void
    {
        if (! $this->subscriptionPlanId) {
            return;
        }

        $selectedPlan = collect($this->platformPlans)->firstWhere('id', (int) $this->subscriptionPlanId);
        if ($selectedPlan && ($force || $this->invoiceAmount === '' || (float) $this->invoiceAmount <= 0)) {
            $this->invoiceAmount = (string) ((float) $selectedPlan['price']);
        }
    }

    private function generateInvoiceNo(): string
    {
        do {
            $candidate = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (TenantSubscriptionInvoice::query()->where('invoice_no', $candidate)->exists());

        return $candidate;
    }

    public function render()
    {
        $subscriptionHistory = $this->tenant->subscriptions()->with('plan')->latest()->take(6)->get();
        $invoiceHistory = TenantSubscriptionInvoice::query()
            ->where('tenant_id', $this->tenant->id)
            ->with('plan')
            ->latest()
            ->take(12)
            ->get();

        return view('livewire.admin.tenant-profile', [
            'subscriptionHistory' => $subscriptionHistory,
            'invoiceHistory' => $invoiceHistory,
        ]);
    }
}
