<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\User;
use App\Notifications\NoticeBroadcastNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NoticeDispatchService
{
    public function dispatch(Notice $notice): int
    {
        $channelsConfig = app(NotificationChannelService::class);
        $recipients = $this->resolveRecipients($notice);

        foreach ($recipients as $recipient) {
            $recipient->notify(new NoticeBroadcastNotification($notice));

            if ($notice->delivery_push && $channelsConfig->canSend(NotificationChannelService::EVENT_NOTICE_BROADCAST, 'push')) {
                $this->sendPush($notice, $recipient);
            }

            if ($notice->delivery_whatsapp && $channelsConfig->canSend(NotificationChannelService::EVENT_NOTICE_BROADCAST, 'whatsapp')) {
                $this->sendWhatsapp($notice, $recipient);
            }
        }

        return $recipients->count();
    }

    public function visibleNoticesFor(User $user, ?int $activeTenantId = null): Builder
    {
        $query = Notice::query()->currentlyActive();

        if ($user->role === 'super_admin') {
            return $query->orderByDesc('created_at');
        }

        if ($user->role === 'library_owner') {
            return $query
                ->whereIn('audience', ['libraries', 'both'])
                ->where(function (Builder $q) use ($user): void {
                    $q->whereNull('tenant_id')->orWhere('tenant_id', $user->tenant_id);
                })
                ->orderByDesc('created_at');
        }

        if ($user->role === 'student') {
            $tenantIds = $user->memberships()->pluck('tenant_id')->all();
            if ($activeTenantId && in_array($activeTenantId, $tenantIds, true)) {
                $tenantIds = [$activeTenantId];
            }

            return $query
                ->whereIn('audience', ['students', 'both'])
                ->where(function (Builder $q) use ($tenantIds): void {
                    $q->whereNull('tenant_id');
                    if (! empty($tenantIds)) {
                        $q->orWhereIn('tenant_id', $tenantIds);
                    }
                })
                ->orderByDesc('created_at');
        }

        return $query->whereRaw('1=0');
    }

    private function resolveRecipients(Notice $notice): Collection
    {
        $audiences = match ($notice->audience) {
            'libraries' => ['library_owner'],
            'both' => ['library_owner', 'student'],
            default => ['student'],
        };

        $query = User::query()
            ->whereIn('role', $audiences);

        if ($notice->tenant_id) {
            $query->where(function (Builder $q) use ($notice): void {
                $q->where(function (Builder $inner) use ($notice): void {
                    $inner->where('role', 'library_owner')
                        ->where('tenant_id', $notice->tenant_id);
                })->orWhere(function (Builder $inner) use ($notice): void {
                    $inner->where('role', 'student')
                        ->whereHas('memberships', fn (Builder $mq) => $mq->where('tenant_id', $notice->tenant_id));
                });
            });
        }

        return $query->get();
    }

    private function sendPush(Notice $notice, User $recipient): void
    {
        if (empty($recipient->fcm_token)) {
            return;
        }

        try {
            app(FirebaseNotificationService::class)->sendToDevice(
                $recipient->fcm_token,
                $notice->title,
                $notice->body,
                [
                    'type' => 'notice',
                    'notice_id' => (string) $notice->id,
                    'level' => $notice->level,
                ]
            );
        } catch (\Throwable) {}
    }

    private function sendWhatsapp(Notice $notice, User $recipient): void
    {
        if (empty($recipient->phone)) {
            return;
        }

        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_NOTICE_BROADCAST,
            'whatsapp',
            [
                'app_name' => \App\Models\Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => \App\Models\Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $recipient->name,
                'tenant_name' => $recipient->tenant?->name ?? 'Library',
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'notice_title' => $notice->title,
                'notice_body' => $notice->body,
                'level' => $notice->level,
            ]
        );
        if (! ($rendered['is_active'] ?? true)) {
            return;
        }

        $message = $rendered['body'] ?: ($notice->title.': '.$notice->body);
        app(WhatsappNotificationService::class)->sendMessage($recipient->phone, $message);
    }
}
