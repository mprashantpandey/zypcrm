<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Services\NotificationChannelService;
use App\Services\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiryNotification extends Notification
{
    use Queueable;

    public $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\StudentSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channelsConfig = app(NotificationChannelService::class);
        if (
            ! $channelsConfig->canSend(NotificationChannelService::EVENT_SUBSCRIPTION_EXPIRY, 'email')
            || empty($notifiable->email)
        ) {
            return [];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->subscription->tenant->name ?? 'Library';
        $planName = $this->subscription->plan->name ?? 'Plan';
        $expiryDate = \Carbon\Carbon::parse($this->subscription->end_date)->format('M d, Y');
        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_SUBSCRIPTION_EXPIRY,
            'email',
            [
                'app_name' => Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $notifiable->name,
                'tenant_name' => $tenantName,
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'plan_name' => $planName,
                'expiry_date' => $expiryDate,
            ]
        );

        return (new MailMessage)
            ->subject($rendered['subject'] ?: "Action Required: Your Library Pass Expires Soon - {$tenantName}")
            ->view('emails.dynamic-template', ['html' => $rendered['body']]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
