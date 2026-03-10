<?php

namespace App\Notifications;

use App\Services\NotificationChannelService;
use App\Services\NotificationTemplateService;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeePaymentReceipt extends Notification
{
    use Queueable;

    public $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\FeePayment $payment)
    {
        $this->payment = $payment;
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
            ! $channelsConfig->canSend(NotificationChannelService::EVENT_FEE_PAYMENT_RECEIPT, 'email')
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
        $tenantName = $this->payment->tenant->name ?? 'Library';
        $formattedAmount = Setting::getCurrencySymbol('$').number_format((float) $this->payment->amount, 2);
        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_FEE_PAYMENT_RECEIPT,
            'email',
            [
                'app_name' => Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $notifiable->name,
                'tenant_name' => $tenantName,
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'amount' => $formattedAmount,
                'payment_date' => \Carbon\Carbon::parse($this->payment->payment_date)->format('M d, Y'),
                'payment_method' => ucfirst((string) $this->payment->payment_method),
                'payment_status' => ucfirst((string) $this->payment->status),
            ]
        );

        return (new MailMessage)
            ->subject($rendered['subject'] ?: "Payment Receipt - {$tenantName}")
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
