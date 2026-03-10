<?php

namespace App\Notifications;

use App\Models\FeePayment;
use App\Models\Setting;
use App\Services\NotificationChannelService;
use App\Services\NotificationTemplateService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeeDueReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public FeePayment $payment) {}

    public function via(object $notifiable): array
    {
        $channelsConfig = app(NotificationChannelService::class);
        if (
            ! $channelsConfig->canSend(NotificationChannelService::EVENT_FEE_DUE_REMINDER, 'email')
            || empty($notifiable->email)
        ) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->payment->tenant?->name ?? 'Library';
        $amount = Setting::getCurrencySymbol('$').number_format((float) $this->payment->amount, 2);
        $dueDate = Carbon::parse($this->payment->payment_date)->format('M d, Y');
        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_FEE_DUE_REMINDER,
            'email',
            [
                'app_name' => Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $notifiable->name,
                'tenant_name' => $tenantName,
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'amount' => $amount,
                'due_date' => $dueDate,
                'pay_url' => url('/pay/'.$this->payment->slug),
            ]
        );

        return (new MailMessage)
            ->subject($rendered['subject'] ?: "Fee Reminder - {$tenantName}")
            ->view('emails.dynamic-template', ['html' => $rendered['body']]);
    }
}
