<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'LibrarySaaS'))</title>

    @if(!empty($global_favicon))
        <link rel="icon" href="{{ Storage::url($global_favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --brand-primary: {{ $globalSettings['primary_color'] ?? '#4f46e5' }};
        }
        [x-cloak] { display: none !important; }
        body { font-family: 'Manrope', sans-serif; }
        .font-display { font-family: 'Outfit', sans-serif; }
        .section-space { padding-top: 5rem; padding-bottom: 5rem; }
        @media (min-width: 640px) {
            .section-space { padding-top: 6rem; padding-bottom: 6rem; }
        }
        .grid-pattern {
            background-image:
                linear-gradient(to right, rgba(148, 163, 184, .16) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(148, 163, 184, .16) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .fade-up { animation: fadeUp .65s ease both; }
        .fade-up.delay-1 { animation-delay: .08s; }
        .fade-up.delay-2 { animation-delay: .16s; }
        .float-soft { animation: floatSoft 6s ease-in-out infinite; }
        .reveal {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .55s ease, transform .55s ease;
        }
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .card-lift {
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .card-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px -20px rgba(15, 23, 42, .38);
            border-color: rgba(99, 102, 241, .35);
        }
        .nav-link {
            color: rgba(226, 232, 240, .9);
            position: relative;
            transition: color .2s ease;
        }
        .nav-link:hover { color: #ffffff; }
        .nav-link::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -8px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, #818cf8 0%, #38bdf8 100%);
            transform: scaleX(0);
            transform-origin: center;
            transition: transform .2s ease;
        }
        .nav-link:hover::after { transform: scaleX(1); }
        .btn-primary {
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #fff;
            font-weight: 700;
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .btn-primary:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
            box-shadow: 0 14px 26px -16px rgba(79, 70, 229, .95);
        }
        .btn-secondary {
            border-radius: 0.75rem;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            font-weight: 600;
            transition: transform .15s ease, border-color .2s ease, background .2s ease;
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes floatSoft {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white">
    <nav x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/70 bg-white/85 backdrop-blur-lg">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                @php
                    $hasDarkLogo = !empty($globalSettings['dark_logo']) && file_exists(storage_path('app/public/'.$globalSettings['dark_logo']));
                    $hasLightLogo = !empty($globalSettings['light_logo']) && file_exists(storage_path('app/public/'.$globalSettings['light_logo']));
                    $appName = $globalSettings['app_name'] ?? config('app.name');
                @endphp
                @if($hasLightLogo)
                    <img src="{{ Storage::url($globalSettings['light_logo']) }}" alt="{{ $appName }}" class="h-10 w-auto">
                @elseif($hasDarkLogo)
                    <img src="{{ Storage::url($globalSettings['dark_logo']) }}" alt="{{ $appName }}" class="h-10 w-auto">
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-lg font-bold text-white font-display">{{ strtoupper(substr($appName, 0, 1)) }}</div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 font-display">{{ $appName }}</span>
                @endif
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600">Home</a>
                <a href="{{ url('/pricing') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600">Pricing</a>
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600">About</a>
                <a href="{{ url('/contact') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600">Contact</a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-indigo-300/40 hover:bg-indigo-700">Get library system</a>
                @endauth
            </div>

            <button @click="open = !open" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-700 md:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
        </div>

        <div x-show="open" x-cloak class="border-t border-slate-200 bg-white p-3 md:hidden">
            <div class="space-y-1">
                <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Home</a>
                <a href="{{ url('/pricing') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Pricing</a>
                <a href="{{ url('/about') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">About</a>
                <a href="{{ url('/contact') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Contact</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-center text-sm font-semibold text-slate-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-center text-sm font-semibold text-slate-700">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-3 py-2 text-center text-sm font-bold text-white">Start</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="min-h-screen pt-20">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-3">
                <div>
                    <p class="text-lg font-bold text-white font-display">{{ $globalSettings['app_name'] ?? config('app.name') }}</p>
                    <p class="mt-3 text-sm leading-6 text-slate-400">A modern operating platform for study libraries with attendance, seats, fees, and communication workflows.</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Quick Links</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <a href="{{ url('/') }}" class="rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-300 hover:border-slate-700 hover:text-white">Home</a>
                        <a href="{{ url('/pricing') }}" class="rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-300 hover:border-slate-700 hover:text-white">Pricing</a>
                        <a href="{{ url('/about') }}" class="rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-300 hover:border-slate-700 hover:text-white">About</a>
                        <a href="{{ url('/contact') }}" class="rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-300 hover:border-slate-700 hover:text-white">Contact</a>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex flex-col gap-2 border-t border-slate-800 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $globalSettings['app_name'] ?? config('app.name') }}. All rights reserved.</p>
                <p>Built for modern library operations.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script>
        (() => {
            const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReduced) return;
            const items = document.querySelectorAll('.reveal');
            if (!items.length) return;
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            items.forEach((item) => observer.observe(item));
        })();
    </script>

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
