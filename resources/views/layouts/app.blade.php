<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $globalSettings['app_name'] ?? config('app.name', 'LibrarySaaS') }}</title>

    @php
        $faviconUrl = !empty($global_favicon)
            ? Storage::url($global_favicon)
            : (!empty($globalSettings['light_logo']) ? Storage::url($globalSettings['light_logo']) : asset('favicon.ico'));
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important;}</style>
</head>

<body class="antialiased bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white"
    style="font-family: 'Manrope', sans-serif;">
    <!-- Navigation (Sidebar & Topbar) -->
    <livewire:layout.navigation />

    <!-- Main Content Area -->
    <div class="md:pl-64 flex flex-col min-h-screen pt-16 transition-all duration-300">
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <div
        x-data="{
            toasts: [],
            showTour: false,
            tourTitle: 'Quick Tour',
            tourSteps: [],
            tourIndex: 0,
            push(type, message) {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, type: type || 'success', message: message || 'Done' });
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4200);
            },
            startTour(payload) {
                this.tourTitle = payload?.title || 'Quick Tour';
                this.tourSteps = Array.isArray(payload?.steps) ? payload.steps : [];
                this.tourIndex = 0;
                this.showTour = this.tourSteps.length > 0;
            },
            nextTour() {
                if (this.tourIndex + 1 < this.tourSteps.length) {
                    this.tourIndex++;
                    return;
                }
                this.showTour = false;
            }
        }"
        x-init="
            window.addEventListener('notify', (e) => {
                const d = e.detail || {};
                push(d.type || d[0]?.type, d.message || d[0]?.message);
            });
            window.addEventListener('start-tour', (e) => startTour(e.detail || {}));
        "
        class="pointer-events-none fixed inset-x-0 top-0 z-[70] flex flex-col items-end gap-2 p-4 sm:p-6">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl border bg-white shadow-lg"
                 :class="toast.type === 'error' ? 'border-rose-200' : (toast.type === 'warning' ? 'border-amber-200' : 'border-emerald-200')">
                <div class="flex items-start justify-between gap-3 p-4">
                    <div class="text-sm" :class="toast.type === 'error' ? 'text-rose-700' : (toast.type === 'warning' ? 'text-amber-700' : 'text-emerald-700')" x-text="toast.message"></div>
                    <button type="button" class="text-xs font-semibold text-slate-500 hover:text-slate-700" @click="toasts = toasts.filter(t => t.id !== toast.id)">Close</button>
                </div>
            </div>
        </template>

        <div x-show="showTour" x-cloak class="pointer-events-auto fixed inset-0 z-[80] flex items-end justify-center bg-slate-900/35 p-4 sm:items-center" @click.self="showTour = false">
            <div class="w-full max-w-lg rounded-2xl border border-indigo-200 bg-white p-5 shadow-2xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600" x-text="tourTitle"></p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">Guided Walkthrough</h3>
                    </div>
                    <button type="button" class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100" @click="showTour = false">Close</button>
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-700" x-text="tourSteps[tourIndex] || ''"></p>
                <div class="mt-5 flex items-center justify-between">
                    <p class="text-xs text-slate-500"><span x-text="tourIndex + 1"></span>/<span x-text="tourSteps.length"></span></p>
                    <div class="flex gap-2">
                        <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" @click="tourIndex = Math.max(0, tourIndex - 1)">Back</button>
                        <button type="button" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500" @click="nextTour()" x-text="tourIndex + 1 < tourSteps.length ? 'Next' : 'Finish'"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(app()->environment('production'))
        @if(!empty($globalSettings['analytics_custom_js']))
            {!! $globalSettings['analytics_custom_js'] !!}
        @endif

        @if(!empty($globalSettings['tawkto_embed_code']))
            {!! $globalSettings['tawkto_embed_code'] !!}
        @endif
    @endif
</body>

</html>
