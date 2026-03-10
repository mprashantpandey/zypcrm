@extends('layouts.public')

@section('title', config('app.name', 'LibrarySaaS') . ' | Study Library Management Platform')

@section('content')
<section class="section-space relative overflow-hidden bg-white">
    <div class="grid-pattern absolute inset-0 opacity-40"></div>
    <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-indigo-200/50 blur-3xl float-soft"></div>
    <div class="absolute -right-20 top-24 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl float-soft"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div class="fade-up reveal">
                <span class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] text-indigo-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Built for Study Libraries
                </span>

                <h1 class="mt-5 text-4xl font-extrabold leading-tight text-slate-900 sm:text-5xl lg:text-6xl font-display">
                    Run your library with
                    <span class="bg-gradient-to-r from-indigo-600 to-sky-500 bg-clip-text text-transparent">clarity, speed, and control.</span>
                </h1>

                <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                    Manage student admissions, attendance, seats, fee collection, and notices from one operational dashboard.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-primary inline-flex items-center gap-2 px-6 py-3 text-sm">
                        Start Free Trial
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </a>
                    <a href="{{ url('/pricing') }}" class="btn-secondary inline-flex items-center px-6 py-3 text-sm">
                        View Pricing
                    </a>
                </div>

                <div class="mt-8 grid max-w-md grid-cols-3 gap-3 text-center">
                    <div class="card-lift rounded-xl border border-slate-200 bg-white px-3 py-3">
                        <p class="text-xl font-extrabold text-slate-900">500+</p>
                        <p class="text-xs text-slate-500">Libraries</p>
                    </div>
                    <div class="card-lift rounded-xl border border-slate-200 bg-white px-3 py-3">
                        <p class="text-xl font-extrabold text-slate-900">120k+</p>
                        <p class="text-xs text-slate-500">Students</p>
                    </div>
                    <div class="card-lift rounded-xl border border-slate-200 bg-white px-3 py-3">
                        <p class="text-xl font-extrabold text-slate-900">99.9%</p>
                        <p class="text-xs text-slate-500">Uptime</p>
                    </div>
                </div>
            </div>

            <div class="fade-up delay-1 reveal">
                <div class="card-lift rounded-3xl border border-slate-200 bg-white p-4 shadow-xl">
                    <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 p-6 text-white">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm font-semibold text-indigo-200">Operations Snapshot</p>
                            <span class="inline-flex rounded-full bg-emerald-400/20 px-2 py-1 text-[11px] font-semibold text-emerald-200">Live</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <p class="text-xs text-indigo-200">Today Attendance</p>
                                <p class="mt-1 text-xl font-bold">93%</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <p class="text-xs text-indigo-200">Seat Utilization</p>
                                <p class="mt-1 text-xl font-bold">87%</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <p class="text-xs text-indigo-200">Pending Fees</p>
                                <p class="mt-1 text-xl font-bold">12</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <p class="text-xs text-indigo-200">New Leads</p>
                                <p class="mt-1 text-xl font-bold">08</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">Core Features</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl font-display">Everything needed to run daily operations</h2>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="fade-up reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="font-bold text-slate-900">Attendance</h3>
                <p class="mt-2 text-sm text-slate-600">Dedicated marking flow with check-in and checkout support.</p>
            </article>
            <article class="fade-up delay-1 reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2" /></svg>
                </div>
                <h3 class="font-bold text-slate-900">Fee Collection</h3>
                <p class="mt-2 text-sm text-slate-600">Invoices, payment status, receipts, and reminder-ready records.</p>
            </article>
            <article class="fade-up delay-1 reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9-5 9 5-9 5-9-5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12l-9 5-9-5" /></svg>
                </div>
                <h3 class="font-bold text-slate-900">Plans & Seats</h3>
                <p class="mt-2 text-sm text-slate-600">Manage time-based plans and keep seat usage optimized.</p>
            </article>
            <article class="fade-up delay-2 reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" /></svg>
                </div>
                <h3 class="font-bold text-slate-900">Notices</h3>
                <p class="mt-2 text-sm text-slate-600">Broadcast announcements to students and library staff instantly.</p>
            </article>
        </div>
    </div>
</section>

<section class="section-space bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-center">
            <div class="fade-up reveal">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">How it works</p>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl font-display">
                    A clear daily flow for owners and staff
                </h2>
                <p class="mt-4 text-sm leading-6 text-slate-600 sm:text-base">
                    From first enquiry to regular attendance, the system keeps your data consistent so your team spends
                    less time chasing spreadsheets and more time with students.
                </p>
            </div>

            <div class="fade-up delay-1 reveal space-y-4">
                <div class="card-lift rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Step 1</p>
                    <h3 class="mt-1 text-sm font-semibold text-slate-900">Capture and confirm admissions</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Register new students with the right plan, seat, and start date from one intake screen.
                    </p>
                </div>
                <div class="card-lift rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Step 2</p>
                    <h3 class="mt-1 text-sm font-semibold text-slate-900">Track attendance and seat usage</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Use dedicated flows for check‑in/out and keep a live view of who is inside and which seats are
                        free.
                    </p>
                </div>
                <div class="card-lift rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Step 3</p>
                    <h3 class="mt-1 text-sm font-semibold text-slate-900">Collect fees and send notices</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        Keep dues, receipts, and alerts in sync so parents and students always know what is pending.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-[2fr,1.3fr] lg:items-center">
            <div class="reveal text-slate-100">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-300">For library owners</p>
                <h2 class="mt-2 text-3xl font-extrabold sm:text-4xl font-display">
                    A system that matches the seriousness of your students.
                </h2>
                <p class="mt-4 text-sm leading-6 text-slate-300 sm:text-base">
                    Many study libraries start with notebooks and spreadsheets. As seats fill up, things break:
                    attendance is delayed, dues are unclear, and students lose trust. This platform is designed to
                    handle that scale with calm dashboards instead of chaos.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-900/60 px-3 py-1 text-xs font-medium text-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Built for multi‑branch growth
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-900/60 px-3 py-1 text-xs font-medium text-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                        Clean audit‑ready records
                    </div>
                </div>
            </div>
            <div class="reveal card-lift rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-[0_22px_60px_-40px_rgba(15,23,42,1)]">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Customer story</p>
                <blockquote class="mt-3 text-sm leading-6 text-slate-100">
                    “We moved 300+ active students from registers into this system. Within the first month our team
                    reduced daily admin time by over 2 hours and fee follow‑ups became dramatically easier.”
                </blockquote>
                <div class="mt-4 flex items-center gap-3 text-sm text-slate-300">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-500 text-xs font-bold text-white">
                        AK
                    </div>
                    <div>
                        <p class="font-semibold">Owner, 2‑branch study library</p>
                        <p class="text-xs text-slate-400">Customer since 2023</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ url('/pricing') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 hover:bg-indigo-400">
                        View plans and start
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
