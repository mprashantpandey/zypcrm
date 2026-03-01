<?php

namespace Tests\Feature\Student;

use App\Livewire\Student\Leaves;
use App\Models\StudentLeave;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentLeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_leave_request(): void
    {
        $tenant = Tenant::create([
            'name' => 'Demo Library',
            'status' => 'active',
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($student);

        Livewire::test(Leaves::class)
            ->set('startDate', now()->toDateString())
            ->set('endDate', now()->addDay()->toDateString())
            ->set('reason', 'Medical appointment and recovery rest.')
            ->call('submitLeaveRequest')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('student_leaves', [
            'tenant_id' => $tenant->id,
            'user_id' => $student->id,
            'status' => 'pending',
        ]);
    }

    public function test_library_owner_cannot_access_student_leave_page(): void
    {
        $owner = User::factory()->create([
            'role' => 'library_owner',
        ]);

        $this->actingAs($owner)
            ->get(route('student.leaves'))
            ->assertForbidden();
    }
}
