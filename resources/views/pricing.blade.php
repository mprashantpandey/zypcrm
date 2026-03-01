@extends('layouts.public')

@section('title', 'Pricing | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<div class="bg-gray-50 py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-base font-semibold leading-7 text-indigo-600 tracking-wide uppercase">Pricing</h2>
            <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl"
                style="font-family: 'Outfit', sans-serif;">
                Simple, transparent pricing
            </p>
        </div>
        <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-600">
            Choose the plan that fits your library's size. No hidden fees. Elevate your library management today.
        </p>

        <div
            class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-y-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-x-8 xl:gap-x-12">
            @forelse ($plans as $plan)
            @php
            // Highlight the middle plan typically, or check a custom 'is_popular' flag if it existed.
            // Here we'll just emphasize the second plan visually if it exists, or just style them evenly.
            $isPopular = $loop->iteration === 2;
            @endphp
            <div
                class="rounded-3xl p-8 xl:p-10 {{ $isPopular ? 'bg-gray-900 ring-2 ring-indigo-600 text-white shadow-2xl scale-105 transform z-10' : 'bg-white ring-1 ring-gray-200 text-gray-900 hover:shadow-xl transition-shadow' }}">
                <div class="flex items-center justify-between gap-x-4">
                    <h3 id="tier-{{ str()->slug($plan->name) }}"
                        class="text-2xl font-bold leading-8 {{ $isPopular ? 'text-white' : 'text-gray-900' }}"
                        style="font-family: 'Outfit', sans-serif;">
                        {{ $plan->name }}
                    </h3>
                    @if($isPopular)
                    <p
                        class="rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs font-semibold leading-5 text-indigo-400">
                        Most popular</p>
                    @endif
                </div>
                <p class="mt-4 text-sm leading-6 {{ $isPopular ? 'text-gray-300' : 'text-gray-600' }}">
                    {{ current((array)$plan->features) ? 'Everything you need to manage ' .
                    current((array)$plan->features) : 'Perfect for growing study libraries.' }}
                </p>
                <p class="mt-6 flex items-baseline gap-x-1">
                    <span
                        class="text-4xl font-bold tracking-tight text-transparent bg-clip-text {{ $isPopular ? 'bg-gradient-to-r from-indigo-200 to-white' : 'bg-gradient-to-r from-gray-900 to-gray-600' }}">{{ $global_currency }}{{
                        number_format($plan->price, 0) }}</span>
                    <span class="text-sm font-semibold leading-6 {{ $isPopular ? 'text-gray-300' : 'text-gray-600' }}">/
                        @php
                        $cycle = strtolower((string)($plan->billing_cycle ?? ''));
                        if ($cycle === 'monthly') {
                        echo 'month';
                        } elseif ($cycle === 'yearly') {
                        echo 'year';
                        } elseif ($cycle === 'weekly') {
                        echo 'week';
                        } elseif ($cycle === 'daily') {
                        echo 'day';
                        } else {
                        echo 'month';
                        }
                        @endphp
                    </span>
                </p>
                <a href="{{ route('register') }}" aria-describedby="tier-{{ str()->slug($plan->name) }}"
                    class="mt-6 block rounded-xl px-3 py-3 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 {{ $isPopular ? 'bg-indigo-500 text-white hover:bg-indigo-400 focus-visible:outline-indigo-500' : 'bg-indigo-600 text-white hover:bg-indigo-500 focus-visible:outline-indigo-600' }}">
                    Get started today
                </a>

                @if($plan->features && is_array($plan->features))
                <div class="mt-8">
                    <h4 class="text-sm font-semibold leading-6 {{ $isPopular ? 'text-white' : 'text-indigo-600' }}">
                        What's included</h4>
                    <ul role="list"
                        class="mt-4 space-y-3 text-sm leading-6 {{ $isPopular ? 'text-gray-300' : 'text-gray-600' }}">
                        @foreach($plan->features as $feature)
                        <li class="flex gap-x-3">
                            <svg class="h-6 w-5 flex-none {{ $isPopular ? 'text-indigo-400' : 'text-indigo-600' }}"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ is_array($feature) ? ($feature['name'] ?? 'Feature') : $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No pricing plans available yet. Please check back later.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="bg-white py-24 sm:py-32 border-t border-gray-100">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:text-center">
            <h2 class="text-base font-semibold leading-7 text-indigo-600 tracking-wide uppercase">FAQ</h2>
            <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                style="font-family: 'Outfit', sans-serif;">
                Frequently asked questions
            </p>
            <p class="mt-6 text-lg leading-8 text-gray-600">
                Can't find the answer you're looking for? Reach out to our customer support team.
            </p>
        </div>
        <div class="mx-auto mt-16 max-w-2xl divide-y divide-gray-900/10">
            <dl class="mt-10 space-y-6 divide-y divide-gray-900/10">
                <div class="pt-6">
                    <dt>
                        <span class="text-lg font-semibold leading-7 text-gray-900"
                            style="font-family: 'Outfit', sans-serif;">Can I upgrade or downgrade my plan?</span>
                    </dt>
                    <dd class="mt-2 text-base leading-7 text-gray-600">
                        Yes, you can upgrade your plan at any time to unlock more features or handle more seats. We
                        prorate all plan changes automatically.
                    </dd>
                </div>
                <!-- More questions... -->
                <div class="pt-6">
                    <dt>
                        <span class="text-lg font-semibold leading-7 text-gray-900"
                            style="font-family: 'Outfit', sans-serif;">How does the 14-day free trial work?</span>
                    </dt>
                    <dd class="mt-2 text-base leading-7 text-gray-600">
                        You'll get full access to all features for 14 days without entering a credit card. At the end of
                        the trial, simply select your preferred tier to continue.
                    </dd>
                </div>
                <div class="pt-6">
                    <dt>
                        <span class="text-lg font-semibold leading-7 text-gray-900"
                            style="font-family: 'Outfit', sans-serif;">Is payment processing secure?</span>
                    </dt>
                    <dd class="mt-2 text-base leading-7 text-gray-600">
                        Absolutely. We integrate directly with Razorpay and Stripe to ensure bank-level encryption. We
                        never store credit card or payment information on our own servers.
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
