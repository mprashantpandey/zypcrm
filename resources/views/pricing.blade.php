@extends('layouts.public')

@section('title', 'Pricing | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<section class="section-space relative overflow-hidden bg-white">
    <div class="grid-pattern absolute inset-0 opacity-35"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">Pricing</p>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl font-display">Simple plans for every library size</h1>
            <p class="mt-4 text-lg text-slate-600">Start with the plan that fits your current operations and upgrade anytime.</p>
        </div>

        <div class="mx-auto mt-12 grid max-w-md grid-cols-1 gap-6 lg:max-w-none lg:grid-cols-3">
            @forelse($plans as $plan)
                @php $isPopular = $loop->iteration === 2; @endphp
                <article class="card-lift reveal rounded-2xl border p-6 shadow-sm {{ $isPopular ? 'border-indigo-300 bg-indigo-600 text-white' : 'border-slate-200 bg-white' }}">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold {{ $isPopular ? 'text-white' : 'text-slate-900' }} font-display">{{ $plan->name }}</h2>
                        @if($isPopular)
                            <span class="inline-flex rounded-full bg-white/20 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Popular</span>
                        @endif
                    </div>

                    <p class="text-4xl font-extrabold {{ $isPopular ? 'text-white' : 'text-slate-900' }}">{{ $global_currency }}{{ number_format((float) $plan->price, 0) }}</p>
                    <p class="mt-1 text-sm {{ $isPopular ? 'text-indigo-100' : 'text-slate-500' }}">/{{ strtolower((string)($plan->billing_cycle ?? 'month')) }}</p>

                    <ul class="mt-6 space-y-2 text-sm {{ $isPopular ? 'text-indigo-50' : 'text-slate-600' }}">
                        @forelse((array) $plan->features as $feature)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 {{ $isPopular ? 'text-white' : 'text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>{{ is_array($feature) ? ($feature['name'] ?? 'Feature') : $feature }}</span>
                            </li>
                        @empty
                            <li>No feature list configured</li>
                        @endforelse
                    </ul>

                    <a href="{{ route('register') }}" class="mt-7 inline-flex w-full items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold {{ $isPopular ? 'bg-white text-indigo-700 hover:bg-indigo-50' : 'btn-primary' }}">
                        Choose Plan
                    </a>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">No pricing plans available yet.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="section-space bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">FAQ</p>
            <h2 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl font-display">
                Pricing questions, answered.
            </h2>
            <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">
                Clear plans with no surprise add‑ons. If you need something custom, you can always reach our team.
            </p>
        </div>

        <div class="mx-auto mt-10 max-w-3xl space-y-4">
            <div class="card-lift rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-semibold text-slate-900">Can I change my plan later?</p>
                <p class="mt-1 text-sm text-slate-600">
                    Yes. You can upgrade or downgrade between available plans. Changes apply from the next billing
                    cycle so your records stay consistent.
                </p>
            </div>
            <div class="card-lift rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-semibold text-slate-900">Is there a setup or onboarding fee?</p>
                <p class="mt-1 text-sm text-slate-600">
                    For most libraries there is no separate setup fee. If you need hands‑on migration support for
                    multiple branches, our team can share a one‑time onboarding quote.
                </p>
            </div>
            <div class="card-lift rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-semibold text-slate-900">What about student or seat limits?</p>
                <p class="mt-1 text-sm text-slate-600">
                    Each plan is designed with a realistic number of active students and seats. If you run larger
                    operations, we can extend limits on a custom agreement.
                </p>
            </div>
            <div class="card-lift rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-semibold text-slate-900">Do you offer discounts for multi‑branch setups?</p>
                <p class="mt-1 text-sm text-slate-600">
                    Yes, there are discounts for owners running multiple libraries on the same account. Reach out on
                    the contact page and we will share details for your case.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
