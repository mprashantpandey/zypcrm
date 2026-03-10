<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'LibrarySaaS'))</title>

    @php
        $faviconUrl = !empty($global_favicon)
            ? Storage::url($global_favicon)
            : (!empty($globalSettings['light_logo']) ? Storage::url($globalSettings['light_logo']) : asset('favicon.ico'));
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }
        .reveal-up { animation: revealUp .55s ease both; }
        .reveal-up.delay-1 { animation-delay: .08s; }
        .pattern-grid {
            background-image:
                linear-gradient(to right, rgba(148,163,184,.16) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(148,163,184,.16) 1px, transparent 1px);
            background-size: 26px 26px;
        }
        @keyframes revealUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased" style="font-family: 'Inter', sans-serif;">
    @yield('content')

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
