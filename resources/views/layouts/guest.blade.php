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
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        .auth-ambient {
            background:
                radial-gradient(1200px 500px at 0% 0%, rgba(14, 116, 144, 0.16), transparent 55%),
                radial-gradient(900px 450px at 100% 100%, rgba(14, 165, 233, 0.18), transparent 55%),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 45%, #ecfeff 100%);
        }

        .auth-glow {
            position: absolute;
            border-radius: 9999px;
            filter: blur(48px);
            opacity: 0.35;
            animation: floatPulse 9s ease-in-out infinite;
        }

        .auth-glow-1 {
            width: 16rem;
            height: 16rem;
            background: #38bdf8;
            top: 4rem;
            left: -4rem;
        }

        .auth-glow-2 {
            width: 13rem;
            height: 13rem;
            background: #0ea5e9;
            right: -3rem;
            bottom: 2rem;
            animation-delay: 1.8s;
        }

        .auth-card {
            animation: cardIn .45s ease-out;
        }

        @keyframes floatPulse {
            0%,
            100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-10px) scale(1.05);
            }
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="text-gray-900 antialiased auth-ambient" style="font-family: 'Manrope', sans-serif;">
    <div class="relative min-h-screen overflow-hidden px-4 py-8 sm:px-6 lg:px-8">
        <div class="auth-glow auth-glow-1"></div>
        <div class="auth-glow auth-glow-2"></div>
        @php
            $appName = $globalSettings['app_name'] ?? config('app.name', 'LibrarySaaS');
            $hasLightLogo = !empty($globalSettings['light_logo']) && file_exists(storage_path('app/public/'.$globalSettings['light_logo']));
            $hasDarkLogo = !empty($globalSettings['dark_logo']) && file_exists(storage_path('app/public/'.$globalSettings['dark_logo']));
        @endphp

        <div class="mx-auto grid w-full max-w-6xl items-stretch gap-6 lg:grid-cols-2">
            <section class="relative hidden overflow-hidden rounded-3xl border border-sky-100 bg-slate-950 p-10 text-white shadow-2xl lg:flex lg:flex-col">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.20),_transparent_45%)]"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <a href="/" wire:navigate class="inline-flex items-center gap-3">
                            @if($hasLightLogo)
                                <img src="{{ Storage::url($globalSettings['light_logo']) }}" alt="{{ $appName }}" class="h-10 w-auto">
                            @else
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white">
                                    {{ strtoupper(substr($appName, 0, 1)) }}
                                </span>
                            @endif
                            <span class="text-xl font-bold tracking-tight" style="font-family: 'Sora', sans-serif;">
                                {{ $appName }}
                            </span>
                        </a>
                        <h1 class="mt-10 text-4xl font-bold leading-tight" style="font-family: 'Sora', sans-serif;">
                            Welcome back to your smart library workspace.
                        </h1>
                        <p class="mt-5 max-w-md text-sm leading-6 text-slate-200">
                            Handle admissions, fees, attendance, seats, and alerts from one reliable dashboard designed for operational speed.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/15 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sky-200">Why teams use it daily</p>
                        <ul class="mt-3 space-y-2 text-sm text-slate-100">
                            <li>Real-time attendance and seat tracking</li>
                            <li>Automated fee reminders and receipts</li>
                            <li>Unified admin, library, and student workflows</li>
                        </ul>
                    </div>
                </div>
            </section>

            <div class="auth-card relative w-full rounded-3xl border border-white/70 bg-white/85 p-6 shadow-xl backdrop-blur sm:p-8 lg:p-10">
                <div class="mb-8 flex items-center justify-between lg:hidden">
                    <a href="/" wire:navigate class="inline-flex items-center gap-2">
                        @if($hasDarkLogo)
                            <img src="{{ Storage::url($globalSettings['dark_logo']) }}" alt="{{ $appName }}" class="h-9 w-auto">
                        @else
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500 text-sm font-bold text-white">
                                {{ strtoupper(substr($appName, 0, 1)) }}
                            </span>
                        @endif
                        <span class="text-base font-bold text-slate-900">{{ $appName }}</span>
                    </a>
                    <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Secure Access</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

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
