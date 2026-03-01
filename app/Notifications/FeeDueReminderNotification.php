<?php

namespace App\Notifications;

use App\Models\FeePayment;
use App\Models\Setting;
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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->payment->tenant?->name ?? 'Library';
        $amount = Setting::getCurrencySymbol('$').number_format((float) $this->payment->amount, 2);
        $dueDate = Carbon::parse($this->payment->payment_date)->format('M d, Y');
        $isOverdue = Carbon::parse($this->payment->payment_date)->isPast();

        return (new MailMessage)
            ->subject("Fee Reminder - {$tenantName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder for your pending fee payment of {$amount}.")
            ->line("Due date: {$dueDate}")
            ->line($isOverdue
                ? 'Your payment is overdue. Please complete it as soon as possible.'
                : 'Please complete payment on or before the due date.')
            ->action('Pay Now', url('/pay/'.$this->payment->slug))
            ->line("If you already paid, please ignore this email.");
    }
}
