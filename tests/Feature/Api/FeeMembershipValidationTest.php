<?php

namespace Tests\Feature\Api;

use App\Models\StudentMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeeMembershipValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_create_fee_for_student_with_membership_even_if_legacy_tenant_id_differs(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);
        $tenantB = Tenant::create(['name' => 'Tenant B', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenantA->id,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenantB->id,
        ]);

        StudentMembership::create([
            'user_id' => $student->id,
            'tenant_id' => $tenantA->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        Sanctum::actingAs($owner);

        $response = $this->postJson('/api/tenant/fees', [
            'user_id' => $student->id,
            'amount' => 1200,
            'payment_date' => now()->toDateString(),
            'status' => 'pending',
            'payment_method' => 'cash',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('fee_payments', [
            'tenant_id' => $tenantA->id,
            'user_id' => $student->id,
            'amount' => 1200,
        ]);
    }
}
