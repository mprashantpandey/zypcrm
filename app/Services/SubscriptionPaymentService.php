<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use Razorpay\Api\Api as RazorpayApi;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session as StripeSession;

class SubscriptionPaymentService
{
    protected function getFirstSetting(array $keys, ?string $default = null): ?string
    {
        foreach ($keys as $key) {
            $value = $this->getSetting($key);
            if (! empty($value)) {
                return $value;
            }
        }

        return $default;
    }

    protected function getSetting(string $key, ?string $default = null): ?string
    {
        static $cache = [];
        if (!isset($cache[$key])) {
            $cache[$key] = Setting::where('key', $key)->value('value');
        }
        return $cache[$key] ?? $default;
    }

    // ─── Stripe ─────────────────────────────────────────────────────────────────

    protected function configureStripe(): void
    {
        $secret = $this->getFirstSetting(['stripe_secret', 'stripe_secret_key']);
        if (empty($secret)) {
            throw new Exception('Stripe secret key is not configured in Platform Settings.');
        }
        Stripe::setApiKey($secret);
    }

    /**
     * Create a Stripe Checkout Session for a subscription plan.
     * Returns the URL to redirect the customer to.
     */
    public function createStripeCheckout(string $planName, int $amountInPaise, string $currency, string $successUrl, string $cancelUrl): string
    {
        $this->configureStripe();

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'unit_amount' => $amountInPaise,
                        'product_data' => ['name' => $planName],
                    ],
                    'quantity' => 1,
                ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return $session->url;
    }

    /**
     * Retrieve and verify a completed Stripe Checkout Session.
     */
    public function retrieveStripeSession(string $sessionId): StripeSession
    {
        $this->configureStripe();
        return StripeSession::retrieve($sessionId);
    }

    // ─── Razorpay ────────────────────────────────────────────────────────────────

    protected function getRazorpayClient(): RazorpayApi
    {
        $key = $this->getFirstSetting(['razorpay_key', 'razorpay_key_id']);
        $secret = $this->getFirstSetting(['razorpay_secret', 'razorpay_key_secret']);
        if (empty($key) || empty($secret)) {
            throw new Exception('Razorpay credentials are not configured in Platform Settings.');
        }
        return new RazorpayApi($key, $secret);
    }

    /**
     * Create a Razorpay Order.
     * Returns the order details array.
     */
    public function createRazorpayOrder(int $amountInPaise, string $currency = 'USD', string $receipt = ''): array
    {
        $api = $this->getRazorpayClient();
        $order = $api->order->create([
            'amount' => $amountInPaise,
            'currency' => $currency,
            'receipt' => $receipt ?: ('receipt_' . now()->timestamp),
        ]);
        return $order->toArray();
    }

    /**
     * Verify a Razorpay payment signature.
     * Throws an exception if verification fails.
     */
    public function verifyRazorpayPayment(string $orderId, string $paymentId, string $signature): void
    {
        $api = $this->getRazorpayClient();
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
        ]);
    }

    /**
     * Fetch payment details from Razorpay.
     */
    public function fetchRazorpayPayment(string $paymentId): array
    {
        $api = $this->getRazorpayClient();
        return $api->payment->fetch($paymentId)->toArray();
    }
}
