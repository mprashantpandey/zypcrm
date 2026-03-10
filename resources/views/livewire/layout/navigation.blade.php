<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ mobileMenuOpen: false }" class="relative z-40">
    <!-- Mobile overlay -->
    <div x-show="mobileMenuOpen" x-transition.opacity
        class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm md:hidden" @click="mobileMenuOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-300 transition-transform duration-300 ease-in-out md:translate-x-0 flex flex-col border-r border-slate-800/80 shadow-xl shadow-black/40">
        <!-- Logo Area -->
        <div class="flex h-16 shrink-0 items-center px-6 bg-slate-950/70 border-b border-slate-800/80">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 transition-opacity hover:opacity-80"
                wire:navigate>
                <div class="bg-indigo-500 text-white rounded-xl p-1.5 flex items-center justify-center shadow-inner shadow-indigo-900/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                        </path>
                    </svg>
                </div>
                <span class="font-bold text-lg text-white tracking-tight">{{ $globalSettings['app_name'] ??
                    'LibrarySaaS' }}</span>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">

            <a href="{{ route('dashboard') }}" wire:navigate
                class="{{ request()->routeIs('dashboard') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->role === 'super_admin')
            <div class="pt-6 pb-2">
                <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Super Admin</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" wire:navigate
                class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Platform Hub
            </a>

            <a href="{{ route('admin.plans') }}" wire:navigate
                class="{{ request()->routeIs('admin.plans') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.plans') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Subscription Plans
            </a>

            <a href="{{ route('admin.tenants') }}" wire:navigate
                class="{{ request()->routeIs('admin.tenants') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.tenants') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Library Tenants
            </a>

            <a href="{{ route('admin.students') }}" wire:navigate
                class="{{ request()->routeIs('admin.students') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.students') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4a4 4 0 110 8 4 4 0 010-8zm0 10c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z" />
                </svg>
                Students
            </a>

            <a href="{{ route('admin.reports') }}" wire:navigate
                class="{{ request()->routeIs('admin.reports') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.reports') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reports & Analytics
            </a>

            @if(\App\Models\Setting::getBool('enable_blog', false))
            <a href="{{ route('admin.blog') }}" wire:navigate
                class="{{ request()->routeIs('admin.blog') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.blog') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h10M4 14h10M4 18h6" />
                </svg>
                Platform Blog
            </a>
            @endif

            <a href="{{ route('admin.campaigns') }}" wire:navigate
                class="{{ request()->routeIs('admin.campaigns') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.campaigns') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10l9-6 9 6-9 6-9-6zm0 4l9 6 9-6" />
                </svg>
                Campaign Manager
            </a>

            <a href="{{ route('admin.exports') }}" wire:navigate
                class="{{ request()->routeIs('admin.exports') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.exports') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />
                </svg>
                Data Exports
            </a>

            <a href="{{ route('admin.ops-monitor') }}" wire:navigate
                class="{{ request()->routeIs('admin.ops-monitor') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.ops-monitor') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 3v2.25M14.25 3v2.25M4.5 9h15M4.5 19.5h15M6.75 6.75h10.5A2.25 2.25 0 0119.5 9v6A2.25 2.25 0 0117.25 17.25H6.75A2.25 2.25 0 014.5 15V9a2.25 2.25 0 012.25-2.25z" />
                </svg>
                Ops Monitor
            </a>

            <a href="{{ route('admin.disaster-readiness') }}" wire:navigate
                class="{{ request()->routeIs('admin.disaster-readiness') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.disaster-readiness') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                Disaster Readiness
            </a>

            <a href="{{ route('admin.attendance-risk') }}" wire:navigate
                class="{{ request()->routeIs('admin.attendance-risk') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.attendance-risk') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3m0 4h.01M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                Attendance Risk
            </a>

            @if(\App\Models\Setting::getBool('enable_support_tickets', false))
                <a href="{{ route('admin.support') }}" wire:navigate
                    class="{{ request()->routeIs('admin.support') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.support') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Support Tickets
                </a>
            @endif

            <a href="{{ route('admin.notices') }}" wire:navigate
                class="{{ request()->routeIs('admin.notices') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.notices') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                </svg>
                Notices
            </a>

            <a href="{{ route('admin.settings') }}" wire:navigate
                class="{{ request()->routeIs('admin.settings') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('admin.settings') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Platform Settings
            </a>
            @endif

            @if(auth()->user()->role === 'library_owner')
            <div class="pt-6 pb-2">
                <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Library Management</p>
            </div>

            <a href="{{ route('library.dashboard') }}" wire:navigate
                class="{{ request()->routeIs('library.dashboard') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Overview
            </a>

            <a href="{{ route('library.plans') }}" wire:navigate
                class="{{ request()->routeIs('library.plans') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.plans') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Subscription Plans
            </a>

            <a href="{{ route('library.students') }}" wire:navigate
                class="{{ request()->routeIs('library.students') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.students') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Students
            </a>

            <a href="{{ route('library.attendance.mark') }}" wire:navigate
                class="{{ request()->routeIs('library.attendance.mark') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.attendance.mark') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Mark Attendance
            </a>

            <a href="{{ route('library.attendance') }}" wire:navigate
                class="{{ request()->routeIs('library.attendance') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.attendance') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0a2 2 0 002 2h2a2 2 0 002-2m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v8" />
                </svg>
                Attendance View
            </a>

            <a href="{{ route('library.leaves') }}" wire:navigate
                class="{{ request()->routeIs('library.leaves') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.leaves') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Leaves
            </a>

            <a href="{{ route('library.seats') }}" wire:navigate
                class="{{ request()->routeIs('library.seats') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.seats') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Seating Arrangement
            </a>

            <a href="{{ route('library.fees') }}" wire:navigate
                class="{{ request()->routeIs('library.fees') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.fees') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Fee Collection
            </a>

            <a href="{{ route('library.exports') }}" wire:navigate
                class="{{ request()->routeIs('library.exports') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.exports') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16" />
                </svg>
                Data Exports
            </a>

            <a href="{{ route('library.leads') }}" wire:navigate
                class="{{ request()->routeIs('library.leads') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.leads') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h6m-8 8h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Leads
            </a>

            <a href="{{ route('library.notices') }}" wire:navigate
                class="{{ request()->routeIs('library.notices') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.notices') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                </svg>
                Notices
            </a>

            <a href="{{ route('library.settings') }}" wire:navigate
                class="{{ request()->routeIs('library.settings') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.settings') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Library Settings
            </a>
            @if(\App\Models\Setting::getBool('enable_support_tickets', false))
            <a href="{{ route('library.support') }}" wire:navigate
                class="{{ request()->routeIs('library.support') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('library.support') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Support
            </a>
            @endif
            @endif

            @if(auth()->user()->role === 'student')
            <div class="pt-6 pb-2">
                <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Student Portal</p>
            </div>

            <a href="{{ route('student.dashboard') }}" wire:navigate
                class="{{ request()->routeIs('student.dashboard') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('student.dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                My Dashboard
            </a>

            <a href="{{ route('student.attendance') }}" wire:navigate
                class="{{ request()->routeIs('student.attendance') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('student.attendance') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Attendance
            </a>

            <a href="{{ route('student.fees') }}" wire:navigate
                class="{{ request()->routeIs('student.fees') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('student.fees') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Fee History
            </a>

            <a href="{{ route('student.leaves') }}" wire:navigate
                class="{{ request()->routeIs('student.leaves') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('student.leaves') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Leave Requests
            </a>

            <a href="{{ route('student.notices') }}" wire:navigate
                class="{{ request()->routeIs('student.notices') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('student.notices') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                </svg>
                Notices
            </a>

            @if(\App\Models\Setting::getBool('enable_support_tickets', false))
            <a href="{{ route('student.support') }}" wire:navigate
                class="{{ request()->routeIs('student.support') ? 'bg-indigo-600/10 text-indigo-400 font-semibold' : 'hover:bg-slate-800/50 hover:text-white font-medium' }} group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('student.support') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Support
            </a>
            @endif
            @endif
        </nav>

        <!-- User Profile (Bottom of Sidebar) -->
        <div class="p-4 border-t border-slate-800 bg-slate-900/50">
            <div class="flex items-center gap-3">
                <div
                    class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold shrink-0 shadow-inner">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Top Header -->
    <header
        class="fixed top-0 right-0 left-0 md:left-64 h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 z-30 flex items-center justify-between px-4 sm:px-6 transition-all duration-300">
        <div class="flex items-center">
            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = true" type="button"
                class="md:hidden p-2 -ml-2 mr-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Right Side Nav -->
        <div class="flex items-center gap-4 ml-auto">
            <!-- Settings Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="flex items-center gap-2 p-1.5 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <div
                            class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <x-dropdown-link :href="route('profile')" wire:navigate
                        class="flex items-center py-2.5 text-sm font-medium text-gray-700">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Profile Settings') }}
                    </x-dropdown-link>

                    <div class="border-t border-gray-100"></div>

                    <button wire:click="logout" class="w-full text-start">
                        <x-dropdown-link
                            class="flex items-center py-2.5 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Sign out') }}
                        </x-dropdown-link>
                    </button>
                </x-slot>
            </x-dropdown>
        </div>
    </header>
</div>
