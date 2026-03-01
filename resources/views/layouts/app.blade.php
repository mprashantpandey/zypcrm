<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $globalSettings['app_name'] ?? config('app.name', 'LibrarySaaS') }}</title>

    @if(!empty($global_favicon))
    <link rel="icon" href="{{ Storage::url($global_favicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 selection:bg-indigo-500 selection:text-white"
    style="font-family: 'Inter', sans-serif;">
    <!-- Navigation (Sidebar & Topbar) -->
    <livewire:layout.navigation />

    <!-- Main Content Area -->
    <div class="md:pl-64 flex flex-col min-h-screen pt-16 transition-all duration-300">
        <main class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            @if (isset($header))
            <header class="mb-8">
                {{ $header }}
            </header>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>

</html>