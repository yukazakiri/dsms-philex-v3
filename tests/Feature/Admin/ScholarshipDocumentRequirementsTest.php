<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\ScholarshipProgram;
use App\Models\DocumentRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScholarshipDocumentRequirementsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);
    }

    public function test_can_create_scholarship_with_document_requirements(): void
    {
        $this->actingAs($this->adminUser);

        $scholarshipData = [
            'name' => 'Test Scholarship',
            'description' => 'A test scholarship program',
            'total_budget' => 10000.00,
            'per_student_budget' => 1000.00,
            'school_type_eligibility' => 'both',
            'min_gpa' => 80.0,
            'min_units' => 12,
            'semester' => 'Fall',
            'academic_year' => '2024-2025',
            'application_deadline' => '2024-12-31',
            'community_service_days' => 5,
            'active' => true,
            'available_slots' => 10,
            'document_requirements' => [
                [
                    'name' => 'Transcript',
                    'description' => 'Official academic transcript',
                    'is_required' => true,
                ],
                [
                    'name' => 'Letter of Recommendation',
                    'description' => 'Letter from a teacher or counselor',
                    'is_required' => true,
                ],
                [
                    'name' => 'Personal Statement',
                    'description' => 'Essay about your goals',
                    'is_required' => false,
                ],
            ],
        ];

        $response = $this->post(route('admin.scholarships.store'), $scholarshipData);

        $response->assertRedirect(route('admin.scholarships.index'));
        $response->assertSessionHas('success');

        // Verify scholarship was created
        $this->assertDatabaseHas('scholarship_programs', [
            'name' => 'Test Scholarship',
            'description' => 'A test scholarship program',
        ]);

        // Verify document requirements were created
        $scholarship = ScholarshipProgram::where('name', 'Test Scholarship')->first();
        $this->assertNotNull($scholarship);

        $this->assertDatabaseHas('document_requirements', [
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Transcript',
            'description' => 'Official academic transcript',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('document_requirements', [
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Letter of Recommendation',
            'description' => 'Letter from a teacher or counselor',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('document_requirements', [
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Personal Statement',
            'description' => 'Essay about your goals',
            'is_required' => false,
        ]);

        // Verify the count of document requirements
        $this->assertEquals(3, $scholarship->documentRequirements()->count());
    }

    public function test_can_create_scholarship_without_document_requirements(): void
    {
        $this->actingAs($this->adminUser);

        $scholarshipData = [
            'name' => 'Simple Scholarship',
            'description' => 'A scholarship without document requirements',
            'total_budget' => 5000.00,
            'per_student_budget' => 500.00,
            'school_type_eligibility' => 'high_school',
            'min_gpa' => 75.0,
            'min_units' => null,
            'semester' => 'Spring',
            'academic_year' => '2024-2025',
            'application_deadline' => '2024-11-30',
            'community_service_days' => 3,
            'active' => true,
            'available_slots' => 10,
            'document_requirements' => [],
        ];

        $response = $this->post(route('admin.scholarships.store'), $scholarshipData);

        $response->assertRedirect(route('admin.scholarships.index'));
        $response->assertSessionHas('success');

        // Verify scholarship was created
        $scholarship = ScholarshipProgram::where('name', 'Simple Scholarship')->first();
        $this->assertNotNull($scholarship);

        // Verify no document requirements were created
        $this->assertEquals(0, $scholarship->documentRequirements()->count());
    }

    public function test_validation_fails_for_invalid_document_requirements(): void
    {
        $this->actingAs($this->adminUser);

        $scholarshipData = [
            'name' => 'Test Scholarship',
            'description' => 'A test scholarship program',
            'total_budget' => 10000.00,
            'per_student_budget' => 1000.00,
            'school_type_eligibility' => 'both',
            'min_gpa' => 80.0,
            'min_units' => 12,
            'semester' => 'Fall',
            'academic_year' => '2024-2025',
            'application_deadline' => '2024-12-31',
            'community_service_days' => 5,
            'active' => true,
            'available_slots' => 10,
            'document_requirements' => [
                [
                    'name' => '', // Empty name should fail validation
                    'description' => 'Official academic transcript',
                    'is_required' => true,
                ],
            ],
        ];

        $response = $this->post(route('admin.scholarships.store'), $scholarshipData);

        $response->assertSessionHasErrors(['document_requirements.0.name']);
    }
}
