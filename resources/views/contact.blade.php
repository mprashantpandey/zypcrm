@extends('layouts.public')

@section('title', 'Contact Us | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<section class="section-space relative isolate overflow-hidden bg-white">
    <div class="grid-pattern absolute inset-0 opacity-35"></div>
    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
        <div
            class="relative left-[calc(50%-8rem)] aspect-[1155/678] w-[30rem] -translate-x-1/2 rotate-[35deg] bg-gradient-to-tr from-indigo-200 to-sky-400 opacity-25 sm:left-[calc(50%-30rem)] sm:w-[70rem]">
        </div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center fade-up">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">Contact</p>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl font-display">Talk to our team</h1>
            <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">
                Questions about pricing, onboarding, or implementation? Share your details and we will reach out.
            </p>
        </div>

        <div class="mx-auto mt-12 grid max-w-6xl grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="fade-up reveal card-lift rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
                <h2 class="text-xl font-bold text-slate-900 font-display">Contact Info</h2>
                <div class="mt-6 space-y-4 text-sm text-slate-600">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Email</p>
                            <p>support@zypcrm.com</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l.4 2M7 13h10l4-8H5.4m1.6 8l-1 5h14m-10 0a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z" /></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Sales</p>
                            <p>Mon-Sat, 10:00 AM - 7:00 PM</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0L6.343 16.657a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Coverage</p>
                            <p>India-first, global-ready SaaS onboarding</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="#" method="POST" class="fade-up delay-1 reveal card-lift rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <div>
                        <label for="first-name" class="block text-sm font-semibold text-slate-900">First name</label>
                        <input type="text" name="first-name" id="first-name" autocomplete="given-name"
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label for="last-name" class="block text-sm font-semibold text-slate-900">Last name</label>
                        <input type="text" name="last-name" id="last-name" autocomplete="family-name"
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-semibold text-slate-900">Email</label>
                        <input type="email" name="email" id="email" autocomplete="email"
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="phone-number" class="block text-sm font-semibold text-slate-900">Phone number</label>
                        <input type="tel" name="phone-number" id="phone-number" autocomplete="tel"
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="message" class="block text-sm font-semibold text-slate-900">Message</label>
                        <textarea name="message" id="message" rows="5"
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-500">We usually respond within one business day.</p>
                    <button type="submit" onclick="event.preventDefault(); alert('Message sent successfully!');"
                        class="btn-primary inline-flex items-center px-5 py-2.5 text-sm">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
