@extends('layouts.public')

@section('title', 'Razorpay Checkout')

@section('content')
<div class="py-12">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
            <h1 class="text-xl font-bold text-gray-900">Razorpay Checkout (optional)</h1>
            <p class="mt-2 text-sm text-gray-500">
                Plan: <span class="font-semibold text-gray-700">{{ $plan->name }}</span>
                <span class="mx-2 text-gray-300">|</span>
                Amount: <span class="font-semibold text-gray-700">{{ $global_currency }}{{ number_format($plan->price, 2) }}</span>
            </p>
            <p class="mt-1 text-xs text-gray-500">
                You can also collect this payment offline and record it manually in ZypCRM. Online payment is optional.
            </p>

            <form id="razorpay-verify-form" method="POST" action="{{ route('subscription.razorpay.verify', $plan) }}" class="hidden">
                @csrf
                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order['id'] }}">
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_signature" id="razorpay_signature">
            </form>

            <button id="pay-now-btn"
                class="mt-6 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                Pay online with Razorpay
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (function () {
        const button = document.getElementById('pay-now-btn');
        if (!button) return;

        const openCheckout = () => {
            const options = {
                key: @json($razorpayKeyId),
                amount: @json($order['amount']),
                currency: @json($order['currency']),
                name: @json(config('app.name')),
                description: @json('Subscription payment for ' . $plan->name),
                order_id: @json($order['id']),
                handler: function (response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_signature').value = response.razorpay_signature;
                    document.getElementById('razorpay-verify-form').submit();
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        };

        button.addEventListener('click', openCheckout);
        openCheckout();
    })();
</script>
@endpush
