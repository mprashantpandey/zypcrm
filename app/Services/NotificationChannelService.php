<?php

namespace App\Services;

use App\Models\Setting;

class NotificationChannelService
{
    public const EVENT_NOTICE_BROADCAST = 'notice_broadcast';
    public const EVENT_LEAVE_STATUS = 'leave_status';
    public const EVENT_FEE_DUE_REMINDER = 'fee_due_reminder';
    public const EVENT_FEE_PAYMENT_RECEIPT = 'fee_payment_receipt';
    public const EVENT_SUBSCRIPTION_EXPIRY = 'subscription_expiry';

    public function isEmailEnabled(): bool
    {
        return Setting::getBool('notification_email_enabled', true);
    }

    public function isPushEnabled(): bool
    {
        return Setting::getBool('notification_push_enabled', true);
    }

    public function isWhatsappEnabled(): bool
    {
        return Setting::getBool('notification_whatsapp_enabled', false);
    }

    public function isEventEnabled(string $event): bool
    {
        return Setting::getBool("notification_event_{$event}_enabled", true);
    }

    public function canSend(string $event, string $channel): bool
    {
        if (! $this->isEventEnabled($event)) {
            return false;
        }

        return match ($channel) {
            'email' => $this->isEmailEnabled(),
            'push' => $this->isPushEnabled() && Setting::getBool('firebase_enabled', false),
            'whatsapp' => $this->isWhatsappEnabled(),
            'in_app' => true,
            default => false,
        };
    }
}
