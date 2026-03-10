@extends('layouts.public')

@section('title', 'Policies | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<section class="section-space bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="reveal card-lift rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">Legal</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl font-display">Terms & Privacy</h1>
            <p class="mt-3 text-sm text-slate-500">Last updated: {{ date('F d, Y') }}</p>

            <div class="mt-8 space-y-8 text-sm leading-7 text-slate-600 sm:text-base">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 font-display">Terms of Service</h2>
                    <p class="mt-3">
                        By accessing or using this platform, you agree to follow these terms and all applicable laws.
                        If you do not agree, please discontinue use of the service.
                    </p>
                    <p class="mt-3">
                        The platform is intended for library operations such as admissions, attendance, fee tracking,
                        and student communication. Each customer is responsible for lawful and compliant data handling.
                    </p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-slate-900 font-display">Privacy Policy</h2>
                    <p class="mt-3">
                        We collect only the information required to deliver and improve the service. We do not sell
                        personal data to third parties.
                    </p>
                    <p class="mt-3">
                        Reasonable technical and organizational controls are used to secure data, but users should also
                        maintain strong credentials and responsible access management.
                    </p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-slate-900 font-display">Refund & Cancellation</h2>
                    <p class="mt-3">
                        Subscriptions can be cancelled anytime. Access remains available until the current billing cycle
                        ends. Partial refunds for mid-cycle cancellation are generally not provided unless required by law.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
