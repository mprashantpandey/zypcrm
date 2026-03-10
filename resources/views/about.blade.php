@extends('layouts.public')

@section('title', 'About Us | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<section class="section-space relative overflow-hidden bg-white">
    <div class="grid-pattern absolute inset-0 opacity-35"></div>
    <div class="absolute -left-20 top-12 h-56 w-56 rounded-full bg-indigo-200/40 blur-3xl"></div>
    <div class="absolute right-0 top-20 h-52 w-52 rounded-full bg-sky-200/35 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1.4fr,1.6fr] lg:items-center">
            <div class="fade-up reveal">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">About ZypCRM</p>
                <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl font-display">
                    Built for serious library operations.
                </h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                    We help study libraries run admissions, attendance, fee collection, seats, and communication in one
                    clean workflow so owners can focus on growth and student experience.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ url('/pricing') }}" class="btn-primary inline-flex items-center px-5 py-3 text-sm">
                        Explore Plans
                    </a>
                    <a href="{{ url('/contact') }}" class="btn-secondary inline-flex items-center px-5 py-3 text-sm">
                        Talk to Sales
                    </a>
                </div>
            </div>

            <div class="fade-up delay-1 reveal card-lift rounded-3xl border border-slate-200 bg-white p-6 shadow-lg">
                <h2 class="text-xl font-bold text-slate-900 font-display">Why teams choose us</h2>
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <article class="card-lift rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <div>
                                <h3 class="font-semibold text-slate-900">Owner-first workflows</h3>
                                <p class="mt-1 text-sm text-slate-600">Screens mirror how study libraries actually operate day to day.</p>
                            </div>
                        </div>
                    </article>
                    <article class="card-lift rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999A5.002 5.002 0 006 10H5a2 2 0 00-2 2v3z" /></svg>
                            </span>
                            <div>
                                <h3 class="font-semibold text-slate-900">Reliable cloud setup</h3>
                                <p class="mt-1 text-sm text-slate-600">Secure access from web and app with consistent, backed‑up data.</p>
                            </div>
                        </div>
                    </article>
                    <article class="card-lift rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-8H7v8m10 0H7" /></svg>
                            </span>
                            <div>
                                <h3 class="font-semibold text-slate-900">Built for multi-library growth</h3>
                                <p class="mt-1 text-sm text-slate-600">Support for multiple branches, teams, and roles from day one.</p>
                            </div>
                        </div>
                    </article>
                    <article class="card-lift rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <div>
                                <h3 class="font-semibold text-slate-900">Fast implementation</h3>
                                <p class="mt-1 text-sm text-slate-600">Onboard your first branch in days, not months.</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <div class="mt-14 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 text-center">
                <p class="text-3xl font-extrabold text-slate-900">500+</p>
                <p class="mt-1 text-sm text-slate-500">Libraries Powered</p>
            </div>
            <div class="reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 text-center">
                <p class="text-3xl font-extrabold text-slate-900">120k+</p>
                <p class="mt-1 text-sm text-slate-500">Active Students</p>
            </div>
            <div class="reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 text-center">
                <p class="text-3xl font-extrabold text-slate-900">99.9%</p>
                <p class="mt-1 text-sm text-slate-500">Platform Uptime</p>
            </div>
            <div class="reveal card-lift rounded-2xl border border-slate-200 bg-white p-5 text-center">
                <p class="text-3xl font-extrabold text-slate-900">24/7</p>
                <p class="mt-1 text-sm text-slate-500">Operations Visibility</p>
            </div>
        </div>
    </div>
</section>
@endsection
