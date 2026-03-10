<?php

namespace App\Notifications;

use App\Models\StudentLeave;
use App\Models\Setting;
use App\Services\NotificationChannelService;
use App\Services\NotificationTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentLeaveStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public StudentLeave $leave,
        public string $status,
        public int $extendedDays = 0,
        public ?string $updatedSubscriptionEndDate = null
    ) {}

    public function via(object $notifiable): array
    {
        $channelsConfig = app(NotificationChannelService::class);
        $channels = [];

        if ($channelsConfig->canSend(NotificationChannelService::EVENT_LEAVE_STATUS, 'in_app')) {
            $channels[] = 'database';
        }

        if (
            $channelsConfig->canSend(NotificationChannelService::EVENT_LEAVE_STATUS, 'email')
            && ! empty($notifiable->email)
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $start = $this->leave->start_date?->format('M d, Y') ?? (string) $this->leave->start_date;
        $end = $this->leave->end_date?->format('M d, Y') ?? (string) $this->leave->end_date;
        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_LEAVE_STATUS,
            'email',
            [
                'app_name' => Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $notifiable->name,
                'tenant_name' => $notifiable->tenant?->name ?? 'Library',
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'status' => $this->status,
                'start_date' => $start,
                'end_date' => $end,
                'extended_days' => (string) $this->extendedDays,
                'updated_end_date' => (string) ($this->updatedSubscriptionEndDate ?? '-'),
            ]
        );

        return (new MailMessage)
            ->subject($rendered['subject'] ?: 'Leave Request Update')
            ->view('emails.dynamic-template', ['html' => $rendered['body']]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'student_leave_status',
            'leave_id' => $this->leave->id,
            'status' => $this->status,
            'start_date' => (string) $this->leave->start_date,
            'end_date' => (string) $this->leave->end_date,
            'reason' => (string) $this->leave->reason,
            'extended_days' => $this->extendedDays,
            'updated_subscription_end_date' => $this->updatedSubscriptionEndDate,
        ];
    }
}
