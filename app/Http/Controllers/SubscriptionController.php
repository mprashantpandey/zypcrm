<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use App\Services\SubscriptionPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionPaymentService $paymentService) {}

    private function resolveTenant(): ?Tenant
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'library_owner') {
            return null;
        }

        return Tenant::find($user->tenant_id);
    }

    private function calculateSubscriptionEndDate(SubscriptionPlan $plan)
    {
        $cycle = strtolower((string) ($plan->billing_cycle ?? 'monthly'));

        return match ($cycle) {
            'yearly' => now()->addYear(),
            'weekly' => now()->addWeek(),
            'daily' => now()->addDay(),
            default => now()->addMonth(),
        };
    }

    private function activateSubscription(Tenant $tenant, SubscriptionPlan $plan): void
    {
        DB::transaction(function () use ($tenant, $plan): void {
            $tenant->subscriptions()
                ->where('status', 'active')
                ->update(['status' => 'canceled']);

            $tenant->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'ends_at' => $this->calculateSubscriptionEndDate($plan),
            ]);
        });
    }

    // ─── Stripe ─────────────────────────────────────────────────────────────────

    /**
     * Initiate a Stripe Checkout Session for the given plan.
     * GET /subscription/stripe/checkout/{plan}
     */
    public function stripeCheckout(SubscriptionPlan $plan)
    {
        if (! $this->resolveTenant()) {
            abort(403, 'Only library owners can buy subscriptions.');
        }

        try {
            $currency = Setting::getCurrencyCode('USD');
            $amountCents = (int) ($plan->price * 100);

            $url = $this->paymentService->createStripeCheckout(
                planName:   $plan->name,
                amountInPaise: $amountCents,
                currency:   $currency,
                successUrl: route('subscription.stripe.success', ['plan' => $plan->id, 'session_id' => '{CHECKOUT_SESSION_ID}']),
                cancelUrl:  route('subscription.stripe.cancel'),
            );

            return redirect()->away($url);
        } catch (\Exception $e) {
            Log::error('Stripe checkout failed: ' . $e->getMessage());
            return back()->withErrors(['payment' => 'Payment gateway error: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle successful Stripe payment.
     * GET /subscription/stripe/success
     */
    public function stripeSuccess(Request $request, SubscriptionPlan $plan)
    {
        $sessionId = $request->query('session_id');
        if (empty($sessionId)) {
            return redirect()->route('dashboard')->withErrors(['payment' => 'Missing Stripe session reference.']);
        }

        try {
            $session = $this->paymentService->retrieveStripeSession($sessionId);

            if ($session->payment_status === 'paid') {
                $tenant = $this->resolveTenant();
                if (! $tenant) {
                    return redirect()->route('dashboard')->withErrors(['payment' => 'No library account found for this user.']);
                }
                $this->activateSubscription($tenant, $plan);

                return redirect()->route('dashboard')->with('success', "Subscription to {$plan->name} activated via Stripe!");
            }

            return redirect()->route('dashboard')->withErrors(['payment' => 'Stripe payment was not completed.']);
        } catch (\Exception $e) {
            Log::error('Stripe success verification failed: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors(['payment' => 'Could not verify payment.']);
        }
    }

    public function stripeCancel()
    {
        return redirect()->route('dashboard')->with('warning', 'Payment was cancelled.');
    }

    // ─── Razorpay ────────────────────────────────────────────────────────────────

    /**
     * Create a Razorpay Order and show the payment form.
     * GET /subscription/razorpay/checkout/{plan}
     */
    public function razorpayCheckout(SubscriptionPlan $plan)
    {
        if (! $this->resolveTenant()) {
            abort(403, 'Only library owners can buy subscriptions.');
        }

        try {
            $currency = Setting::getCurrencyCode('USD');
            $amountPaise = (int) ($plan->price * 100);

            $order = $this->paymentService->createRazorpayOrder(
                amountInPaise: $amountPaise,
                currency:      $currency,
                receipt:       'plan_' . $plan->id . '_user_' . Auth::id(),
            );

            $razorpayKeyId = Setting::getValue('razorpay_key', Setting::getValue('razorpay_key_id'));

            return view('subscription.razorpay', compact('plan', 'order', 'razorpayKeyId'));
        } catch (\Exception $e) {
            Log::error('Razorpay checkout failed: ' . $e->getMessage());
            return back()->withErrors(['payment' => 'Payment gateway error: ' . $e->getMessage()]);
        }
    }

    /**
     * Verify Razorpay payment and activate subscription.
     * POST /subscription/razorpay/verify
     */
    public function razorpayVerify(Request $request, SubscriptionPlan $plan)
    {
        if (! $this->resolveTenant()) {
            abort(403, 'Only library owners can buy subscriptions.');
        }

        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        try {
            $this->paymentService->verifyRazorpayPayment(
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
            );

            $tenant = $this->resolveTenant();
            if (! $tenant) {
                return redirect()->route('dashboard')->withErrors(['payment' => 'No library account found for this user.']);
            }
            $this->activateSubscription($tenant, $plan);

            return redirect()->route('dashboard')->with('success', "Subscription to {$plan->name} activated via Razorpay!");
        } catch (\Exception $e) {
            Log::error('Razorpay verification failed: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors(['payment' => 'Payment verification failed: ' . $e->getMessage()]);
        }
    }
}
