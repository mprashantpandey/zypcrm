<?php

use App\Livewire\Forms\LoginForm;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public array $allowedMethods = [];
    public bool $showDevTools = false;
    public array $demoUsers = [];

    public function mount(): void
    {
        $emailPasswordEnabled = Setting::getBool('email_password_auth_enabled', true);
        $phoneOtpEnabled = Setting::getBool('firebase_enabled', false) && Setting::getBool('firebase_phone_auth_enabled', false);

        $this->allowedMethods = array_filter([
            $emailPasswordEnabled ? 'Email/Password' : null,
            $phoneOtpEnabled ? 'Phone OTP' : null,
        ]);

        $this->showDevTools = app()->environment(['local', 'development']);

        if ($this->showDevTools) {
            try {
                $this->demoUsers = User::query()
                    ->select(['id', 'name', 'email', 'phone', 'role'])
                    ->whereIn('role', ['super_admin', 'library_owner', 'student'])
                    ->orderByRaw("FIELD(role, 'super_admin', 'library_owner', 'student')")
                    ->orderBy('id')
                    ->get()
                    ->toArray();
            } catch (\Throwable) {
                $this->demoUsers = [];
            }
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function loginAs(int $userId): void
    {
        if (! $this->showDevTools) {
            abort(403);
        }

        $user = User::query()
            ->whereKey($userId)
            ->whereIn('role', ['super_admin', 'library_owner', 'student'])
            ->firstOrFail();

        Auth::login($user);
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h1>
        <p class="mt-1 text-sm text-slate-500">Sign in to manage admissions, attendance, fees, and more.</p>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Allowed login methods</span>
            @forelse ($allowedMethods as $method)
                <span class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-[11px] font-medium text-indigo-700">{{ $method }}</span>
            @empty
                <span class="inline-flex items-center rounded-full border border-rose-100 bg-rose-50 px-2.5 py-1 text-[11px] font-medium text-rose-700">No method enabled</span>
            @endforelse
        </div>
    </div>

    @if ($showDevTools)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Development Mode</p>
            <p class="mt-1 text-sm text-amber-800">Demo credentials and one-click login are enabled.</p>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-left text-xs">
                    <thead>
                        <tr class="text-amber-700">
                            <th class="py-1 pr-3 font-semibold">Role</th>
                            <th class="py-1 pr-3 font-semibold">Email</th>
                            <th class="py-1 pr-3 font-semibold">Phone</th>
                            <th class="py-1 pr-3 font-semibold">Password</th>
                            <th class="py-1 pr-3 font-semibold">Quick Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($demoUsers as $user)
                            <tr class="text-amber-900">
                                <td class="py-1 pr-3">{{ str_replace('_', ' ', $user['role']) }}</td>
                                <td class="py-1 pr-3">{{ $user['email'] ?: '-' }}</td>
                                <td class="py-1 pr-3">{{ $user['phone'] ?: '-' }}</td>
                                <td class="py-1 pr-3">password123</td>
                                <td class="py-1 pr-3">
                                    <button type="button" wire:click="loginAs({{ $user['id'] }})"
                                        class="inline-flex items-center rounded-md bg-amber-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-amber-500">
                                        Login
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-2 text-amber-800">No demo users found. Run demo seeder first.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email or Phone -->
        <div>
            <label for="login" class="block text-sm font-medium text-slate-900">Email or Phone</label>
            <input wire:model="form.login" id="login"
                class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                type="text" name="login" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-900">Password</label>
            <input wire:model="form.password" id="password"
                class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" />
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between gap-4 pt-1">
            @if (Route::has('password.request'))
            <a class="text-sm font-medium text-indigo-600 hover:text-indigo-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                href="{{ route('password.request') }}" wire:navigate>
                {{ __('Forgot your password?') }}
            </a>
            @endif

            <button type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-300/40 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</div>
