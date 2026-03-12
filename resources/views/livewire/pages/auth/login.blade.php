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
    public bool $phoneOtpEnabled = false;
    public array $firebaseConfig = [];

    public function mount(): void
    {
        $emailPasswordEnabled = Setting::getBool('email_password_auth_enabled', true);
        $phoneOtpEnabled = Setting::getBool('firebase_enabled', false) && Setting::getBool('firebase_phone_auth_enabled', false);

        $this->phoneOtpEnabled = $phoneOtpEnabled;

        $this->allowedMethods = array_filter([
            $emailPasswordEnabled ? 'Email/Password' : null,
            $phoneOtpEnabled ? 'Phone OTP (web & mobile)' : null,
        ]);

        if ($phoneOtpEnabled) {
            $serviceJson = Setting::getValue('firebase_service_account_json', null);
            $projectId = null;
            if (! empty($serviceJson)) {
                $decoded = json_decode($serviceJson, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $projectId = $decoded['project_id'] ?? null;
                }
            }

            $this->firebaseConfig = [
                'apiKey' => trim((string) Setting::getValue('firebase_api_key', '')),
                'authDomain' => trim((string) Setting::getValue('firebase_auth_domain', '')),
                'appId' => trim((string) Setting::getValue('firebase_app_id', '')),
                'projectId' => $projectId ? trim((string) $projectId) : null,
            ];
        }

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

    @if ($phoneOtpEnabled && ! empty($firebaseConfig['apiKey']) && ! empty($firebaseConfig['authDomain']) && ! empty($firebaseConfig['appId']))
        <div class="mt-10 border-t border-slate-200 pt-8">
            <h2 class="text-sm font-semibold text-slate-900">Or sign in with Phone OTP</h2>
            <p class="mt-1 text-xs text-slate-500">
                Uses Firebase phone authentication. Enter your phone number and verify the OTP to sign in.
            </p>

            <div id="phone-otp-login" class="mt-4 space-y-4">
                <div>
                    <label for="phone-login-number" class="block text-sm font-medium text-slate-900">Phone number</label>
                    <input id="phone-login-number"
                           type="tel"
                           class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                           placeholder="+91 84491 83686">
                </div>

                <div class="flex items-center gap-3">
                    <button id="phone-login-send-otp"
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                        Send OTP
                    </button>
                    <span id="phone-login-status"
                          class="text-xs text-slate-500"></span>
                </div>

                <div id="phone-login-otp-section" class="hidden space-y-3">
                    <div>
                        <label for="phone-login-otp" class="block text-sm font-medium text-slate-900">Enter OTP</label>
                        <input id="phone-login-otp"
                               type="text"
                               maxlength="6"
                               class="mt-2 block w-40 rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                               placeholder="123456">
                    </div>
                    <button id="phone-login-verify-otp"
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Verify & Sign In
                    </button>
                </div>
            </div>

            <div id="phone-login-recaptcha-container"></div>
        </div>

        @php
            $firebaseConfigJson = json_encode($firebaseConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @endphp

        {{-- Firebase JS SDK (compat) – pinned to latest major 12.x --}}
        <script src="https://www.gstatic.com/firebasejs/12.10.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/12.10.0/firebase-auth-compat.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const cfg = {!! $firebaseConfigJson !!};
                if (!cfg || !cfg.apiKey || !cfg.authDomain || !cfg.appId) {
                    return;
                }

                // Expose for debugging in browser console (window._firebaseCfg)
                window._firebaseCfg = cfg;

                if (!window.firebase || !window.firebase.initializeApp) {
                    console.error('Firebase SDK not loaded');
                    return;
                }

                let firebaseApp;
                try {
                    firebaseApp = firebase.apps.length ? firebase.app() : firebase.initializeApp(cfg);
                } catch (e) {
                    console.error('Failed to init Firebase app', e);
                    return;
                }

                const auth = firebase.auth(firebaseApp);

                const phoneInput = document.getElementById('phone-login-number');
                const sendOtpBtn = document.getElementById('phone-login-send-otp');
                const otpSection = document.getElementById('phone-login-otp-section');
                const otpInput = document.getElementById('phone-login-otp');
                const verifyOtpBtn = document.getElementById('phone-login-verify-otp');
                const statusEl = document.getElementById('phone-login-status');

                if (!phoneInput || !sendOtpBtn || !otpSection || !otpInput || !verifyOtpBtn || !statusEl) {
                    return;
                }

                let confirmationResult = null;
                let sending = false;

                function setStatus(message, isError) {
                    statusEl.textContent = message || '';
                    statusEl.classList.toggle('text-rose-600', !!isError);
                    statusEl.classList.toggle('text-slate-500', !isError);
                }

                function setSending(isSending) {
                    sending = isSending;
                    sendOtpBtn.disabled = isSending;
                    verifyOtpBtn.disabled = isSending;
                    sendOtpBtn.classList.toggle('opacity-60', isSending);
                    verifyOtpBtn.classList.toggle('opacity-60', isSending);
                }

                function ensureRecaptcha() {
                    if (window.recaptchaVerifier) {
                        return window.recaptchaVerifier;
                    }
                    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('phone-login-recaptcha-container', {
                        'size': 'invisible'
                    }, auth);
                    return window.recaptchaVerifier;
                }

                sendOtpBtn.addEventListener('click', function () {
                    if (sending) return;
                    const raw = phoneInput.value.trim();
                    if (!raw) {
                        setStatus('Enter phone number', true);
                        return;
                    }
                    const phone = raw.startsWith('+') ? raw : '+91' + raw;
                    setSending(true);
                    setStatus('Sending OTP...', false);

                    const verifier = ensureRecaptcha();

                    auth.signInWithPhoneNumber(phone, verifier)
                        .then(function (result) {
                            confirmationResult = result;
                            otpSection.classList.remove('hidden');
                            setStatus('OTP sent. Please check your phone.', false);
                            setSending(false);
                        })
                        .catch(function (error) {
                            console.error(error);
                            setStatus(error.message || 'Failed to send OTP', true);
                            setSending(false);
                        });
                });

                verifyOtpBtn.addEventListener('click', function () {
                    if (sending) return;
                    if (!confirmationResult) {
                        setStatus('Please request OTP first.', true);
                        return;
                    }
                    const code = otpInput.value.trim();
                    if (!code) {
                        setStatus('Enter the OTP code', true);
                        return;
                    }
                    setSending(true);
                    setStatus('Verifying OTP...', false);

                    confirmationResult.confirm(code)
                        .then(function (result) {
                            return result.user.getIdToken();
                        })
                        .then(function (idToken) {
                            return fetch('/api/auth/firebase', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ firebase_id_token: idToken })
                            });
                        })
                        .then(function (res) {
                            return res.json().then(function (data) {
                                return { ok: res.ok, data: data };
                            });
                        })
                        .then(function (resp) {
                            if (!resp.ok) {
                                throw new Error(resp.data && resp.data.message ? resp.data.message : 'Login failed');
                            }
                            const token = resp.data && resp.data.access_token;
                            if (!token) {
                                throw new Error('Missing access token from server');
                            }
                            var csrf = document.querySelector('meta[name="csrf-token"]');
                            return fetch('/login/phone/callback', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': (csrf && csrf.getAttribute('content')) || ''
                                },
                                body: JSON.stringify({ token: token })
                            });
                        })
                        .then(function (res) {
                            return res.json().then(function (data) {
                                return { ok: res.ok, data: data };
                            });
                        })
                        .then(function (resp) {
                            if (!resp.ok) {
                                throw new Error(resp.data && resp.data.message ? resp.data.message : 'Session setup failed');
                            }
                            if (resp.data && resp.data.redirect) {
                                window.location.href = resp.data.redirect;
                            } else {
                                window.location.href = '/dashboard';
                            }
                        })
                        .catch(function (error) {
                            console.error(error);
                            setStatus(error.message || 'Login failed', true);
                            setSending(false);
                        });
                });
            });
        </script>
    @endif
</div>
