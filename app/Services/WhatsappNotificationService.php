<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class WhatsappNotificationService
{
    /**
     * Placeholder provider implementation.
     * Replace this with actual provider SDK/API integration later.
     */
    public function sendMessage(string $phone, string $message): array
    {
        $provider = Setting::getValue('whatsapp_provider_name', 'placeholder');
        $enabled = Setting::getBool('whatsapp_provider_enabled', false);
        $apiBaseUrl = Setting::getValue('whatsapp_api_base_url');
        $sender = Setting::getValue('whatsapp_sender_id');

        if (! $enabled) {
            return ['success' => false, 'reason' => 'provider_disabled'];
        }

        Log::info('WhatsApp placeholder dispatch', [
            'provider' => $provider,
            'to' => $phone,
            'sender' => $sender,
            'api_base_url' => $apiBaseUrl,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'provider' => $provider,
            'message' => 'placeholder_sent',
        ];
    }
}

