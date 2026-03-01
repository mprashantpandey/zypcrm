<div class="py-10 bg-gradient-to-br from-slate-50 to-gray-100/60 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Platform Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Configure your SaaS platform — branding, payments, notifications,
                    and more.</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-medium text-gray-600">{{ $activeTab === 'general' ? 'General' : ($activeTab
                        === 'branding' ? 'Branding' : ($activeTab === 'seo' ? 'SEO & Analytics' : ($activeTab === 'auth' ?
                        'Authentication' : ($activeTab === 'mail' ? 'SMTP / Email' : ($activeTab === 'payment' ? 'Payment
                        Gateways' : ($activeTab === 'firebase' ? 'Firebase & Push' : ($activeTab === 'app_links' ? 'App &
                        Versions' : 'Platform Modules'))))))) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
                    <span class="inline-block w-2 h-2 rounded-full {{ $schedulerLastRunAt ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                    <span class="text-xs font-medium text-gray-600">
                        Scheduler: {{ $schedulerLastRunHuman ? 'last run '.$schedulerLastRunHuman : 'no run yet' }}
                    </span>
                    @if ($schedulerLastRunCommand)
                        <span class="text-[10px] text-gray-500">({{ $schedulerLastRunCommand }})</span>
                    @endif
                </div>
            </div>
        </div>

        @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start justify-between gap-3">
                <span>{{ session('message') }}</span>
                <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-900 font-medium">Dismiss</button>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-start justify-between gap-3">
                <div>
                    <p class="font-semibold mb-1">Please fix the following issues:</p>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" @click="show = false" class="text-red-700 hover:text-red-900 font-medium">Dismiss</button>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-8 items-start md:grid-cols-[19rem_minmax(0,1fr)]">

            <!-- ── Sidebar Navigation ─────────────────────── -->
            <div class="w-full md:w-[19rem] md:min-w-[19rem]">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                    {{-- PLATFORM group --}}
                    <div class="px-2 pt-4 pb-2">
                        <p class="px-2 mb-2 text-[9px] font-bold tracking-[0.15em] uppercase text-gray-400">Platform</p>

                        @php $a = ($activeTab === 'general'); @endphp
                        <button wire:click="switchTab('general')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">General</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Name,
                                    currency, contact</span></span>
                        </button>

                        @php $a = ($activeTab === 'branding'); @endphp
                        <button wire:click="switchTab('branding')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Branding
                                    &amp; UI</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Logos,
                                    colors, theme</span></span>
                        </button>

                        @php $a = ($activeTab === 'seo'); @endphp
                        <button wire:click="switchTab('seo')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">SEO
                                    &amp; Analytics</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Meta, GA
                                    tracking</span></span>
                        </button>
                    </div>

                    {{-- IDENTITY group --}}
                    <div class="px-2 pt-3 pb-2 border-t border-gray-100">
                        <p class="px-2 mb-2 text-[9px] font-bold tracking-[0.15em] uppercase text-gray-400">Identity</p>

                        @php $a = ($activeTab === 'auth'); @endphp
                        <button wire:click="switchTab('auth')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Authentication</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Email/password,
                                    phone OTP</span></span>
                        </button>

                        @php $a = ($activeTab === 'mail'); @endphp
                        <button wire:click="switchTab('mail')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">SMTP /
                                    Email</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Transactional
                                    mail</span></span>
                        </button>
                    </div>

                    {{-- INTEGRATIONS group --}}
                    <div class="px-2 pt-3 pb-2 border-t border-gray-100">
                        <p class="px-2 mb-2 text-[9px] font-bold tracking-[0.15em] uppercase text-gray-400">Integrations
                        </p>

                        @php $a = ($activeTab === 'payment'); @endphp
                        <button wire:click="switchTab('payment')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Payment
                                    Gateways</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Stripe,
                                    Razorpay, manual</span></span>
                        </button>

                        @php $a = ($activeTab === 'firebase'); @endphp
                        <button wire:click="switchTab('firebase')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Firebase
                                    &amp; Push</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">FCM Admin
                                    SDK, VAPID</span></span>
                        </button>
                    </div>

                    {{-- SYSTEM group --}}
                    <div class="px-2 pt-3 pb-4 border-t border-gray-100">
                        <p class="px-2 mb-2 text-[9px] font-bold tracking-[0.15em] uppercase text-gray-400">System</p>

                        @php $a = ($activeTab === 'app_links'); @endphp
                        <button wire:click="switchTab('app_links')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">App
                                    &amp; Versions</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">Stores,
                                    force update</span></span>
                        </button>

                        @php $a = ($activeTab === 'modules'); @endphp
                        <button wire:click="switchTab('modules')"
                            class="group w-full flex items-center gap-3 px-2.5 py-2.5 mb-1 rounded-xl text-left transition-all {{ $a ? 'bg-indigo-600 shadow-sm' : 'hover:bg-gray-50' }}">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center {{ $a ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-gray-200' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ $a ? 'text-white' : 'text-gray-500' }}" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0"><span
                                    class="block text-sm font-medium {{ $a ? 'text-white' : 'text-gray-800' }}">Platform
                                    Modules</span><span
                                    class="block text-[11px] {{ $a ? 'text-indigo-200' : 'text-gray-400' }}">KYC, blog,
                                    support</span></span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Content Area -->
            <div class="w-full min-w-0">

                <!-- General Tab -->
                <div
                    class="{{ $activeTab === 'general' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-semibold text-gray-900">General Settings</h3>
                    </div>
                    <div class="p-6">
                        <form wire:submit="saveGeneral" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Site Title</label>
                                    <input type="text" wire:model="siteTitle"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900">Currency</label>
                                        <input type="text" wire:model="currency" placeholder="USD"
                                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900">Symbol</label>
                                        <input type="text" wire:model="currencySymbol" placeholder="$"
                                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Contact Email</label>
                                    <input type="email" wire:model="contactEmail"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Contact Phone</label>
                                    <input type="text" wire:model="contactPhone"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-900">Address / HQ</label>
                                    <input type="text" wire:model="address"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="flex justify-end border-t border-gray-100 pt-4">
                                <button type="submit"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                    General Configuration</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Branding Tab -->
                <div
                    class="{{ $activeTab === 'branding' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-semibold text-gray-900">Branding & UI</h3>
                    </div>
                    <div class="p-6">
                        <form wire:submit="saveBranding" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Application Name</label>
                                    <input type="text" wire:model="appName"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Updates the platform title and sidebar
                                        branding globally.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Primary Color (Hex)</label>
                                    <div class="mt-2 flex items-center gap-3">
                                        <input type="color" wire:model="primaryColor"
                                            class="h-9 w-9 p-0 border-0 rounded cursor-pointer">
                                        <input type="text" wire:model="primaryColor"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase font-mono">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Light Theme Logo Dropzone -->
                        <div x-data="{ isDropping: false }" x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="isDropping = false; $refs.lightFileInput.files = $event.dataTransfer.files; $refs.lightFileInput.dispatchEvent(new Event('change', { bubbles: true }));"
                            class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl transition-all duration-200 ease-in-out cursor-pointer"
                            :class="isDropping ? 'border-indigo-500 bg-indigo-50/50 scale-[1.02]' : 'border-gray-300 bg-gray-50 hover:bg-gray-100/50'"
                            @click="$refs.lightFileInput.click()">

                            <label
                                class="block text-sm font-bold text-gray-900 mb-4 text-center pointer-events-none">Light
                                Theme Logo</label>

                            @if ($lightLogo)
                            <img src="{{ $lightLogo->temporaryUrl() }}"
                                class="h-14 mb-4 object-contain drop-shadow-sm rounded pointer-events-none">
                            @elseif ($existingLightLogo)
                            <img src="{{ Storage::url($existingLightLogo) }}"
                                class="h-14 mb-4 object-contain drop-shadow-sm rounded pointer-events-none">
                            @else
                            <div
                                class="h-14 w-14 rounded-full bg-white flex items-center justify-center mb-4 shadow-sm border border-gray-200 pointer-events-none">
                                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            @endif

                            <div class="text-center pointer-events-none">
                                <span class="text-xs text-indigo-600 font-semibold">Click to upload</span>
                                <span class="text-xs text-gray-500 block mt-1">or drag & drop</span>
                            </div>
                            <input type="file" x-ref="lightFileInput" wire:model.live="lightLogo" class="hidden"
                                accept="image/*">
                        </div>

                        <!-- Dark Theme Logo Dropzone -->
                        <div x-data="{ isDropping: false }" x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="isDropping = false; $refs.darkFileInput.files = $event.dataTransfer.files; $refs.darkFileInput.dispatchEvent(new Event('change', { bubbles: true }));"
                            class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl transition-all duration-200 ease-in-out cursor-pointer"
                            :class="isDropping ? 'border-gray-600 bg-gray-800 scale-[1.02]' : 'border-gray-500 bg-gray-900 hover:bg-gray-800'"
                            @click="$refs.darkFileInput.click()">

                            <label class="block text-sm font-bold text-white mb-4 text-center pointer-events-none">Dark
                                Theme Logo</label>

                            @if ($darkLogo)
                            <img src="{{ $darkLogo->temporaryUrl() }}"
                                class="h-14 mb-4 object-contain drop-shadow-md rounded pointer-events-none">
                            @elseif ($existingDarkLogo)
                            <img src="{{ Storage::url($existingDarkLogo) }}"
                                class="h-14 mb-4 object-contain drop-shadow-md rounded pointer-events-none">
                            @else
                            <div
                                class="h-14 w-14 rounded-full bg-gray-800 flex items-center justify-center mb-4 shadow-inner border border-gray-700 pointer-events-none">
                                <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                    </path>
                                </svg>
                            </div>
                            @endif

                            <div class="text-center pointer-events-none">
                                <span class="text-xs text-indigo-400 font-semibold">Click to upload</span>
                                <span class="text-xs text-gray-400 block mt-1">or drag & drop</span>
                            </div>
                            <input type="file" x-ref="darkFileInput" wire:model.live="darkLogo" class="hidden"
                                accept="image/*">
                        </div>

                        <!-- Favicon Dropzone -->
                        <div x-data="{ isDropping: false }" x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="isDropping = false; $refs.faviconInput.files = $event.dataTransfer.files; $refs.faviconInput.dispatchEvent(new Event('change', { bubbles: true }));"
                            class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl transition-all duration-200 ease-in-out cursor-pointer"
                            :class="isDropping ? 'border-indigo-500 bg-indigo-50/50 scale-[1.02]' : 'border-gray-300 bg-gray-50 hover:bg-gray-100/50'"
                            @click="$refs.faviconInput.click()">

                            <label
                                class="block text-sm font-bold text-gray-900 mb-4 text-center pointer-events-none">Favicon
                                (32x32)</label>

                            @if ($favicon)
                            <img src="{{ $favicon->temporaryUrl() }}"
                                class="h-10 w-10 mb-4 object-contain drop-shadow-sm rounded pointer-events-none">
                            @elseif ($existingFavicon)
                            <img src="{{ Storage::url($existingFavicon) }}"
                                class="h-10 w-10 mb-4 object-contain drop-shadow-sm rounded pointer-events-none">
                            @else
                            <div
                                class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-4 shadow-sm border border-gray-200 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                    </path>
                                </svg>
                            </div>
                            @endif

                            <div class="text-center pointer-events-none">
                                <span class="text-xs text-indigo-600 font-semibold">Click to upload</span>
                                <span class="text-xs text-gray-500 block mt-1">or drag & drop</span>
                            </div>
                            <input type="file" x-ref="faviconInput" wire:model.live="favicon" class="hidden"
                                accept="image/*">
                        </div>

                            </div>

                            <div class="flex justify-end border-t border-gray-100 pt-4">
                                <button type="submit"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                    Branding</button>
                            </div>
                        </form>
                    </div>
                </div>
            <!-- SEO Tab -->
                <div
                    class="{{ $activeTab === 'seo' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">SEO & Analytics</h3>
                </div>
                <div class="p-6">
                    <form wire:submit="saveSeo" class="space-y-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Meta Keywords</label>
                                <input type="text" wire:model="seoMetaKeywords"
                                    placeholder="library, saas, study room, management"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Meta Description</label>
                                <textarea wire:model="seoMetaDescription" rows="3"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Google Analytics Tracking
                                    ID</label>
                                <input type="text" wire:model="googleAnalyticsId" placeholder="G-XXXXXXXXXX"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end border-t border-gray-100 pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                SEO Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Auth Tab -->
                <div
                    class="{{ $activeTab === 'auth' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">Authentication & Registration</h3>
                </div>
                <div class="p-6">
                    <form wire:submit="saveAuth" class="space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <label class="text-sm font-medium text-gray-900">Allow Global Registration</label>
                                <p class="text-sm text-gray-500">If disabled, new tenants cannot sign up
                                    independently.</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="allowRegistration"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <label class="text-sm font-medium text-gray-900">Require Email Verification</label>
                                <p class="text-sm text-gray-500">Force new signups to click an email verification
                                    link before accessing the platform.</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="requireEmailVerification"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                        </div>
                        <div class="pt-4 space-y-4">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Login Methods</h4>
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 space-y-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Email / Password Login</label>
                                        <p class="text-sm text-gray-500">Used by web and mobile app password login.</p>
                                    </div>
                                    <input type="checkbox" wire:model="emailPasswordAuthEnabled"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 mt-1">
                                </div>
                                <div class="flex items-start justify-between gap-4 border-t border-gray-200 pt-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-900">Phone OTP Login (Firebase)</label>
                                        <p class="text-sm text-gray-500">Used by mobile app OTP login via Firebase phone auth.</p>
                                    </div>
                                    <input type="checkbox" wire:model="firebasePhoneAuthEnabled"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 mt-1">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end border-t border-gray-100 pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                Authentication Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mail Tab -->
                <div
                    class="{{ $activeTab === 'mail' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">SMTP Configurations</h3>
                </div>
                <div class="p-6">
                    <form wire:submit="saveMail" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-900">SMTP Host</label>
                                <input type="text" wire:model="mailHost" placeholder="smtp.mailgun.org"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Port (587, 465)</label>
                                <input type="text" wire:model="mailPort"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900">From Address</label>
                                <input type="email" wire:model="mailFromAddress" placeholder="noreply@domain.com"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-900">SMTP Username</label>
                                <input type="text" wire:model="mailUsername"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-900">SMTP Password</label>
                                <input type="password" wire:model="mailPassword"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end border-t border-gray-100 pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                Mail Setup</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Tab -->
                <div
                    class="{{ $activeTab === 'payment' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">Payment Gateways</h3>
                    <p class="mt-1 text-sm text-gray-500">Enable or disable gateways and configure your keys.</p>
                </div>
                <div class="p-6">
                    <form wire:submit="saveBilling" class="space-y-8">

                        <!-- Stripe Integration -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                <h4
                                    class="text-sm font-bold text-gray-900 uppercase tracking-tight flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div> Stripe Integration
                                </h4>
                                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                                    <input type="checkbox" wire:model="enableStripe"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    Enable Stripe
                                </label>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-6 sm:grid-cols-2 p-4 bg-gray-50 rounded-lg border border-gray-100 {{ !$enableStripe ? 'opacity-50 pointer-events-none' : '' }}">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Publishable Key</label>
                                    <input type="text" wire:model="stripeKey" placeholder="pk_live_..."
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Secret Key</label>
                                    <input type="password" wire:model="stripeSecret" placeholder="sk_live_..."
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Razorpay Integration -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                <h4
                                    class="text-sm font-bold text-gray-900 uppercase tracking-tight flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div> Razorpay Integration
                                </h4>
                                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                                    <input type="checkbox" wire:model="enableRazorpay"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    Enable Razorpay
                                </label>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-6 sm:grid-cols-2 p-4 bg-gray-50 rounded-lg border border-gray-100 {{ !$enableRazorpay ? 'opacity-50 pointer-events-none' : '' }}">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Key ID</label>
                                    <input type="text" wire:model="razorpayKey"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Key Secret</label>
                                    <input type="password" wire:model="razorpaySecret"
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Manual Payment -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                <h4
                                    class="text-sm font-bold text-gray-900 uppercase tracking-tight flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div> Manual Payment / Wire
                                    Transfer
                                </h4>
                                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                                    <input type="checkbox" wire:model="enableManualPayment"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    Enable Manual
                                </label>
                            </div>
                            <div
                                class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-100 {{ !$enableManualPayment ? 'opacity-50 pointer-events-none' : '' }}">
                                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-900">
                                    <input type="checkbox" wire:model="enableManualSubscriptionApproval"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    Require manual Admin approval for subscriptions purchased via Wire Transfer.
                                </label>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900">Payment Instructions /
                                        Bank Details</label>
                                    <textarea wire:model="manualPaymentInstructions" rows="4"
                                        placeholder="Bank Name: XYZ Bank\nAccount Number: 123456789\nPlease upload your wire transfer receipt after checkout..."
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Fees Content -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                <h4
                                    class="text-sm font-bold text-gray-900 uppercase tracking-tight flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-purple-500"></div> Global Platform Fees Settings
                                </h4>
                                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                                    <input type="checkbox" wire:model="enablePlatformFeeCollection"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    Enable Library Fees Collection
                                </label>
                            </div>
                            <div
                                class="p-4 bg-gray-50 rounded-lg border border-gray-100 {{ !$enablePlatformFeeCollection ? 'opacity-50 pointer-events-none' : '' }}">
                                <p class="text-sm text-gray-500 mb-4">Allow library owners to collect fees online
                                    directly through the platform. We deduct the configured commission percentage on
                                    each successful transaction before settling with the library owner.</p>

                                <div class="max-w-xs">
                                    <label class="block text-sm font-medium text-gray-900">Platform Commission
                                        (%)</label>
                                    <div class="mt-2 relative rounded-md shadow-sm">
                                        <input type="number" wire:model="platformFeePercentage" step="0.01" min="0"
                                            max="100" placeholder="5.0"
                                            class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">E.g., 5.0 for 5% processing/platform fee.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end border-t border-gray-100 pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                Gateways</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Firebase Tab -->
                <div
                    class="{{ $activeTab === 'firebase' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">Firebase Admin SDK (FCM HTTP v1)</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Uses the <strong>Firebase Admin SDK</strong> (HTTP v1 API / OAuth2), not the deprecated legacy
                        Server Key.
                        Generate credentials at <strong>Firebase Console → Project Settings → Service Accounts →
                            Generate new private key</strong>.
                    </p>
                </div>
                <div class="p-6">
                    <form wire:submit="saveFirebase" class="space-y-6">

                        {{-- Toggle --}}
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <label class="text-sm font-medium text-gray-900">Enable Push Notifications</label>
                                <p class="text-sm text-gray-500">Send FCM push notifications to Flutter / Web clients.
                                </p>
                            </div>
                            <input type="checkbox" wire:model="firebaseEnabled"
                                class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>

                        {{-- Service Account JSON --}}
                        <div class="{{ !$firebaseEnabled ? 'opacity-50 pointer-events-none' : '' }} space-y-4">

                            <div>
                                <label class="block text-sm font-bold text-gray-900">
                                    Service Account JSON
                                    <span
                                        class="ml-1 text-xs font-normal text-indigo-600 bg-indigo-50 rounded-full px-2 py-0.5">Admin
                                        SDK — required for push</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Paste the entire contents of the <code
                                        class="bg-gray-100 px-1 rounded">serviceAccountKey.json</code> file downloaded
                                    from Firebase Console.</p>
                                <textarea wire:model="firebaseServiceAccountJson" rows="12"
                                    placeholder='{ "type": "service_account", "project_id": "your-project", "private_key": "-----BEGIN RSA PRIVATE KEY-----\n...", ... }'
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs font-mono"></textarea>
                                @error('firebaseServiceAccountJson')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Quick info box --}}
                            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 flex gap-3">
                                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-amber-800 space-y-1">
                                    <p class="font-semibold">How to get the Service Account JSON:</p>
                                    <ol class="list-decimal pl-4 space-y-0.5">
                                        <li>Open <strong>Firebase Console</strong> → select your project.</li>
                                        <li>Go to ⚙️ <strong>Project Settings</strong> → <strong>Service
                                                Accounts</strong> tab.</li>
                                        <li>Click <strong>"Generate new private key"</strong> → confirm → a <code
                                                class="bg-amber-100 px-0.5 rounded">*.json</code> file downloads.</li>
                                        <li>Open the file and paste <strong>all its contents</strong> in the textarea
                                            above.</li>
                                    </ol>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Web / Flutter client config --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900">
                                    Web Client Config
                                    <span class="ml-1 text-xs font-normal text-gray-500">Optional — for Flutter Web
                                        &amp; browser SDK</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1">From Firebase Console → Project Settings → General
                                    → Your apps → Web SDK snippet.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Web API Key</label>
                                    <input type="text" wire:model="firebaseApiKey" placeholder="AIza..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Auth Domain</label>
                                    <input type="text" wire:model="firebaseAuthDomain"
                                        placeholder="your-app.firebaseapp.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">App ID</label>
                                    <input type="text" wire:model="firebaseAppId" placeholder="1:123456:web:abc..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">VAPID Key (Web Push)</label>
                                    <input type="text" wire:model="firebaseVapidKey" placeholder="BJ..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-4">

                        {{-- Phone Auth (OTP) --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900">
                                        Phone Authentication (OTP)
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Allow users to log in using their phone
                                        numbers and OTP via Firebase Phone Auth.</p>
                                </div>
                                <input type="checkbox" wire:model="firebasePhoneAuthEnabled"
                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>

                            <div
                                class="{{ !$firebasePhoneAuthEnabled ? 'opacity-50 pointer-events-none' : '' }} space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Test Phone Numbers (For
                                        Reviewers / Development)</label>
                                    <input type="text" wire:model="firebaseTestPhoneNumbers"
                                        placeholder="+16505551234, +919999999999"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">Comma-separated list of phone numbers to
                                        bypass real SMS for testing. Ensure these are also added in the Firebase
                                        Console.</p>
                                </div>

                                {{-- SHA-1 info box --}}
                                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 flex gap-3">
                                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-xs text-blue-800 space-y-1">
                                        <p class="font-semibold">Phone Auth Setup Requirements:</p>
                                        <ol class="list-decimal pl-4 space-y-0.5">
                                            <li>Enable <strong>Phone</strong> sign-in method in Firebase Console →
                                                Authentication.</li>
                                            <li>Add your <strong>SHA-1</strong> and <strong>SHA-256</strong> app
                                                fingerprints to your Android app settings in Firebase Console.</li>
                                            <li>Place <code>google-services.json</code> in <code>android/app/</code> in
                                                your Flutter project.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end border-t border-gray-100 pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                Firebase Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- App Links Tab -->
                <div
                    class="{{ $activeTab === 'app_links' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">Mobile Apps & Versioning</h3>
                    <p class="text-sm text-gray-500 mt-1">Provide quick-links to your Google Play / Apple app stores
                        and enforce minimum client versions.</p>
                </div>
                <div class="p-6">
                    <form wire:submit="saveAppLinks" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Google Play Store
                                    Link</label>
                                <input type="url" wire:model="playStoreLink"
                                    placeholder="https://play.google.com/store/apps/details?id=..."
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Apple App Store Link</label>
                                <input type="url" wire:model="appStoreLink"
                                    placeholder="https://apps.apple.com/us/app/..."
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 pt-6 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-medium text-gray-900">Current App Version
                                    Requirement</label>
                                <input type="text" wire:model="currentAppVersion" placeholder="1.0.5"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">The minimum Flutter app version allowed to
                                    connect to this API.</p>
                            </div>
                            <div class="flex items-center pt-8">
                                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-900">
                                    <input type="checkbox" wire:model="forceAppUpdate"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    Force Users to Update App
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end border-t border-gray-100 pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Save
                                App Links</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modules Tab -->
                <div
                    class="{{ $activeTab === 'modules' ? 'block' : 'hidden' }} w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-900">Platform Modules</h3>
                    <p class="text-sm text-gray-500 mt-1">Enable or disable heavy ecosystem packages dynamically.
                    </p>
                </div>
                <div class="p-6">
                    <form wire:submit="saveModules" class="space-y-6">

                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <label class="text-sm font-bold text-gray-900">Tenant KYC / Identity
                                    Verification</label>
                                <p class="text-sm text-gray-500">Require platform library owners to upload an ID and
                                    Business License before receiving payments.</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="enableKyc"
                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <label class="text-sm font-bold text-gray-900">Platform Blog (SEO)</label>
                                <p class="text-sm text-gray-500">Enable the frontend public facing blog module to
                                    capture organic traffic.</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="enableBlog"
                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <label class="text-sm font-bold text-gray-900">Support Ticket System</label>
                                <p class="text-sm text-gray-500">Allow tenants to open tickets in the dashboard
                                    instead of emailing you.</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="enableSupportTickets"
                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-500 transition-colors">Apply
                                Module Toggles</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
