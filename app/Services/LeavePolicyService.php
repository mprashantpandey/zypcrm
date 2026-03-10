<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\StudentLeave;
use App\Models\StudentSubscription;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LeavePolicyService
{
    public function getPolicyConfig(): array
    {
        $mode = Setting::getValue('leave_policy_mode', 'none') ?: 'none';
        $cap = (int) (Setting::getValue('leave_policy_cap_days_per_month', '0') ?? 0);

        return [
            'mode' => in_array($mode, ['none', 'full', 'capped'], true) ? $mode : 'none',
            'cap_days_per_month' => max(0, min(31, $cap)),
        ];
    }

    public function applyApproval(StudentLeave $leave): array
    {
        $policy = $this->getPolicyConfig();
        $extensionDays = $this->calculateExtensionDays($leave, $policy['mode'], $policy['cap_days_per_month']);

        $subscription = StudentSubscription::withoutGlobalScopes()
            ->where('tenant_id', $leave->tenant_id)
            ->where('user_id', $leave->user_id)
            ->where('status', 'active')
            ->orderByDesc('end_date')
            ->first();

        $oldEndDate = $subscription?->end_date ? Carbon::parse($subscription->end_date)->toDateString() : null;
        $newEndDate = $oldEndDate;

        if ($subscription && $extensionDays > 0) {
            $updatedEndDate = Carbon::parse($subscription->end_date)->addDays($extensionDays)->toDateString();
            $subscription->update(['end_date' => $updatedEndDate]);
            $newEndDate = $updatedEndDate;
        }

        return [
            'policy_mode' => $policy['mode'],
            'cap_days_per_month' => $policy['cap_days_per_month'],
            'requested_days' => $this->inclusiveDays($leave->start_date, $leave->end_date),
            'extended_days' => $extensionDays,
            'subscription_id' => $subscription?->id,
            'old_end_date' => $oldEndDate,
            'new_end_date' => $newEndDate,
        ];
    }

    private function calculateExtensionDays(StudentLeave $leave, string $mode, int $capDaysPerMonth): int
    {
        if ($mode === 'none') {
            return 0;
        }

        if ($mode === 'full') {
            return $this->inclusiveDays($leave->start_date, $leave->end_date);
        }

        if ($mode !== 'capped' || $capDaysPerMonth <= 0) {
            return 0;
        }

        $start = Carbon::parse($leave->start_date)->startOfDay();
        $end = Carbon::parse($leave->end_date)->startOfDay();

        $daysByMonth = [];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $monthKey = $date->format('Y-m');
            $daysByMonth[$monthKey] = ($daysByMonth[$monthKey] ?? 0) + 1;
        }

        $approvedDays = 0;
        foreach ($daysByMonth as $monthKey => $requestedInMonth) {
            [$year, $month] = explode('-', $monthKey);
            $monthStart = Carbon::create((int) $year, (int) $month, 1)->startOfDay();
            $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();

            $alreadyApproved = $this->approvedDaysInMonth(
                tenantId: $leave->tenant_id,
                userId: $leave->user_id,
                monthStart: $monthStart,
                monthEnd: $monthEnd,
                excludeLeaveId: $leave->id
            );

            $available = max($capDaysPerMonth - $alreadyApproved, 0);
            $approvedDays += min($requestedInMonth, $available);
        }

        return $approvedDays;
    }

    private function approvedDaysInMonth(int $tenantId, int $userId, Carbon $monthStart, Carbon $monthEnd, ?int $excludeLeaveId = null): int
    {
        $query = StudentLeave::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $monthEnd->toDateString())
            ->whereDate('end_date', '>=', $monthStart->toDateString());

        if ($excludeLeaveId) {
            $query->where('id', '!=', $excludeLeaveId);
        }

        $count = 0;
        foreach ($query->get(['start_date', 'end_date']) as $leave) {
            $overlapStart = Carbon::parse($leave->start_date)->greaterThan($monthStart)
                ? Carbon::parse($leave->start_date)
                : $monthStart->copy();
            $overlapEnd = Carbon::parse($leave->end_date)->lessThan($monthEnd)
                ? Carbon::parse($leave->end_date)
                : $monthEnd->copy();

            if ($overlapStart->lte($overlapEnd)) {
                $count += $overlapStart->diffInDays($overlapEnd) + 1;
            }
        }

        return $count;
    }

    private function inclusiveDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        return $start->diffInDays($end) + 1;
    }
}

