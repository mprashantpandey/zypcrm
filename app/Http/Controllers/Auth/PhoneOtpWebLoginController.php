<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class PhoneOtpWebLoginController extends Controller
{
    /**
     * Complete a web login that started via Firebase Phone OTP in the browser.
     *
     * The browser first calls POST /api/auth/firebase to exchange the Firebase ID token
     * for a Sanctum personal access token. That token is then passed to this route,
     * which looks up the owning user and logs them into the web session.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $plainTextToken = $request->query('token');

        if (! $plainTextToken) {
            abort(400, 'Missing token');
        }

        $accessToken = PersonalAccessToken::findToken($plainTextToken);

        if (! $accessToken) {
            abort(403, 'Invalid or expired token');
        }

        $user = $accessToken->tokenable;

        // Optionally revoke this token so it is only used once for web login
        $accessToken->delete();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}

