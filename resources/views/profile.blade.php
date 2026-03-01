<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Profile Settings') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-slate-50 to-gray-100/60 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-4 sm:p-8">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-4 sm:p-8">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-red-100 bg-white shadow-sm">
                <div class="p-4 sm:p-8">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
