<?php

namespace App\Notifications;

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

        return (new MailMessage)
            ->subject("Action Required: Your Library Pass Expires Soon - {$tenantName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a friendly reminder that your active subscription ({$planName}) will expire on **{$expiryDate}**.")
            ->line("To avoid any interruption in your library access and preserve your currently assigned seat, please renew your membership.")
            ->action('Login to Dashboard', url('/dashboard'))
            ->line("If you have any questions or have already renewed, please contact the library desk.");
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