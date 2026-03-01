<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50/50">
    <div
        class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden">

        <!-- Decorative Header -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

        <div class="text-center">
            <div
                class="mx-auto h-16 w-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
            </div>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-gray-900">
                Fee Invoice
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                {{ $fee->tenant->name ?? 'Library' }}
            </p>
        </div>

        @if (session()->has('message'))
        <div class="rounded-xl bg-blue-50 p-4 border border-blue-100">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800 font-medium">{{ session('message') }}</p>
                </div>
            </div>
        </div>
        @elseif (session()->has('success'))
        <div class="rounded-xl bg-green-50 p-4 border border-green-100 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-green-800">Payment Successful</h3>
            <p class="mt-2 text-sm text-green-700">{{ session('success') }}</p>
            <p class="mt-4 text-xs text-green-600 font-mono bg-green-100 py-2 rounded-lg break-all">Ref: {{
                $transactionId }}</p>

            <div class="mt-6">
                <a href="/" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                    Return to site &rarr;
                </a>
            </div>
        </div>
        @endif

        @if ($fee->status !== 'paid' && !session()->has('success'))
        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 space-y-4">
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <span class="text-sm text-gray-500">Student Name</span>
                <span class="text-sm font-semibold text-gray-900">{{ $fee->user->name ?? 'Unknown Student' }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <span class="text-sm text-gray-500">Date</span>
                <span class="text-sm font-semibold text-gray-900">{{
                    \Carbon\Carbon::parse($fee->payment_date)->format('M d, Y') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <span class="text-sm text-gray-500">Status</span>
                <span
                    class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800 capitalize">{{
                    $fee->status }}</span>
            </div>
            <div class="flex justify-between items-center pt-2">
                <span class="text-base font-medium text-gray-900">Total Amount</span>
                <span class="text-2xl font-bold text-indigo-600">{{ $global_currency }}{{ number_format($amount, 2)
                    }}</span>
            </div>
        </div>

        <form wire:submit="processPayment" class="mt-8 space-y-6">
            <button type="submit" wire:loading.attr="disabled"
                class="group relative flex w-full justify-center rounded-xl bg-indigo-600 px-3 py-3.5 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 shadow-md transition-all active:scale-[0.98]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg wire:loading.remove class="h-5 w-5 text-indigo-300 group-hover:text-indigo-400" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <svg wire:loading class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
                <span wire:loading.remove>Pay {{ $global_currency }}{{ number_format($amount, 2) }} Securely</span>
                <span wire:loading>Initializing...</span>
            </button>

            <p class="text-center text-xs text-gray-500 flex items-center justify-center gap-1 mt-4">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                Payments are securely processed.
            </p>
        </form>
        @endif

    </div>

    @push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('initiate-razorpay', (data) => {
                const config = data[0];
                const options = {
                    "key": config.key,
                    "amount": config.order.amount,
                    "currency": config.order.currency,
                    "name": config.name,
                    "description": config.description,
                    "order_id": config.order.id,
                    "prefill": {
                        "name": config.name,
                        "email": config.email,
                        "contact": config.contact
                    },
                    "theme": {
                        "color": "#4f46e5"
                    },
                    "handler": function (response) {
                        @this.verifyRazorpayPayment(
                            response.razorpay_payment_id,
                            response.razorpay_order_id,
                            response.razorpay_signature
                        );
                    }
                };

                const rzp = new Razorpay(options);

                rzp.on('payment.failed', function (response) {
                    console.error('Payment Failed:', response.error);
                });

                rzp.open();
            });
        });
    </script>
    @endpush
</div>