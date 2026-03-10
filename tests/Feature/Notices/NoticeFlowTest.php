<?php

namespace Tests\Feature\Notices;

use App\Models\Notice;
use App\Models\StudentMembership;
use App\Models\Tenant;
use App\Models\User;
use App\Services\NoticeDispatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_notice_dispatch_targets_expected_users_and_visibility_respects_tenant_context(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);
        $tenantB = Tenant::create(['name' => 'Tenant B', 'status' => 'active']);

        $admin = User::factory()->create(['role' => 'super_admin']);
        $ownerA = User::factory()->create(['role' => 'library_owner', 'tenant_id' => $tenantA->id]);
        $ownerB = User::factory()->create(['role' => 'library_owner', 'tenant_id' => $tenantB->id]);
        $studentA = User::factory()->create(['role' => 'student', 'tenant_id' => $tenantA->id]);
        $studentB = User::factory()->create(['role' => 'student', 'tenant_id' => $tenantB->id]);
        $studentDual = User::factory()->create(['role' => 'student', 'tenant_id' => $tenantA->id]);

        StudentMembership::updateOrCreate(['user_id' => $studentA->id, 'tenant_id' => $tenantA->id], ['status' => 'active']);
        StudentMembership::updateOrCreate(['user_id' => $studentB->id, 'tenant_id' => $tenantB->id], ['status' => 'active']);
        StudentMembership::updateOrCreate(['user_id' => $studentDual->id, 'tenant_id' => $tenantA->id], ['status' => 'active']);
        StudentMembership::updateOrCreate(['user_id' => $studentDual->id, 'tenant_id' => $tenantB->id], ['status' => 'active']);

        $globalNotice = Notice::create([
            'tenant_id' => null,
            'created_by' => $admin->id,
            'title' => 'Global',
            'body' => 'Global broadcast',
            'level' => 'info',
            'audience' => 'both',
            'delivery_in_app' => true,
            'delivery_email' => false,
            'delivery_push' => false,
            'is_active' => true,
        ]);

        $tenantNotice = Notice::create([
            'tenant_id' => $tenantA->id,
            'created_by' => $ownerA->id,
            'title' => 'Tenant A only',
            'body' => 'For Tenant A students',
            'level' => 'warning',
            'audience' => 'students',
            'delivery_in_app' => true,
            'delivery_email' => false,
            'delivery_push' => false,
            'is_active' => true,
        ]);

        $service = app(NoticeDispatchService::class);
        $service->dispatch($globalNotice);
        $service->dispatch($tenantNotice);

        $this->assertDatabaseCount('notifications', 7);
        $this->assertSame(2, $studentA->notifications()->count());
        $this->assertSame(1, $studentB->notifications()->count());
        $this->assertSame(2, $studentDual->notifications()->count());

        $studentBVisible = $service->visibleNoticesFor($studentB, $tenantB->id)->pluck('id')->all();
        $this->assertContains($globalNotice->id, $studentBVisible);
        $this->assertNotContains($tenantNotice->id, $studentBVisible);

        $dualTenantAVisible = $service->visibleNoticesFor($studentDual, $tenantA->id)->pluck('id')->all();
        $this->assertContains($globalNotice->id, $dualTenantAVisible);
        $this->assertContains($tenantNotice->id, $dualTenantAVisible);

        $dualTenantBVisible = $service->visibleNoticesFor($studentDual, $tenantB->id)->pluck('id')->all();
        $this->assertContains($globalNotice->id, $dualTenantBVisible);
        $this->assertNotContains($tenantNotice->id, $dualTenantBVisible);

        $ownerAVisible = $service->visibleNoticesFor($ownerA)->pluck('id')->all();
        $this->assertContains($globalNotice->id, $ownerAVisible);
    }
}
