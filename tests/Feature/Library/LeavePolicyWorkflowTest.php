<?php

namespace Tests\Feature\Library;

use App\Livewire\Library\Leaves;
use App\Models\LibraryPlan;
use App\Models\Setting;
use App\Models\StudentLeave;
use App\Models\StudentSubscription;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\StudentLeaveStatusNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class LeavePolicyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_leave_approval_with_full_policy_extends_subscription_and_logs_audit(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        [$owner, $student, $leave, $subscription] = $this->seedBaseLeaveData();
        Setting::create(['key' => 'leave_policy_mode', 'value' => 'full', 'group' => 'policy']);
        Setting::create(['key' => 'leave_policy_cap_days_per_month', 'value' => '0', 'group' => 'policy']);
        Setting::create(['key' => 'firebase_enabled', 'value' => 'false', 'group' => 'firebase']);

        Notification::fake();

        Livewire::actingAs($owner)
            ->test(Leaves::class)
            ->call('updateStatus', $leave->id, 'approved');

        $leave->refresh();
        $subscription->refresh();

        $this->assertSame('approved', $leave->status);
        $this->assertSame('2026-03-14', $subscription->end_date->toDateString());

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $owner->tenant_id,
            'actor_user_id' => $owner->id,
            'action' => 'leave.approved',
            'entity_type' => StudentLeave::class,
            'entity_id' => $leave->id,
        ]);

        Notification::assertSentTo(
            $student,
            StudentLeaveStatusNotification::class,
            fn (StudentLeaveStatusNotification $notification) => $notification->status === 'approved'
                && $notification->extendedDays === 3
                && $notification->updatedSubscriptionEndDate === '2026-03-14'
        );
    }

    public function test_capped_policy_respects_monthly_limit(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        [$owner, $student, $leave, $subscription] = $this->seedBaseLeaveData();
        Setting::create(['key' => 'leave_policy_mode', 'value' => 'capped', 'group' => 'policy']);
        Setting::create(['key' => 'leave_policy_cap_days_per_month', 'value' => '3', 'group' => 'policy']);
        Setting::create(['key' => 'firebase_enabled', 'value' => 'false', 'group' => 'firebase']);

        StudentLeave::create([
            'tenant_id' => $owner->tenant_id,
            'user_id' => $student->id,
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-02',
            'reason' => 'Previous approved leave',
            'status' => 'approved',
        ]);

        Notification::fake();

        Livewire::actingAs($owner)
            ->test(Leaves::class)
            ->call('updateStatus', $leave->id, 'approved');

        $leave->refresh();
        $subscription->refresh();

        $this->assertSame('approved', $leave->status);
        $this->assertSame('2026-03-12', $subscription->end_date->toDateString());

        Notification::assertSentTo(
            $student,
            StudentLeaveStatusNotification::class,
            fn (StudentLeaveStatusNotification $notification) => $notification->extendedDays === 1
        );
    }

    public function test_reject_leave_logs_audit_and_does_not_extend_subscription(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        [$owner, $student, $leave, $subscription] = $this->seedBaseLeaveData();
        Setting::create(['key' => 'leave_policy_mode', 'value' => 'full', 'group' => 'policy']);
        Setting::create(['key' => 'firebase_enabled', 'value' => 'false', 'group' => 'firebase']);

        Notification::fake();

        Livewire::actingAs($owner)
            ->test(Leaves::class)
            ->call('updateStatus', $leave->id, 'rejected');

        $leave->refresh();
        $subscription->refresh();

        $this->assertSame('rejected', $leave->status);
        $this->assertSame('2026-03-11', $subscription->end_date->toDateString());

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $owner->tenant_id,
            'actor_user_id' => $owner->id,
            'action' => 'leave.rejected',
            'entity_type' => StudentLeave::class,
            'entity_id' => $leave->id,
        ]);

        Notification::assertSentTo(
            $student,
            StudentLeaveStatusNotification::class,
            fn (StudentLeaveStatusNotification $notification) => $notification->status === 'rejected'
                && $notification->extendedDays === 0
        );
    }

    private function seedBaseLeaveData(): array
    {
        $tenant = Tenant::create(['name' => 'Policy Tenant', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenant->id,
        ]);

        $plan = LibraryPlan::create([
            'tenant_id' => $tenant->id,
            'name' => 'Monthly',
            'price' => 1000,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $subscription = StudentSubscription::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'library_plan_id' => $plan->id,
            'seat_id' => null,
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-11',
            'status' => 'active',
        ]);

        $leave = StudentLeave::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'start_date' => '2026-03-03',
            'end_date' => '2026-03-05',
            'reason' => 'Exam preparation',
            'status' => 'pending',
        ]);

        return [$owner, $student, $leave, $subscription];
    }
}
