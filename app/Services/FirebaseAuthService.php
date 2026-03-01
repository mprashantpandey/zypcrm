<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

/**
 * Firebase Authentication Service
 *
 * Verifies Firebase ID tokens sent from the mobile app after
 * the user completes Phone OTP authentication on the device.
 *
 * Uses the same Service Account JSON stored in Platform Settings
 * as the notification service — no extra credentials needed.
 */
class FirebaseAuthService
{
    protected ?Auth $auth = null;

    /**
     * Build and return the Firebase Auth client (lazy + cached per request).
     *
     * @throws Exception if the service account JSON is missing / invalid.
     */
    protected function getAuth(): Auth
    {
        if ($this->auth !== null) {
            return $this->auth;
        }

        $json = Setting::where('key', 'firebase_service_account_json')->value('value');

        if (empty($json)) {
            throw new Exception(
                'Firebase service account JSON is not configured. ' .
                'Go to Platform Settings → Firebase & Push and paste your service account JSON.'
                );
        }

        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($decoded['project_id'])) {
            throw new Exception('Firebase service account JSON is invalid or missing the "project_id" field.');
        }

        $this->auth = (new Factory)
            ->withServiceAccount($decoded)
            ->createAuth();

        return $this->auth;
    }

    /**
     * Verify a Firebase ID token from the mobile app.
     *
     * Returns a decoded token payload array:
     *   [
     *     'uid'          => 'firebase-uid',
     *     'phone_number' => '+911234567890',   // may be null for non-phone auth
     *     'email'        => 'user@example.com', // may be null for phone auth
     *   ]
     *
     * @throws Exception if the token is invalid, expired, or revoked.
     */
    public function verifyIdToken(string $idToken): array
    {
        $auth = $this->getAuth();

        // verifiedIdToken() throws if the token is invalid / expired
        $verifiedToken = $auth->verifyIdToken($idToken);

        $claims = $verifiedToken->claims();

        return [
            'uid' => $claims->get('sub'), // Firebase UID
            'phone_number' => $claims->get('phone_number'),
            'email' => $claims->get('email'),
            'name' => $claims->get('name'),
        ];
    }
}