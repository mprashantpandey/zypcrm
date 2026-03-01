<?php

namespace App\Livewire\Public;

use Livewire\Component;

class FeePayment extends Component
{
    public $fee;
    public $amount;
    public $transactionId;
    public $paymentMethod = 'online';

    public function mount($slug)
    {
        $this->fee = \App\Models\FeePayment::with(['user', 'tenant'])->where('slug', $slug)->firstOrFail();
        
        if ($this->fee->status === 'paid') {
            session()->flash('message', 'This fee has already been paid.');
        }

        $this->amount = $this->fee->amount;
    }

    public $razorpayOrder;
    public $razorpayKey;

    public function processPayment()
    {
        if ($this->fee->status === 'paid') {
            session()->flash('message', 'This fee has already been paid.');

            return;
        }

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $enablePlatformFee = filter_var($settings['enable_platform_fee_collection'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $platformFeePercent = (float) ($settings['platform_fee_percentage'] ?? 0);

        $platformFeeAmount = 0;
        $netAmount = $this->fee->amount;

        if ($enablePlatformFee && $platformFeePercent > 0) {
            $platformFeeAmount = round(($this->fee->amount * $platformFeePercent) / 100, 2);
            $netAmount = $this->fee->amount - $platformFeeAmount;
        }

        // Store these calculated values temporarily to be saved after successful verification
        session()->put('pending_fee_payment', [
            'fee_id' => $this->fee->id,
            'platform_fee_amount' => $platformFeeAmount,
            'net_amount' => $netAmount,
        ]);

        try {
            /** @var \App\Services\SubscriptionPaymentService $paymentService */
            $paymentService = app(\App\Services\SubscriptionPaymentService::class);
            $currency = \App\Models\Setting::getCurrencyCode('USD');
            
            $this->razorpayOrder = $paymentService->createRazorpayOrder(
                amountInPaise: (int) ($this->fee->amount * 100),
                currency: $currency,
                receipt: 'fee_' . $this->fee->slug
            );
            $this->razorpayKey = $settings['razorpay_key'] ?? env('RAZORPAY_KEY');

            if (empty($this->razorpayKey) || empty($settings['razorpay_secret'] ?? env('RAZORPAY_SECRET'))) {
                throw new \Exception('Razorpay gateway is not fully configured.');
            }

            $this->dispatch('initiate-razorpay', [
                'order' => $this->razorpayOrder,
                'key' => $this->razorpayKey,
                'name' => $this->fee->tenant->name ?? 'Library',
                'description' => 'Fee Payment for ' . ($this->fee->user->name ?? 'Student'),
                'email' => $this->fee->user->email ?? '',
                'contact' => $this->fee->user->phone ?? ''
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay checkout failed on public link: ' . $e->getMessage());
            session()->flash('error', 'Payment initialization failed. Please try again later.');
        }
    }

    public function verifyRazorpayPayment($paymentId, $orderId, $signature)
    {
        try {
            if ($this->fee->status === 'paid') {
                session()->flash('message', 'This fee has already been paid.');

                return;
            }

            /** @var \App\Services\SubscriptionPaymentService $paymentService */
            $paymentService = app(\App\Services\SubscriptionPaymentService::class);
            $paymentService->verifyRazorpayPayment($orderId, $paymentId, $signature);

            $pendingData = session()->pull('pending_fee_payment');

            $remarks = trim((string) $this->fee->remarks);
            if (! str_contains($remarks, '(Paid via Public Link)')) {
                $remarks = trim($remarks.' (Paid via Public Link)');
            }

            $this->fee->update([
                'status' => 'paid',
                'payment_method' => 'online',
                'transaction_id' => $paymentId,
                'platform_fee_amount' => $pendingData['platform_fee_amount'] ?? 0,
                'net_amount' => $pendingData['net_amount'] ?? $this->fee->amount,
                'remarks' => $remarks,
            ]);

            $this->transactionId = $paymentId;
            session()->flash('success', 'Payment successful! Transaction ID: ' . $this->transactionId);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay verification failed on public link: ' . $e->getMessage());
            session()->flash('error', 'Payment verification failed. Please contact support.');
        }
    }

    public function render()
    {
        return view('livewire.public.fee-payment')->layout('layouts.guest', [
            'header' => 'Fee Payment'
        ]);
    }
}
