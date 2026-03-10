<?php

namespace App\Notifications;

use App\Models\Notice;
use App\Models\Setting;
use App\Services\NotificationChannelService;
use App\Services\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoticeBroadcastNotification extends Notification
{
    use Queueable;

    public function __construct(public Notice $notice) {}

    public function via(object $notifiable): array
    {
        $channelsConfig = app(NotificationChannelService::class);
        $channels = [];

        if (
            $this->notice->delivery_in_app
            && $channelsConfig->canSend(NotificationChannelService::EVENT_NOTICE_BROADCAST, 'in_app')
        ) {
            $channels[] = 'database';
        }

        if (
            $this->notice->delivery_email
            && $channelsConfig->canSend(NotificationChannelService::EVENT_NOTICE_BROADCAST, 'email')
            && ! empty($notifiable->email)
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_NOTICE_BROADCAST,
            'email',
            [
                'app_name' => Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $notifiable->name,
                'tenant_name' => $notifiable->tenant?->name ?? 'Library',
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'notice_title' => $this->notice->title,
                'notice_body' => $this->notice->body,
                'level' => $this->notice->level,
            ]
        );

        return (new MailMessage)
            ->subject($rendered['subject'] ?: ('New Notice: '.$this->notice->title))
            ->view('emails.dynamic-template', ['html' => $rendered['body']]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'notice',
            'notice_id' => $this->notice->id,
            'title' => $this->notice->title,
            'body' => $this->notice->body,
            'level' => $this->notice->level,
            'audience' => $this->notice->audience,
            'tenant_id' => $this->notice->tenant_id,
            'starts_at' => optional($this->notice->starts_at)?->toDateTimeString(),
            'ends_at' => optional($this->notice->ends_at)?->toDateTimeString(),
        ];
    }
}
