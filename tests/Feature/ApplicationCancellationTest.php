<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function student_can_cancel_draft_application()
    {
        // Create a student user with profile
        $user = User::factory()->create(['role' => 'student']);
        $studentProfile = StudentProfile::factory()->create(['user_id' => $user->id]);
        
        // Create a scholarship program
        $program = ScholarshipProgram::factory()->create();
        
        // Create a draft application
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $studentProfile->id,
            'scholarship_program_id' => $program->id,
            'status' => 'draft'
        ]);

        // Act as the student and cancel the application
        $response = $this->actingAs($user)
            ->post(route('student.applications.cancel', $application));

        // Assert the application was cancelled
        $response->assertRedirect(route('student.applications.index'));
        $response->assertSessionHas('success', 'Your scholarship application has been cancelled successfully.');
        
        $application->refresh();
        $this->assertEquals('cancelled', $application->status);
        $this->assertNotNull($application->reviewed_at);
    }

    /** @test */
    public function student_can_cancel_submitted_application()
    {
        $user = User::factory()->create(['role' => 'student']);
        $studentProfile = StudentProfile::factory()->create(['user_id' => $user->id]);
        $program = ScholarshipProgram::factory()->create();
        
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $studentProfile->id,
            'scholarship_program_id' => $program->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($user)
            ->post(route('student.applications.cancel', $application));

        $response->assertRedirect(route('student.applications.index'));
        $application->refresh();
        $this->assertEquals('cancelled', $application->status);
    }

    /** @test */
    public function student_cannot_cancel_approved_application()
    {
        $user = User::factory()->create(['role' => 'student']);
        $studentProfile = StudentProfile::factory()->create(['user_id' => $user->id]);
        $program = ScholarshipProgram::factory()->create();
        
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $studentProfile->id,
            'scholarship_program_id' => $program->id,
            'status' => 'documents_approved'
        ]);

        $response = $this->actingAs($user)
            ->post(route('student.applications.cancel', $application));

        $response->assertRedirect(route('student.applications.show', $application));
        $response->assertSessionHas('error', 'This application cannot be cancelled at its current stage.');
        
        $application->refresh();
        $this->assertEquals('documents_approved', $application->status);
    }

    /** @test */
    public function student_cannot_cancel_another_students_application()
    {
        // Create two students
        $user1 = User::factory()->create(['role' => 'student']);
        $user2 = User::factory()->create(['role' => 'student']);
        $studentProfile1 = StudentProfile::factory()->create(['user_id' => $user1->id]);
        $studentProfile2 = StudentProfile::factory()->create(['user_id' => $user2->id]);
        
        $program = ScholarshipProgram::factory()->create();
        
        // Create application for student 1
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $studentProfile1->id,
            'scholarship_program_id' => $program->id,
            'status' => 'draft'
        ]);

        // Try to cancel as student 2
        $response = $this->actingAs($user2)
            ->post(route('student.applications.cancel', $application));

        $response->assertStatus(403);
        
        $application->refresh();
        $this->assertEquals('draft', $application->status);
    }

    /** @test */
    public function guest_cannot_cancel_application()
    {
        $user = User::factory()->create(['role' => 'student']);
        $studentProfile = StudentProfile::factory()->create(['user_id' => $user->id]);
        $program = ScholarshipProgram::factory()->create();
        
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $studentProfile->id,
            'scholarship_program_id' => $program->id,
            'status' => 'draft'
        ]);

        $response = $this->post(route('student.applications.cancel', $application));

        $response->assertRedirect(route('login'));
        
        $application->refresh();
        $this->assertEquals('draft', $application->status);
    }

    /** @test */
    public function admin_cannot_access_student_cancellation_route()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $studentProfile = StudentProfile::factory()->create(['user_id' => $student->id]);
        $program = ScholarshipProgram::factory()->create();
        
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $studentProfile->id,
            'scholarship_program_id' => $program->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('student.applications.cancel', $application));

        // Should be forbidden since admin doesn't have student profile
        $response->assertStatus(403);
        
        $application->refresh();
        $this->assertEquals('draft', $application->status);
    }
}
