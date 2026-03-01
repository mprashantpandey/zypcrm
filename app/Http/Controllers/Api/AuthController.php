<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function allowedLoginMethods(): array
    {
        return [
            'email_password' => Setting::getBool('email_password_auth_enabled', true),
            'phone_otp' => Setting::getBool('firebase_enabled', false) && Setting::getBool('firebase_phone_auth_enabled', false),
        ];
    }

    public function registerLibraryOwner(Request $request)
    {
        if (! Setting::getBool('allow_registration', true)) {
            return response()->json(['message' => 'Registration is currently disabled by admin'], 403);
        }

        $validated = $request->validate([
            'library_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        [$tenant, $user] = DB::transaction(function () use ($validated) {
            $tenant = Tenant::create([
                'name' => $validated['library_name'],
                'status' => 'active',
            ]);

            $user = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'library_owner',
                'tenant_id' => $tenant->id,
            ]);

            return [$tenant, $user];
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'tenant' => $tenant,
        ]);
    }

    public function login(Request $request)
    {
        $allowedMethods = $this->allowedLoginMethods();

        if (! $allowedMethods['email_password']) {
            return response()->json([
                'message' => 'Email/password login is currently disabled by admin',
                'allowed_login_methods' => $allowedMethods,
            ], 403);
        }

        $request->validate([
            'login' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required|string',
        ]);

        $login = $request->string('login')->toString();
        if ($login === '') {
            $login = $request->string('email')->toString();
        }

        if ($login === '') {
            return response()->json([
                'message' => 'The login field is required.',
                'allowed_login_methods' => $allowedMethods,
            ], 422);
        }

        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;
        $user = User::where($isEmail ? 'email' : 'phone', $login)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'allowed_login_methods' => $allowedMethods,
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'tenant' => $user->tenant,
            'allowed_login_methods' => $allowedMethods,
        ]);
    }

    /**
     * Authenticate a mobile user via Firebase Phone Auth ID token.
     *
     * Flow:
     *  1. Flutter app completes Firebase Phone OTP → receives ID token
     *  2. Sends { "firebase_id_token": "..." } to this endpoint
     *  3. We verify the token with Firebase Admin SDK
     *  4. Find or create a User record keyed by firebase_uid / phone
     *  5. Return a Sanctum token so the app can call protected API routes
     */
    public function firebaseLogin(Request $request, FirebaseAuthService $firebase)
    {
        $allowedMethods = $this->allowedLoginMethods();

        if (! $allowedMethods['phone_otp']) {
            return response()->json([
                'message' => 'Phone OTP login is currently disabled by admin',
                'allowed_login_methods' => $allowedMethods,
            ], 403);
        }

        $request->validate([
            'firebase_id_token' => 'required|string',
            'tenant_id' => 'nullable|integer|exists:tenants,id',
        ]);

        try {
            $payload = $firebase->verifyIdToken($request->firebase_id_token);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Firebase token verification failed',
                'allowed_login_methods' => $allowedMethods,
            ], 401);
        }

        $uid = $payload['uid'];
        $phone = $payload['phone_number'];
        $name = $payload['name'] ?? 'Student';

        if (empty($uid)) {
            return response()->json([
                'message' => 'Invalid Firebase token: missing uid',
                'allowed_login_methods' => $allowedMethods,
            ], 422);
        }

        if (empty($phone)) {
            return response()->json([
                'message' => 'Invalid Firebase token: missing phone number',
                'allowed_login_methods' => $allowedMethods,
            ], 422);
        }

        // Find existing user by firebase_uid, or create a new student
        $user = User::where('firebase_uid', $uid)->first();

        if (! $user && $phone) {
            // Also try matching by phone number (handles re-installs / uid reuse edge case)
            $user = User::where('phone', $phone)->first();
        }

        if (! $user) {
            // First-time login — create a student account
            $tenantId = $request->tenant_id; // Optional: mobile app passes the library's tenant_id

            $user = User::create([
                'name' => $name,
                'email' => $phone ? $phone.'@firebase.phone' : null, // placeholder email
                'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                'role' => 'student',
                'tenant_id' => $tenantId,
                'phone' => $phone,
                'firebase_uid' => $uid,
            ]);
        } else {
            // Update firebase_uid in case we matched via phone on first run
            $user->update(['firebase_uid' => $uid]);
        }

        // Revoke old mobile tokens to prevent accumulation
        $user->tokens()->where('name', 'mobile_token')->delete();
        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->only(['id', 'name', 'phone', 'role', 'tenant_id']),
            'tenant' => $user->tenant,
            'is_new_user' => $user->wasRecentlyCreated,
            'allowed_login_methods' => $allowedMethods,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
