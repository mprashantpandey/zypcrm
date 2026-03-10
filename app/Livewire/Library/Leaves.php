<?php

namespace App\Livewire\Library;

use App\Models\Setting;
use App\Models\StudentLeave;
use App\Notifications\StudentLeaveStatusNotification;
use App\Services\AuditLogService;
use App\Services\FirebaseNotificationService;
use App\Services\LeavePolicyService;
use App\Services\NotificationChannelService;
use App\Services\NotificationTemplateService;
use App\Services\WhatsappNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Leaves extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = 'all'; // all, pending, approved, rejected

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updateStatus($leaveId, $newStatus)
    {
        if (! in_array($newStatus, ['approved', 'rejected'], true)) {
            return;
        }

        $tenantId = Auth::user()->tenant_id;

        $leave = StudentLeave::query()->with('user')
            ->where([['tenant_id', '=', $tenantId]])
            ->where([['id', '=', $leaveId]])
            ->first();

        if (! $leave || $leave->status !== 'pending') {
            return;
        }

        $before = $leave->only(['id', 'tenant_id', 'user_id', 'start_date', 'end_date', 'reason', 'status']);
        $extensionResult = [
            'policy_mode' => Setting::getValue('leave_policy_mode', 'none') ?? 'none',
            'cap_days_per_month' => (int) (Setting::getValue('leave_policy_cap_days_per_month', '0') ?? 0),
            'requested_days' => 0,
            'extended_days' => 0,
            'subscription_id' => null,
            'old_end_date' => null,
            'new_end_date' => null,
        ];

        if ($newStatus === 'approved') {
            $extensionResult = app(LeavePolicyService::class)->applyApproval($leave);
        }

        $leave->update(['status' => $newStatus]);
        $leave->refresh();

        if ($leave->user) {
            $leave->user->notify(new StudentLeaveStatusNotification(
                leave: $leave,
                status: $newStatus,
                extendedDays: (int) ($extensionResult['extended_days'] ?? 0),
                updatedSubscriptionEndDate: $extensionResult['new_end_date'] ?? null
            ));

            $this->sendPushLeaveStatus($leave, $newStatus, $extensionResult);
            $this->sendWhatsappLeaveStatus($leave, $newStatus, $extensionResult);
        }

        app(AuditLogService::class)->log(
            action: "leave.{$newStatus}",
            entityType: StudentLeave::class,
            entityId: $leave->id,
            oldValues: $before,
            newValues: $leave->only(['id', 'tenant_id', 'user_id', 'start_date', 'end_date', 'reason', 'status']),
            metadata: [
                'policy_mode' => $extensionResult['policy_mode'] ?? null,
                'cap_days_per_month' => $extensionResult['cap_days_per_month'] ?? null,
                'requested_days' => $extensionResult['requested_days'] ?? null,
                'extended_days' => $extensionResult['extended_days'] ?? null,
                'subscription_id' => $extensionResult['subscription_id'] ?? null,
                'old_end_date' => $extensionResult['old_end_date'] ?? null,
                'new_end_date' => $extensionResult['new_end_date'] ?? null,
            ],
            actor: Auth::user(),
            tenantId: $tenantId,
            request: request()
        );

        $message = 'Leave status updated to '.ucfirst($newStatus).'.';
        if ($newStatus === 'approved') {
            $message .= ' Subscription extended by '.((int) ($extensionResult['extended_days'] ?? 0)).' day(s).';
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
    }

    private function sendPushLeaveStatus(StudentLeave $leave, string $status, array $extensionResult): void
    {
        if (! app(NotificationChannelService::class)->canSend(NotificationChannelService::EVENT_LEAVE_STATUS, 'push')) {
            return;
        }

        $student = $leave->user;
        if (! $student || empty($student->fcm_token)) {
            return;
        }

        $title = $status === 'approved' ? 'Leave Approved' : 'Leave Rejected';
        $body = "Your leave request (".(string) $leave->start_date.' to '.(string) $leave->end_date.") was {$status}.";

        if ($status === 'approved') {
            $days = (int) ($extensionResult['extended_days'] ?? 0);
            $body .= " Subscription extended by {$days} day(s).";
        }

        try {
            app(FirebaseNotificationService::class)->sendToDevice(
                $student->fcm_token,
                $title,
                $body,
                [
                    'type' => 'leave_status',
                    'leave_id' => (string) $leave->id,
                    'status' => $status,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Leave status push notification failed: '.$e->getMessage(), [
                'leave_id' => $leave->id,
                'user_id' => $student->id,
            ]);
        }
    }

    private function sendWhatsappLeaveStatus(StudentLeave $leave, string $status, array $extensionResult): void
    {
        if (! app(NotificationChannelService::class)->canSend(NotificationChannelService::EVENT_LEAVE_STATUS, 'whatsapp')) {
            return;
        }

        $student = $leave->user;
        if (! $student || empty($student->phone)) {
            return;
        }

        $rendered = app(NotificationTemplateService::class)->render(
            NotificationChannelService::EVENT_LEAVE_STATUS,
            'whatsapp',
            [
                'app_name' => Setting::getValue('app_name', 'ZypCRM'),
                'site_title' => Setting::getValue('site_title', 'ZypCRM'),
                'user_name' => $student->name,
                'tenant_name' => Auth::user()?->tenant?->name ?? 'Library',
                'dashboard_url' => url('/dashboard'),
                'date' => now()->format('M d, Y'),
                'status' => $status,
                'start_date' => (string) $leave->start_date,
                'end_date' => (string) $leave->end_date,
                'extended_days' => (string) ((int) ($extensionResult['extended_days'] ?? 0)),
                'updated_end_date' => (string) ($extensionResult['new_end_date'] ?? '-'),
            ]
        );
        if (! ($rendered['is_active'] ?? true)) {
            return;
        }

        $message = $rendered['body'] ?: "Leave request {$status} for {$leave->start_date} to {$leave->end_date}.";

        try {
            app(WhatsappNotificationService::class)->sendMessage($student->phone, $message);
        } catch (\Throwable $e) {
            Log::warning('Leave status WhatsApp notification failed: '.$e->getMessage(), [
                'leave_id' => $leave->id,
                'user_id' => $student->id,
            ]);
        }
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $leavesQuery = StudentLeave::query()->with('user')
            ->where([['tenant_id', '=', $tenantId]]);

        if ($this->filterStatus !== 'all') {
            $leavesQuery->where([['status', '=', $this->filterStatus]]);
        }

        if (! empty($this->search)) {
            $leavesQuery->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        $leaves = $leavesQuery->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.library.leaves', [
            'leaves' => $leaves,
        ])->layout('layouts.app', [
            'header' => 'Leave Management',
        ]);
    }
}
