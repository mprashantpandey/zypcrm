<?php

namespace Tests\Feature\Student;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_access_student_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($student);

        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Welcome, '.$student->name);
    }

    public function test_non_student_cannot_access_student_dashboard(): void
    {
        $owner = User::factory()->create([
            'role' => 'library_owner',
        ]);

        $this->actingAs($owner);

        $this->get(route('student.dashboard'))
            ->assertForbidden();
    }
}
