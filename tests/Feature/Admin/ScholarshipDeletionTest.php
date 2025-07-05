<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\ScholarshipProgram;
use App\Models\ScholarshipApplication;
use App\Models\DocumentUpload;
use App\Models\DocumentRequirement;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\CommunityServiceReport;
use App\Models\CommunityServiceEntry;
use App\Models\Disbursement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScholarshipDeletionTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $studentUser;
    private StudentProfile $studentProfile;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        // Create a student user and profile
        $this->studentUser = User::factory()->create([
            'email' => 'student@test.com',
            'role' => 'student',
        ]);

        $this->studentProfile = StudentProfile::factory()->create([
            'user_id' => $this->studentUser->id,
        ]);

        // Set up fake storage
        Storage::fake('public');
    }

    public function test_can_delete_scholarship_with_all_related_data(): void
    {
        $this->actingAs($this->adminUser);

        // Create a scholarship program
        $scholarship = ScholarshipProgram::factory()->create([
            'name' => 'Test Scholarship for Deletion',
            'total_budget' => 10000.00,
            'per_student_budget' => 1000.00,
        ]);

        // Create document requirements
        $documentRequirement = DocumentRequirement::factory()->create([
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Test Document',
        ]);

        // Create scholarship application
        $application = ScholarshipApplication::factory()->create([
            'student_profile_id' => $this->studentProfile->id,
            'scholarship_program_id' => $scholarship->id,
            'status' => 'submitted',
        ]);

        // Create document upload with fake file
        $fakeFile = UploadedFile::fake()->create('test-document.pdf', 100);
        $filePath = $fakeFile->store('documents', 'public');

        $documentUpload = DocumentUpload::factory()->create([
            'scholarship_application_id' => $application->id,
            'document_requirement_id' => $documentRequirement->id,
            'file_path' => $filePath,
            'original_filename' => 'test-document.pdf',
        ]);

        // Create community service report with PDF
        $reportFile = UploadedFile::fake()->create('service-report.pdf', 50);
        $reportPath = $reportFile->store('reports', 'public');

        $serviceReport = CommunityServiceReport::factory()->create([
            'scholarship_application_id' => $application->id,
            'pdf_report_path' => $reportPath,
        ]);

        // Create community service entry with photos (use regular file instead of image)
        $photoFile = UploadedFile::fake()->create('photo.jpg', 50);
        $photoPath = $photoFile->store('photos', 'public');

        $serviceEntry = CommunityServiceEntry::factory()->create([
            'scholarship_application_id' => $application->id,
            'photos' => json_encode([$photoPath]),
        ]);

        // Create disbursement
        $disbursement = Disbursement::factory()->create([
            'scholarship_application_id' => $application->id,
            'amount' => 1000.00,
        ]);

        // Verify files exist in storage
        $this->assertTrue(Storage::disk('public')->exists($filePath));
        $this->assertTrue(Storage::disk('public')->exists($reportPath));
        $this->assertTrue(Storage::disk('public')->exists($photoPath));

        // Verify all records exist in database
        $this->assertDatabaseHas('scholarship_programs', ['id' => $scholarship->id]);
        $this->assertDatabaseHas('document_requirements', ['id' => $documentRequirement->id]);
        $this->assertDatabaseHas('scholarship_applications', ['id' => $application->id]);
        $this->assertDatabaseHas('document_uploads', ['id' => $documentUpload->id]);
        $this->assertDatabaseHas('community_service_reports', ['id' => $serviceReport->id]);
        $this->assertDatabaseHas('community_service_entries', ['id' => $serviceEntry->id]);
        $this->assertDatabaseHas('disbursements', ['id' => $disbursement->id]);

        // Delete the scholarship
        $response = $this->delete(route('admin.scholarships.destroy', $scholarship->id));

        // Assert successful deletion and redirect
        $response->assertRedirect(route('admin.scholarships.index'));
        $response->assertSessionHas('success', 'Scholarship program and all related data deleted successfully.');

        // Verify all records are deleted from database
        $this->assertDatabaseMissing('scholarship_programs', ['id' => $scholarship->id]);
        $this->assertDatabaseMissing('document_requirements', ['id' => $documentRequirement->id]);
        $this->assertDatabaseMissing('scholarship_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('document_uploads', ['id' => $documentUpload->id]);
        $this->assertDatabaseMissing('community_service_reports', ['id' => $serviceReport->id]);
        $this->assertDatabaseMissing('community_service_entries', ['id' => $serviceEntry->id]);
        $this->assertDatabaseMissing('disbursements', ['id' => $disbursement->id]);

        // Verify files are deleted from storage
        $this->assertFalse(Storage::disk('public')->exists($filePath));
        $this->assertFalse(Storage::disk('public')->exists($reportPath));
        $this->assertFalse(Storage::disk('public')->exists($photoPath));
    }

    public function test_can_delete_scholarship_without_applications(): void
    {
        $this->actingAs($this->adminUser);

        // Create a scholarship program without applications
        $scholarship = ScholarshipProgram::factory()->create([
            'name' => 'Empty Scholarship for Deletion',
        ]);

        // Create document requirements
        $documentRequirement = DocumentRequirement::factory()->create([
            'scholarship_program_id' => $scholarship->id,
        ]);

        // Verify records exist
        $this->assertDatabaseHas('scholarship_programs', ['id' => $scholarship->id]);
        $this->assertDatabaseHas('document_requirements', ['id' => $documentRequirement->id]);

        // Delete the scholarship
        $response = $this->delete(route('admin.scholarships.destroy', $scholarship->id));

        // Assert successful deletion
        $response->assertRedirect(route('admin.scholarships.index'));
        $response->assertSessionHas('success', 'Scholarship program and all related data deleted successfully.');

        // Verify records are deleted
        $this->assertDatabaseMissing('scholarship_programs', ['id' => $scholarship->id]);
        $this->assertDatabaseMissing('document_requirements', ['id' => $documentRequirement->id]);
    }

    public function test_non_admin_cannot_delete_scholarship(): void
    {
        // Act as student user
        $this->actingAs($this->studentUser);

        $scholarship = ScholarshipProgram::factory()->create();

        // Attempt to delete scholarship
        $response = $this->delete(route('admin.scholarships.destroy', $scholarship->id));

        // Should be forbidden or redirected
        $response->assertStatus(403);

        // Verify scholarship still exists
        $this->assertDatabaseHas('scholarship_programs', ['id' => $scholarship->id]);
    }

    public function test_unauthenticated_user_cannot_delete_scholarship(): void
    {
        $scholarship = ScholarshipProgram::factory()->create();

        // Attempt to delete scholarship without authentication
        $response = $this->delete(route('admin.scholarships.destroy', $scholarship->id));

        // Should redirect to login
        $response->assertRedirect(route('login'));

        // Verify scholarship still exists
        $this->assertDatabaseHas('scholarship_programs', ['id' => $scholarship->id]);
    }
}
