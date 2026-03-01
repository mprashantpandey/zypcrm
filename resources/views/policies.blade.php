@extends('layouts.public')

@section('title', 'Policies | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<div class="bg-gray-50 px-6 py-32 lg:px-8">
    <div class="mx-auto max-w-3xl bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100">
        <div class="text-base leading-7 text-gray-700">
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl mb-8"
                style="font-family: 'Outfit', sans-serif;">Legal Policies & Terms</h1>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4" style="font-family: 'Outfit', sans-serif;">Terms of
                Service</h2>
            <p class="mb-4">By accessing or using our platform, you agree to be bound by these Terms. If you disagree
                with any part of the terms, then you may not access the service.</p>
            <p class="mb-4">Our service enables library owners to manage their internal operations. You are responsible
                for ensuring your use of our service is compliant with local laws and regulations regarding student data
                management.</p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4" style="font-family: 'Outfit', sans-serif;">Privacy
                Policy</h2>
            <p class="mb-4">We respect your privacy and are committed to protecting it. Our Privacy Policy governs your
                visit to this site and explains how we collect, safeguard and disclose information that results from
                your use of our Service.</p>
            <p class="mb-4">We only collect information necessary to provide and improve our service. We do not sell
                your personal data or your students' personal data to third parties.</p>

            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4" style="font-family: 'Outfit', sans-serif;">Refund &
                Cancellation</h2>
            <p class="mb-4">You can cancel your subscription at any time. If you cancel before the end of your billing
                cycle, you will retain access to the platform until that cycle ends. We do not provide prorated refunds
                for mid-cycle cancellations.</p>

            <p class="mt-12 text-sm text-gray-500">Last updated: {{ date('F d, Y') }}</p>
        </div>
    </div>
</div>
@endsection