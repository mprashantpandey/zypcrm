<?php

namespace Tests\Feature\Api;

use App\Models\FeePayment;
use App\Models\Seat;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_assign_seat_to_student_from_other_tenant(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);
        $tenantB = Tenant::create(['name' => 'Tenant B', 'status' => 'active']);

        $ownerA = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenantA->id,
        ]);

        $studentB = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenantB->id,
        ]);

        Sanctum::actingAs($ownerA);

        $response = $this->postJson('/api/tenant/seats', [
            'name' => 'S-01',
            'status' => 'available',
            'user_id' => $studentB->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_tenant_cannot_create_fee_payment_for_student_from_other_tenant(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);
        $tenantB = Tenant::create(['name' => 'Tenant B', 'status' => 'active']);

        $ownerA = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenantA->id,
        ]);

        $studentB = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenantB->id,
        ]);

        Sanctum::actingAs($ownerA);

        $response = $this->postJson('/api/tenant/fees', [
            'user_id' => $studentB->id,
            'amount' => 500,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_student_password_update_requires_minimum_length(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenant->id,
        ]);

        Sanctum::actingAs($owner);

        $response = $this->putJson("/api/tenant/students/{$student->id}", [
            'password' => 'short',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_fee_and_seat_endpoints_include_student_relation(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenant->id,
            'name' => 'Student One',
        ]);

        Seat::create([
            'tenant_id' => $tenant->id,
            'name' => 'S-01',
            'status' => 'occupied',
            'user_id' => $student->id,
        ]);

        FeePayment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'amount' => 1000,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        Sanctum::actingAs($owner);

        $seatResponse = $this->getJson('/api/tenant/seats');
        $seatResponse
            ->assertOk()
            ->assertJsonPath('data.0.student.id', $student->id)
            ->assertJsonPath('data.0.student.name', 'Student One');

        $feeResponse = $this->getJson('/api/tenant/fees');
        $feeResponse
            ->assertOk()
            ->assertJsonPath('data.0.student.id', $student->id)
            ->assertJsonPath('data.0.student.name', 'Student One');
    }
}
