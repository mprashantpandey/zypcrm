<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Students as AdminStudentsComponent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminStudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_students_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.students'))
            ->assertOk()
            ->assertSee('Platform Students');
    }

    public function test_super_admin_can_attach_and_detach_student_memberships(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $tenantA = Tenant::create([
            'name' => 'Alpha Library',
            'status' => 'active',
        ]);
        $tenantB = Tenant::create([
            'name' => 'Beta Library',
            'status' => 'active',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenantA->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(AdminStudentsComponent::class)
            ->call('openMembershipManager', $student->id)
            ->set('membershipTenantIds', [(string) $tenantA->id, (string) $tenantB->id])
            ->call('syncMemberships')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('student_memberships', [
            'user_id' => $student->id,
            'tenant_id' => $tenantA->id,
        ]);
        $this->assertDatabaseHas('student_memberships', [
            'user_id' => $student->id,
            'tenant_id' => $tenantB->id,
        ]);

        Livewire::test(AdminStudentsComponent::class)
            ->call('openMembershipManager', $student->id)
            ->set('membershipTenantIds', [(string) $tenantB->id])
            ->call('syncMemberships')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('student_memberships', [
            'user_id' => $student->id,
            'tenant_id' => $tenantA->id,
        ]);
        $this->assertDatabaseHas('student_memberships', [
            'user_id' => $student->id,
            'tenant_id' => $tenantB->id,
        ]);

        $this->assertEquals($tenantB->id, (int) $student->fresh()->tenant_id);
    }
}
