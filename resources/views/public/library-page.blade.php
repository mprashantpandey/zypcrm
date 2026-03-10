@extends('layouts.library-public')

@section('title', ($tenant->name ?? 'Library').' | '.($globalSettings['app_name'] ?? config('app.name')))

@section('content')
@php
    $brandName = $globalSettings['app_name'] ?? config('app.name', 'ZypCRM');
    $librarySlug = $tenant->public_slug ?: \Illuminate\Support\Str::slug($tenant->name);
    $lightboxItems = (isset($images) ? $images : collect())->map(fn ($image) => [
        'url' => \Illuminate\Support\Facades\Storage::url($image->image_path),
        'caption' => $image->caption ?: ($tenant->name.' image'),
    ])->values()->all();
@endphp

<div class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">
    <section class="relative overflow-hidden border-b border-white/10">
        <div class="pattern-grid absolute inset-0 opacity-20"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(99,102,241,0.35),_transparent_40%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_rgba(14,165,233,0.20),_transparent_45%)]"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-16">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-center">
                <div class="reveal-up">
                    <span class="inline-flex items-center rounded-full border border-indigo-300/30 bg-indigo-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-100">
                        {{ $tenant->status === 'active' ? 'Open for Enquiry' : 'Library Profile' }}
                    </span>
                    <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ $tenant->name }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">
                        {{ $tenant->public_description ?: 'Focused study spaces with structured timings, clear plans, and reliable student support.' }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @if(!empty($tenant->phone))
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $tenant->phone) }}"
                                class="inline-flex items-center rounded-xl bg-indigo-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:bg-indigo-400">
                                Call Library
                            </a>
                        @endif
                        @if(!empty($tenant->email))
                            <a href="mailto:{{ $tenant->email }}"
                                class="inline-flex items-center rounded-xl border border-white/25 bg-white/5 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:bg-white/10">
                                Email Library
                            </a>
                        @endif
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-100">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Verified listing
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-100">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                            Flexible timings
                        </span>
                    </div>
                </div>

                <div class="reveal-up delay-1 rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur sm:p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-200">Quick Details</h2>
                    <dl class="mt-4 space-y-4">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <dt class="text-xs uppercase tracking-wide text-slate-400">Address</dt>
                            <dd class="mt-1 text-sm text-slate-100">{{ $tenant->address ?: 'Address not available' }}</dd>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Phone</dt>
                                <dd class="mt-1 text-sm font-medium text-slate-100">{{ $tenant->phone ?: 'Not provided' }}</dd>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Email</dt>
                                <dd class="mt-1 truncate text-sm font-medium text-slate-100">{{ $tenant->email ?: 'Not provided' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-indigo-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0H9m6 0v6"/></svg>
                        Membership Plans
                    </p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Plans & Timings</h2>
                </div>
                <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                    {{ $plans->count() }} active plan{{ $plans->count() === 1 ? '' : 's' }}
                </span>
            </div>

            @if($plans->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-600">
                    No active plans published yet. Contact the library for details.
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($plans as $plan)
                        @php
                            $start = $plan->start_time ? \Carbon\Carbon::parse($plan->start_time)->format('h:i A') : 'Flexible';
                            $end = $plan->end_time ? \Carbon\Carbon::parse($plan->end_time)->format('h:i A') : 'Anytime';
                        @endphp
                        <article class="reveal-up rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9-5 9 5-9 5-9-5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12l-9 5-9-5"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $plan->name }}</h3>
                            <p class="mt-3 text-3xl font-extrabold text-slate-900">
                                {{ $globalSettings['currency_symbol'] ?? '₹' }}{{ number_format((float) $plan->price, 0) }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">for {{ $plan->duration_days }} day{{ $plan->duration_days == 1 ? '' : 's' }}</p>
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                                {{ $start }} - {{ $end }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if(isset($images) && $images->isNotEmpty())
    <section class="border-y border-slate-200 bg-slate-50 py-14 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
            x-data="{
                open: false,
                index: 0,
                items: @js($lightboxItems),
                touchStartX: null,
                touchEndX: null,
                pointerStartX: null,
                pointerEndX: null,
                openAt(i) {
                    this.index = i;
                    this.open = true;
                    document.body.classList.add('overflow-hidden');
                },
                close() {
                    this.open = false;
                    document.body.classList.remove('overflow-hidden');
                },
                next() {
                    if (!this.items.length) return;
                    this.index = (this.index + 1) % this.items.length;
                },
                prev() {
                    if (!this.items.length) return;
                    this.index = (this.index - 1 + this.items.length) % this.items.length;
                },
                onTouchStart(e) { this.touchStartX = e.changedTouches[0].screenX; },
                onTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    const delta = this.touchEndX - this.touchStartX;
                    if (Math.abs(delta) < 40) return;
                    if (delta < 0) this.next();
                    if (delta > 0) this.prev();
                },
                onPointerDown(e) { this.pointerStartX = e.clientX; },
                onPointerUp(e) {
                    this.pointerEndX = e.clientX;
                    const delta = this.pointerEndX - this.pointerStartX;
                    if (Math.abs(delta) < 50) return;
                    if (delta < 0) this.next();
                    if (delta > 0) this.prev();
                }
            }"
            x-on:keydown.window.escape="if (open) close()"
            x-on:keydown.window.arrow-right="if (open) next()"
            x-on:keydown.window.arrow-left="if (open) prev()">

            <div class="mb-7 flex items-end justify-between gap-3">
                <div>
                    <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-indigo-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4 4 4 8-8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16"/></svg>
                        Gallery
                    </p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Library Images</h2>
                </div>
                <span class="inline-flex rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                    {{ $images->count() }} photos
                </span>
            </div>

            @if($images->count() === 1)
                @php $single = $images->first(); @endphp
                <div class="mx-auto w-full max-w-3xl xl:max-w-2xl">
                    <button type="button" @click="openAt(0)"
                        class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-sm transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <div class="aspect-[16/10] w-full max-h-[340px] bg-gradient-to-br from-slate-100 to-slate-200 p-3 sm:p-4">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($single->image_path) }}"
                                alt="{{ $single->caption ?: ($tenant->name.' image') }}"
                                class="h-full w-full rounded-xl object-contain object-center" loading="lazy">
                        </div>
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($images as $image)
                        <button type="button" @click="openAt({{ $loop->index }})"
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-sm transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div class="aspect-[4/3] bg-gradient-to-br from-slate-100 to-slate-200 p-2.5">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}"
                                    alt="{{ $image->caption ?: ($tenant->name.' image') }}"
                                    class="h-full w-full rounded-lg object-contain object-center" loading="lazy">
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif

            <div x-show="open" x-cloak style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 p-3 sm:p-6"
                @click.self="close()">
                <button type="button" @click="close()"
                    class="absolute right-3 top-3 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/25 sm:right-6 sm:top-6">
                    Close
                </button>
                <button type="button" @click="prev()"
                    class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white/15 px-3 py-2 text-white hover:bg-white/25 sm:left-6">
                    &#10094;
                </button>
                <button type="button" @click="next()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white/15 px-3 py-2 text-white hover:bg-white/25 sm:right-6">
                    &#10095;
                </button>
                <div class="w-full max-w-5xl"
                    @touchstart.passive="onTouchStart($event)"
                    @touchend.passive="onTouchEnd($event)"
                    @mousedown="onPointerDown($event)"
                    @mouseup="onPointerUp($event)">
                    <div class="mx-auto overflow-hidden rounded-2xl border border-white/20 bg-black">
                        <img :src="items[index]?.url ?? ''" :alt="items[index]?.caption ?? 'Library image'"
                            class="max-h-[78vh] w-full object-contain">
                    </div>
                    <p class="mt-3 text-center text-sm text-slate-200" x-text="items[index]?.caption ?? ''"></p>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="bg-white py-14 sm:py-16">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="reveal-up rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Weekly Operating Hours</h3>
                <div class="mt-4 space-y-2">
                    @php $hours = is_array($tenant->operating_hours) ? $tenant->operating_hours : []; @endphp
                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                        @php
                            $window = $hours[$day] ?? null;
                            $closed = (bool) ($window['closed'] ?? true);
                            $openRaw = $window['open'] ?? null;
                            $closeRaw = $window['close'] ?? null;
                            $open = (is_string($openRaw) && preg_match('/^\d{2}:\d{2}$/', $openRaw)) ? \Carbon\Carbon::createFromFormat('H:i', $openRaw)->format('h:i A') : '--';
                            $close = (is_string($closeRaw) && preg_match('/^\d{2}:\d{2}$/', $closeRaw)) ? \Carbon\Carbon::createFromFormat('H:i', $closeRaw)->format('h:i A') : '--';
                        @endphp
                        <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2">
                            <span class="text-sm font-medium capitalize text-slate-800">{{ $day }}</span>
                            <span class="text-sm {{ $closed ? 'text-rose-600' : 'text-slate-600' }}">{{ $closed ? 'Closed' : ($open.' - '.$close) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="reveal-up delay-1 rounded-2xl border border-indigo-200 bg-indigo-50/50 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Send Inquiry</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Share your details and library team will contact you soon.
                </p>

                @if (session('success'))
                    <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.library.contact', ['slug' => $librarySlug]) }}" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="form_started_at" value="{{ now()->timestamp }}">
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                        <textarea name="message" rows="4"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 sm:w-auto">
                        Submit Inquiry
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="border-t border-white/10 bg-slate-950">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-6 text-xs text-slate-400 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} {{ $tenant->name }}. All rights reserved.</p>
            <p>Powered by <a href="{{ url('/') }}" class="font-semibold text-slate-200 underline decoration-slate-500/60 underline-offset-2 hover:text-white">{{ $brandName }}</a></p>
        </div>
    </footer>
</div>
@endsection
