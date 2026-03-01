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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 selection:bg-indigo-500 selection:text-white"
    style="font-family: 'Inter', sans-serif;">

    <!-- Public Navbar -->
    <nav x-data="{ open: false }"
        class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                        @php
                        $hasLightLogo = !empty($globalSettings['light_logo']) && file_exists(storage_path('app/public/'
                        . $globalSettings['light_logo']));
                        @endphp
                        @if($hasLightLogo)
                        <img src="{{ Storage::url($globalSettings['light_logo']) }}"
                            alt="{{ $globalSettings['app_name'] ?? config('app.name') }}"
                            class="h-10 w-auto group-hover:scale-105 transition-transform duration-300">
                        @else
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-800 flex items-center justify-center shadow-lg group-hover:shadow-indigo-500/25 transition-all duration-300">
                            <span class="text-white font-bold text-xl tracking-tighter"
                                style="font-family: 'Outfit', sans-serif;">{{ substr($globalSettings['app_name'] ??
                                config('app.name'), 0, 1) }}</span>
                        </div>
                        <span
                            class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 group-hover:to-indigo-600 transition-colors duration-300"
                            style="font-family: 'Outfit', sans-serif;">{{ $globalSettings['app_name'] ??
                            config('app.name') }}</span>
                        @endif
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}"
                        class="text-gray-600 hover:text-indigo-600 font-medium transition-colors duration-200">Home</a>
                    <a href="{{ url('/pricing') }}"
                        class="text-gray-600 hover:text-indigo-600 font-medium transition-colors duration-200">Pricing</a>
                    <a href="{{ url('/about') }}"
                        class="text-gray-600 hover:text-indigo-600 font-medium transition-colors duration-200">About</a>
                    <a href="{{ url('/contact') }}"
                        class="text-gray-600 hover:text-indigo-600 font-medium transition-colors duration-200">Contact</a>
                </div>

                <!-- Actions -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-600 hover:text-indigo-600 font-semibold transition-colors duration-200">
                        Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="text-gray-600 hover:text-indigo-600 font-semibold transition-colors duration-200">
                        Log in
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Get library system
                    </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" class="md:hidden bg-white border-t border-gray-100" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ url('/') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-indigo-600">Home</a>
                <a href="{{ url('/pricing') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-indigo-600">Pricing</a>
                <a href="{{ url('/about') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-indigo-600">About</a>
                <a href="{{ url('/contact') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50 hover:text-indigo-600">Contact</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-5 space-y-3">
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="block w-full text-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}"
                        class="block w-full text-center px-4 py-2 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Log
                        in</a>
                    <a href="{{ route('register') }}"
                        class="block w-full text-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Get
                        library system</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <div class="space-y-8 xl:col-span-1">
                    <span class="text-2xl font-bold text-white tracking-tighter"
                        style="font-family: 'Outfit', sans-serif;">{{ $globalSettings['app_name'] ?? config('app.name')
                        }}</span>
                    <p class="text-gray-400 text-base leading-relaxed">
                        The complete operating system for modern study libraries. Manage seats, fees, attendance, and
                        grow your student base with ease.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Product</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li><a href="{{ url('/pricing') }}"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Pricing</a>
                                </li>
                                <li><a href="#"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Features</a>
                                </li>
                                <li><a href="{{ route('login') }}"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Log
                                        in</a></li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Company</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li><a href="{{ url('/about') }}"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">About</a>
                                </li>
                                <li><a href="{{ url('/contact') }}"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Contact
                                        Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Legal</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li><a href="{{ url('/policies') }}"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Privacy
                                        Policy</a></li>
                                <li><a href="{{ url('/policies') }}"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Terms
                                        of Service</a></li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Support</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li><a href="#"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">Help
                                        Center</a></li>
                                <li><a href="#"
                                        class="text-base text-gray-400 hover:text-white transition-colors duration-200">API
                                        Documentation</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-800 pt-8">
                <p class="text-base text-gray-400 xl:text-center">&copy; {{ date('Y') }} {{ $globalSettings['app_name']
                    ?? config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>