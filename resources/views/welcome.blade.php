@extends('layouts.public')

@section('title', config('app.name', 'LibrarySaaS') . ' | The Modern Operating System for Study Libraries')

@section('content')
<div class="relative overflow-hidden bg-white">
    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 lg:pb-32 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-indigo-200 to-indigo-600 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-sm font-semibold mb-8 border border-indigo-100 shadow-sm animate-fade-in-up">
                <span class="flex h-2 w-2 rounded-full bg-indigo-600"></span>
                The #1 choice for modern libraries
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 mb-8 max-w-4xl mx-auto"
                style="font-family: 'Outfit', sans-serif;">
                Run your study library on <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">autopilot.</span>
            </h1>

            <p class="mt-6 text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto mb-10 font-medium">
                Everything you need to manage seats, track attendance, collect fees, and grow your student base—all in
                one beautiful platform.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 animate-fade-in-up"
                style="animation-delay: 0.1s;">
                <a href="{{ route('register') }}"
                    class="w-full sm:w-auto px-8 py-4 text-lg font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all duration-300">
                    Get Started Free
                </a>
                <a href="#features"
                    class="w-full sm:w-auto px-8 py-4 text-lg font-bold rounded-2xl text-gray-700 bg-white border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-300">
                    See Features
                </a>
            </div>
            <p class="mt-6 text-sm text-gray-500">No credit card required &middot; 14 day free trial</p>
        </div>

        <!-- Dashboard Image Mockup -->
        <div class="relative mx-auto mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div
                class="rounded-2xl bg-gray-900/5 p-2 ring-1 ring-inset ring-gray-900/10 lg:rounded-3xl lg:p-4 shadow-2xl transform hover:scale-[1.01] transition-transform duration-500">
                <div
                    class="overflow-hidden rounded-xl bg-white ring-1 ring-gray-200 shadow-sm flex items-center justify-center bg-gradient-to-br from-indigo-50 to-white border-4 border-white h-[400px] md:h-[600px] relative">
                    <!-- Synthetic Dashboard Graphic -->
                    <div
                        class="absolute inset-0 bg-gray-50 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:2rem_2rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]">
                    </div>
                    <div class="z-10 text-center">
                        <div
                            class="w-20 h-20 mx-auto bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg mb-6 shadow-indigo-500/30">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900" style="font-family: 'Outfit', sans-serif;">Powerful
                            Library Dashboard</h3>
                        <p class="text-gray-500 mt-2 text-lg">Manage everything from a single pane of glass.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trusted By / Stats Section -->
    <div class="bg-indigo-600 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-y-8 text-center sm:grid-cols-3 sm:gap-x-8">
                <div>
                    <div class="text-5xl font-extrabold text-white tracking-tight"
                        style="font-family: 'Outfit', sans-serif;">100+</div>
                    <div class="mt-2 text-lg font-medium text-indigo-100">Active Libraries</div>
                </div>
                <div>
                    <div class="text-5xl font-extrabold text-white tracking-tight"
                        style="font-family: 'Outfit', sans-serif;">50k+</div>
                    <div class="mt-2 text-lg font-medium text-indigo-100">Students Managed</div>
                </div>
                <div>
                    <div class="text-5xl font-extrabold text-white tracking-tight"
                        style="font-family: 'Outfit', sans-serif;">{{ $global_currency }}2M+</div>
                    <div class="mt-2 text-lg font-medium text-indigo-100">Fees Collected</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 sm:py-32 bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-600 tracking-wide uppercase">Faster. Smarter.
                    Better.</h2>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl"
                    style="font-family: 'Outfit', sans-serif;">
                    Everything you need to scale.
                </p>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Stop using spreadsheets and WhatsApp groups. Upgrade to a professional system designed specifically
                    for the unique needs of study libraries and reading rooms.
                </p>
            </div>

            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                <dl class="grid max-w-xl grid-cols-1 gap-x-12 gap-y-16 lg:max-w-none lg:grid-cols-3">

                    <!-- Feature 1 -->
                    <div
                        class="flex flex-col bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <dt class="flex items-center gap-x-4 text-xl font-bold leading-7 text-gray-900"
                            style="font-family: 'Outfit', sans-serif;">
                            <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-indigo-50">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                            </div>
                            Smart Seat Management
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-relaxed text-gray-600">
                            <p class="flex-auto">Visually map out your library grid. Assign students to specific seats,
                                track vacancies in real-time, and optimize your floor plan to maximize revenue.</p>
                        </dd>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="flex flex-col bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <dt class="flex items-center gap-x-4 text-xl font-bold leading-7 text-gray-900"
                            style="font-family: 'Outfit', sans-serif;">
                            <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-orange-50">
                                <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            Automated Fee Collection
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-relaxed text-gray-600">
                            <p class="flex-auto">Generate payment links, accept online payments via Razorpay or Stripe,
                                send SMS reminders for overdue fees, and track your monthly cash flow automatically.</p>
                        </dd>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="flex flex-col bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <dt class="flex items-center gap-x-4 text-xl font-bold leading-7 text-gray-900"
                            style="font-family: 'Outfit', sans-serif;">
                            <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-emerald-50">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                            </div>
                            Attendance & Leaves
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-relaxed text-gray-600">
                            <p class="flex-auto">Mark daily student attendance with a single click. Allow students to
                                request leaves via the Mobile App, and easily approve or reject them to adjust plan
                                validities.</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- App Download CTA -->
    <div class="bg-white py-24 sm:py-32 overflow-hidden relative">
        <div
            class="absolute right-0 top-0 -mt-20 -mr-20 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none">
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="bg-gray-900 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="grid grid-cols-1">
                    <div class="p-10 sm:p-16 lg:p-20 flex flex-col items-center text-center justify-center">
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-6"
                            style="font-family: 'Outfit', sans-serif;">
                            Includes a dedicated Mobile App for students.
                        </h2>
                        <p class="text-lg text-gray-400 mb-8 leading-relaxed max-w-2xl">
                            Give your students a premium experience. They can log in with their phone number via
                            Firebase OTP, view their seat assignment, check fee status, and request leaves straight from
                            their phone.
                        </p>
                        <div class="flex gap-4 justify-center">
                            <a href="#"
                                class="inline-flex items-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-xl font-bold hover:bg-gray-100 transition-colors">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17.51 10.15c-.06-2.58 2.1-3.84 2.18-3.89-1.2-1.75-3.07-1.99-3.74-2.02-1.58-.16-3.08.93-3.89.93-.8 0-2.05-1.04-3.37-1.02-1.71.02-3.31.99-4.2 2.53-1.8 3.12-.46 7.74 1.3 10.27.86 1.25 1.88 2.65 3.25 2.6 1.33-.06 1.84-.87 3.44-.87 1.6 0 2.07.87 3.46.84 1.41-.02 2.28-1.28 3.13-2.52 1.01-1.48 1.42-2.92 1.44-2.99-.02-.01-2.81-1.07-2.88-4.28-.01-2.68 2.19-3.93 2.22-3.95-1.25-1.83-3.19-2.08-3.87-2.11zM14.93 4.29c.7-.85 1.17-2.02 1.04-3.19-1.01.04-2.22.68-2.95 1.54-.65.76-1.18 1.95-1.03 3.1 1.13.09 2.25-.6 2.94-1.45z" />
                                </svg>
                                App Store
                            </a>
                            <a href="#"
                                class="inline-flex items-center gap-2 bg-gray-800 text-white border border-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M3 20.5v-17c0-.83.67-1.5 1.5-1.5.38 0 .74.15 1.01.4l14.1 11.23c.53.42.61 1.19.19 1.72-.11.14-.24.26-.39.36l-14.1 9.4c-.69.46-1.63.28-2.09-.41-.14-.22-.22-.48-.22-.75z" />
                                </svg>
                                Google Play
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
