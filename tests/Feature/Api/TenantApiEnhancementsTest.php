<?php

namespace Tests\Feature\Api;

use App\Livewire\Library\Seats as SeatsComponent;
use App\Models\AuditLog;
use App\Models\FeePayment;
use App\Models\Seat;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

class TenantApiEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_index_supports_pagination_and_sorting(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);
        $otherTenant = Tenant::create(['name' => 'Tenant B', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        User::factory()->create(['name' => 'Charlie', 'role' => 'student', 'tenant_id' => $tenant->id]);
        User::factory()->create(['name' => 'Alice', 'role' => 'student', 'tenant_id' => $tenant->id]);
        User::factory()->create(['name' => 'Bob', 'role' => 'student', 'tenant_id' => $tenant->id]);
        User::factory()->create(['name' => 'Zed', 'role' => 'student', 'tenant_id' => $otherTenant->id]);

        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/tenant/students?per_page=2&sort_by=name&sort_dir=asc');

        $response->assertOk();
        $response->assertJsonPath('per_page', 2);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.name', 'Alice');
        $response->assertJsonPath('data.1.name', 'Bob');
    }

    public function test_seats_index_supports_pagination_and_sorting(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        Seat::create(['tenant_id' => $tenant->id, 'name' => 'A-01', 'status' => 'available']);
        Seat::create(['tenant_id' => $tenant->id, 'name' => 'C-01', 'status' => 'available']);
        Seat::create(['tenant_id' => $tenant->id, 'name' => 'B-01', 'status' => 'available']);

        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/tenant/seats?per_page=2&sort_by=name&sort_dir=desc');

        $response->assertOk();
        $response->assertJsonPath('per_page', 2);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.name', 'C-01');
        $response->assertJsonPath('data.1.name', 'B-01');
    }

    public function test_fees_index_supports_pagination_and_sorting(): void
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

        FeePayment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'amount' => 500,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);
        FeePayment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'amount' => 1200,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);
        FeePayment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'amount' => 800,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/tenant/fees?per_page=2&sort_by=amount&sort_dir=asc');

        $response->assertOk();
        $response->assertJsonPath('per_page', 2);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.amount', 500);
        $response->assertJsonPath('data.1.amount', 800);
    }

    public function test_api_critical_updates_create_audit_logs(): void
    {
        $tenant = Tenant::create(['name' => 'Tenant A', 'status' => 'active']);

        $owner = User::factory()->create([
            'role' => 'library_owner',
            'tenant_id' => $tenant->id,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenant->id,
            'name' => 'Old Name',
        ]);

        $payment = FeePayment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'amount' => 900,
            'payment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($owner);

        $this->putJson("/api/tenant/students/{$student->id}", ['name' => 'New Name'])->assertOk();
        $this->putJson("/api/tenant/fees/{$payment->id}", ['status' => 'paid'])->assertOk();
        $this->deleteJson("/api/tenant/students/{$student->id}")->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'student.updated',
            'entity_type' => User::class,
            'entity_id' => $student->id,
            'actor_user_id' => $owner->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'fee_payment.updated',
            'entity_type' => FeePayment::class,
            'entity_id' => $payment->id,
            'actor_user_id' => $owner->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'student.deleted',
            'entity_type' => User::class,
            'entity_id' => $student->id,
            'actor_user_id' => $owner->id,
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_livewire_seat_unassign_creates_audit_log(): void
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

        $seat = Seat::create([
            'tenant_id' => $tenant->id,
            'name' => 'S-01',
            'status' => 'occupied',
            'user_id' => $student->id,
        ]);

        $this->actingAs($owner);

        Livewire::test(SeatsComponent::class)
            ->call('unassign', $seat->id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'seat.unassigned',
            'entity_type' => Seat::class,
            'entity_id' => $seat->id,
            'actor_user_id' => $owner->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->assertSame(1, AuditLog::query()->where('action', 'seat.unassigned')->count());
    }
}
