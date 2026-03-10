<?php

namespace App\Services;

use App\Models\NotificationTemplate;

class NotificationTemplateService
{
    public const EVENTS = [
        NotificationChannelService::EVENT_NOTICE_BROADCAST,
        NotificationChannelService::EVENT_LEAVE_STATUS,
        NotificationChannelService::EVENT_FEE_DUE_REMINDER,
        NotificationChannelService::EVENT_FEE_PAYMENT_RECEIPT,
        NotificationChannelService::EVENT_SUBSCRIPTION_EXPIRY,
    ];

    public const CHANNELS = ['email', 'sms', 'whatsapp'];

    public function getTemplate(string $eventKey, string $channel): ?NotificationTemplate
    {
        return NotificationTemplate::query()
            ->where('event_key', $eventKey)
            ->where('channel', $channel)
            ->first();
    }

    public function upsertTemplate(string $eventKey, string $channel, array $payload): NotificationTemplate
    {
        return NotificationTemplate::query()->updateOrCreate(
            ['event_key' => $eventKey, 'channel' => $channel],
            [
                'name' => $payload['name'] ?? $this->defaultName($eventKey, $channel),
                'subject' => $payload['subject'] ?? null,
                'body' => $payload['body'] ?? '',
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]
        );
    }

    public function seedDefaults(): void
    {
        foreach (self::EVENTS as $eventKey) {
            foreach (self::CHANNELS as $channel) {
                $defaults = $this->defaultTemplate($eventKey, $channel);
                $this->upsertTemplate($eventKey, $channel, $defaults);
            }
        }
    }

    public function render(string $eventKey, string $channel, array $variables = []): array
    {
        $template = $this->getTemplate($eventKey, $channel);
        if (! $template || ! $template->is_active) {
            $defaults = $this->defaultTemplate($eventKey, $channel);

            return [
                'subject' => $this->replaceVars((string) ($defaults['subject'] ?? ''), $variables),
                'body' => $this->replaceVars((string) ($defaults['body'] ?? ''), $variables),
                'is_active' => $template?->is_active ?? true,
            ];
        }

        return [
            'subject' => $this->replaceVars((string) $template->subject, $variables),
            'body' => $this->replaceVars((string) $template->body, $variables),
            'is_active' => (bool) $template->is_active,
        ];
    }

    public function getVariableMap(string $eventKey): array
    {
        $common = [
            'app_name',
            'site_title',
            'user_name',
            'tenant_name',
            'dashboard_url',
            'date',
        ];

        return match ($eventKey) {
            NotificationChannelService::EVENT_NOTICE_BROADCAST => array_merge($common, ['notice_title', 'notice_body', 'level']),
            NotificationChannelService::EVENT_LEAVE_STATUS => array_merge($common, ['status', 'start_date', 'end_date', 'extended_days', 'updated_end_date']),
            NotificationChannelService::EVENT_FEE_DUE_REMINDER => array_merge($common, ['amount', 'due_date', 'pay_url']),
            NotificationChannelService::EVENT_FEE_PAYMENT_RECEIPT => array_merge($common, ['amount', 'payment_date', 'payment_method', 'payment_status']),
            NotificationChannelService::EVENT_SUBSCRIPTION_EXPIRY => array_merge($common, ['plan_name', 'expiry_date']),
            default => $common,
        };
    }

    public function defaultTemplate(string $eventKey, string $channel): array
    {
        $name = $this->defaultName($eventKey, $channel);
        $subject = match ($eventKey) {
            NotificationChannelService::EVENT_NOTICE_BROADCAST => 'New Notice: {{notice_title}}',
            NotificationChannelService::EVENT_LEAVE_STATUS => 'Leave request {{status}}',
            NotificationChannelService::EVENT_FEE_DUE_REMINDER => 'Fee Reminder - {{tenant_name}}',
            NotificationChannelService::EVENT_FEE_PAYMENT_RECEIPT => 'Payment Receipt - {{tenant_name}}',
            NotificationChannelService::EVENT_SUBSCRIPTION_EXPIRY => 'Subscription Expiry Alert - {{tenant_name}}',
            default => 'Notification from {{app_name}}',
        };

        $body = match ($eventKey) {
            NotificationChannelService::EVENT_NOTICE_BROADCAST => $channel === 'email'
                ? '<div style="font-family:Arial,sans-serif;background:#f8fafc;padding:24px"><div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px"><p style="margin:0 0 8px;color:#6b7280">Hello {{user_name}},</p><h2 style="margin:0 0 10px;color:#111827">{{notice_title}}</h2><p style="margin:0 0 16px;color:#374151">{{notice_body}}</p><a href="{{dashboard_url}}" style="display:inline-block;background:#4f46e5;color:#fff;text-decoration:none;padding:10px 14px;border-radius:8px">Open Dashboard</a></div></div>'
                : '{{notice_title}}: {{notice_body}}',
            NotificationChannelService::EVENT_LEAVE_STATUS => $channel === 'email'
                ? '<div style="font-family:Arial,sans-serif;background:#f8fafc;padding:24px"><div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px"><p style="margin:0 0 8px;color:#6b7280">Hello {{user_name}},</p><h2 style="margin:0 0 10px;color:#111827">Leave Request {{status}}</h2><p style="margin:0;color:#374151">Leave period: {{start_date}} to {{end_date}}</p><p style="margin:6px 0 0;color:#374151">Extended days: {{extended_days}}</p><p style="margin:6px 0 0;color:#374151">Updated expiry: {{updated_end_date}}</p></div></div>'
                : 'Leave request {{status}} for {{start_date}} to {{end_date}}. Extension days: {{extended_days}}. Updated expiry: {{updated_end_date}}.',
            NotificationChannelService::EVENT_FEE_DUE_REMINDER => $channel === 'email'
                ? '<div style="font-family:Arial,sans-serif;background:#fff7ed;padding:24px"><div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #fed7aa;border-radius:12px;padding:24px"><p style="margin:0 0 8px;color:#6b7280">Hello {{user_name}},</p><h2 style="margin:0 0 10px;color:#9a3412">Fee Reminder</h2><p style="margin:0;color:#374151">Pending amount: <strong>{{amount}}</strong></p><p style="margin:6px 0 16px;color:#374151">Due date: {{due_date}}</p><a href="{{pay_url}}" style="display:inline-block;background:#ea580c;color:#fff;text-decoration:none;padding:10px 14px;border-radius:8px">Pay Now</a></div></div>'
                : 'Pending fee {{amount}} due by {{due_date}}. Pay now: {{pay_url}}',
            NotificationChannelService::EVENT_FEE_PAYMENT_RECEIPT => $channel === 'email'
                ? '<div style="font-family:Arial,sans-serif;background:#ecfeff;padding:24px"><div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #a5f3fc;border-radius:12px;padding:24px"><p style="margin:0 0 8px;color:#6b7280">Hello {{user_name}},</p><h2 style="margin:0 0 10px;color:#0f766e">Payment Receipt</h2><p style="margin:0;color:#374151">Amount: <strong>{{amount}}</strong></p><p style="margin:6px 0 0;color:#374151">Date: {{payment_date}}</p><p style="margin:6px 0 0;color:#374151">Method: {{payment_method}}</p><p style="margin:6px 0 0;color:#374151">Status: {{payment_status}}</p></div></div>'
                : 'Payment of {{amount}} received on {{payment_date}} via {{payment_method}}. Status: {{payment_status}}.',
            NotificationChannelService::EVENT_SUBSCRIPTION_EXPIRY => $channel === 'email'
                ? '<div style="font-family:Arial,sans-serif;background:#fef2f2;padding:24px"><div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #fecaca;border-radius:12px;padding:24px"><p style="margin:0 0 8px;color:#6b7280">Hello {{user_name}},</p><h2 style="margin:0 0 10px;color:#b91c1c">Subscription Expiry Alert</h2><p style="margin:0;color:#374151">Plan: {{plan_name}}</p><p style="margin:6px 0 16px;color:#374151">Expiry date: {{expiry_date}}</p><a href="{{dashboard_url}}" style="display:inline-block;background:#dc2626;color:#fff;text-decoration:none;padding:10px 14px;border-radius:8px">Renew from Dashboard</a></div></div>'
                : 'Your {{plan_name}} subscription expires on {{expiry_date}}. Please renew to avoid interruption.',
            default => 'Hello {{user_name}}, this is a notification from {{app_name}}.',
        };

        return [
            'name' => $name,
            'subject' => $channel === 'email' ? $subject : null,
            'body' => $body,
            'is_active' => true,
        ];
    }

    private function replaceVars(string $content, array $variables): string
    {
        $replacements = [];
        foreach ($variables as $key => $value) {
            $replacements['{{'.$key.'}}'] = (string) $value;
        }

        return strtr($content, $replacements);
    }

    private function defaultName(string $eventKey, string $channel): string
    {
        return strtoupper($channel).' - '.str_replace('_', ' ', strtoupper($eventKey));
    }
}
