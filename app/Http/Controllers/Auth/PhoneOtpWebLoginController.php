<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
     * for a Sanctum personal access token. That token is then sent here (prefer POST body
     * so it does not appear in URLs or server logs).
     *
     * - GET ?token=... — redirects to dashboard (backward compatible).
     * - POST { "token": "..." } — returns JSON { "redirect": "..." } so the client can
     *   navigate without putting the token in the URL.
     */
    public function __invoke(Request $request): RedirectResponse|JsonResponse
    {
        $plainTextToken = $request->filled('token')
            ? $request->input('token')
            : $request->query('token');

        if (! is_string($plainTextToken) || $plainTextToken === '') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Missing token'], 400);
            }
            abort(400, 'Missing token');
        }

        $accessToken = PersonalAccessToken::findToken(trim($plainTextToken));

        if (! $accessToken) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Invalid or expired token'], 403);
            }
            abort(403, 'Invalid or expired token');
        }

        $user = $accessToken->tokenable;
        $accessToken->delete();

        Auth::login($user, true);
        $request->session()->regenerate();

        $redirectUrl = redirect()->intended(route('dashboard'))->getTargetUrl();

        if ($request->isMethod('POST') && ($request->expectsJson() || $request->ajax())) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect()->intended(route('dashboard'));
    }
}

