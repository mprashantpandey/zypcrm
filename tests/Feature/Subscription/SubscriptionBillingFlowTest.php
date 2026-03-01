<?php

namespace Tests\Feature\Subscription;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SubscriptionPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SubscriptionBillingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_razorpay_verification_activates_subscription_with_correct_columns(): void
    {
        $tenant = Tenant::create([
            'name' => 'Billing Tenant',
            'status' => 'active',
        ]);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        $plan = SubscriptionPlan::create([
            'name' => 'Pro Monthly',
            'price' => 999,
            'billing_cycle' => 'monthly',
            'max_students' => 0,
            'is_active' => true,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addDays(2),
        ]);

        $mock = Mockery::mock(SubscriptionPaymentService::class);
        $mock->shouldReceive('verifyRazorpayPayment')
            ->once()
            ->with('order_1', 'pay_1', 'sig_1')
            ->andReturnNull();
        $this->app->instance(SubscriptionPaymentService::class, $mock);

        $this->actingAs($owner)
            ->post(route('subscription.razorpay.verify', $plan), [
                'razorpay_order_id' => 'order_1',
                'razorpay_payment_id' => 'pay_1',
                'razorpay_signature' => 'sig_1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('subscriptions', 2);
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'canceled',
        ]);
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }
}

