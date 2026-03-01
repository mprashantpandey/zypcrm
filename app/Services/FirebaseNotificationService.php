<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;

/**
 * Firebase Cloud Messaging via FCM HTTP v1 API (Admin SDK)
 *
 * Uses kreait/firebase-php v7 with full service-account JSON credentials
 * downloaded from: Firebase Console → Project Settings → Service Accounts
 * → "Generate new private key".
 *
 * The JSON is stored as a single setting key "firebase_service_account_json"
 * in the platform settings table.
 */
class FirebaseNotificationService
{
    protected ?\Kreait\Firebase\Contract\Messaging $messaging = null;

    /**
     * Build and return the Firebase Messaging client.
     * Lazily initialised and cached per request.
     *
     * @throws Exception if the service account JSON is missing / invalid.
     */
    protected function getMessaging(): \Kreait\Firebase\Contract\Messaging
    {
        if ($this->messaging !== null) {
            return $this->messaging;
        }

        $json = Setting::where('key', 'firebase_service_account_json')->value('value');

        if (empty($json)) {
            throw new Exception(
                'Firebase service account JSON is not configured. ' .
                'Go to Platform Settings → Firebase & Push and paste your service account JSON.'
                );
        }

        // Validate it's real JSON before passing to the factory
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($decoded['project_id'])) {
            throw new Exception('Firebase service account JSON is invalid or missing the "project_id" field.');
        }

        // kreait/firebase-php v7 Factory accepts a JSON string or file path
        $this->messaging = (new Factory)
            ->withServiceAccount($decoded)
            ->createMessaging();

        return $this->messaging;
    }

    // ─── Core Senders ────────────────────────────────────────────────────────────

    /**
     * Send to a single FCM registration token.
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): array
    {
        $messaging = $this->getMessaging();

        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $messaging->send($message);
            return ['success' => true, 'sent' => 1, 'failures' => 0];
        }
        catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            // Token is no longer valid — caller should remove it
            return ['success' => false, 'sent' => 0, 'failures' => 1, 'reason' => 'token_not_found'];
        }
        catch (\Kreait\Firebase\Exception\MessagingException $e) {
            return ['success' => false, 'sent' => 0, 'failures' => 1, 'reason' => $e->getMessage()];
        }
    }

    /**
     * Send to multiple FCM tokens (up to 500 per call as per FCM limits).
     */
    public function sendToMultiple(array $fcmTokens, string $title, string $body, array $data = []): array
    {
        if (empty($fcmTokens)) {
            return ['success' => true, 'sent' => 0, 'failures' => 0];
        }

        $messaging = $this->getMessaging();

        // Build one message per token (multicast)
        $messages = array_map(
        fn(string $token) => CloudMessage::withTarget('token', $token)
        ->withNotification(Notification::create($title, $body))
        ->withData($data),
            $fcmTokens
        );

        $report = $messaging->sendAll($messages);

        return [
            'success' => true,
            'sent' => $report->successes()->count(),
            'failures' => $report->failures()->count(),
        ];
    }

    /**
     * Subscribe tokens to a topic and send to the topic.
     * Useful for broadcast notifications (e.g. "all_tenants").
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        $messaging = $this->getMessaging();

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $messaging->send($message);
            return ['success' => true];
        }
        catch (\Kreait\Firebase\Exception\MessagingException $e) {
            return ['success' => false, 'reason' => $e->getMessage()];
        }
    }

    // ─── Domain-specific helpers ─────────────────────────────────────────────────

    /**
     * Notify a student about an upcoming/overdue fee.
     */
    public function sendFeeReminder(string $fcmToken, string $studentName, float $dueAmount, ?string $currency = null): array
    {
        $currencySymbol = $currency ?: Setting::getCurrencySymbol('$');

        return $this->sendToDevice(
            $fcmToken,
            '💳 Fee Payment Reminder',
            "Hi {$studentName}, your fee of {$currencySymbol}{$dueAmount} is due. Please pay to avoid late charges.",
        ['type' => 'fee_reminder', 'amount' => (string)$dueAmount]
        );
    }

    /**
     * Warn a library owner their subscription is about to expire.
     */
    public function sendSubscriptionExpiry(string $fcmToken, string $libraryName, int $daysLeft): array
    {
        return $this->sendToDevice(
            $fcmToken,
            '⚠️ Subscription Expiring Soon',
            "{$libraryName}'s subscription expires in {$daysLeft} day(s). Renew now to avoid interruption.",
        ['type' => 'subscription_expiry', 'days_left' => (string)$daysLeft]
        );
    }

    /**
     * Broadcast a platform-wide announcement to all tenants via a topic.
     */
    public function broadcastAnnouncement(string $subject, string $body): array
    {
        return $this->sendToTopic('all_tenants', $subject, $body, ['type' => 'announcement']);
    }

    // ─── Utility ────────────────────────────────────────────────────────────────

    /**
     * Subscribe an FCM token to a topic (e.g. "all_tenants", "fee_reminders").
     */
    public function subscribeToTopic(string $fcmToken, string $topic): void
    {
        $this->getMessaging()->subscribeToTopic($topic, [$fcmToken]);
    }

    /**
     * Validate a token is still registered with FCM.
     */
    public function validateToken(string $fcmToken): bool
    {
        try {
            // sendOne with dry_run=true is the most reliable validation method in kreait v7
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(Notification::create('validate', 'validate'));
            $this->getMessaging()->validate($message);
            return true;
        }
        catch (\Kreait\Firebase\Exception\Messaging\NotFound) {
            return false;
        }
        catch (\Throwable) {
            return false;
        }
    }
}
