<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @if(!empty($global_favicon))
    <link rel="icon" href="{{ Storage::url($global_favicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100/60 py-10 px-4">
        <div class="mx-auto w-full max-w-md">
            <div class="mb-6 text-center">
                <a href="/" wire:navigate class="inline-flex items-center justify-center">
                    <x-application-logo class="w-16 h-16 fill-current text-indigo-600" />
                </a>
            </div>

            <div class="w-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
