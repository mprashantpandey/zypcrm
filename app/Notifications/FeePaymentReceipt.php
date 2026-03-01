<?php

namespace App\Notifications;

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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->payment->tenant->name ?? 'Library';
        $formattedAmount = number_format($this->payment->amount, 2);

        return (new MailMessage)
            ->subject("Payment Receipt - {$tenantName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("We have received your payment of {$formattedAmount}.")
            ->line("Payment Details:")
            ->line("- Date: " . \Carbon\Carbon::parse($this->payment->payment_date)->format('M d, Y'))
            ->line("- Method: " . ucfirst($this->payment->payment_method))
            ->line("- Status: " . ucfirst($this->payment->status))
            ->action('View Dashboard', url('/dashboard'))
            ->line("Thank you for choosing {$tenantName}!");
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