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

    public function test_can_update_scholarship_with_document_requirements(): void
    {
        $this->actingAs($this->adminUser);

        // First create a scholarship with some document requirements
        $scholarship = ScholarshipProgram::factory()->create([
            'name' => 'Original Scholarship',
            'description' => 'Original description',
        ]);

        $originalReq1 = DocumentRequirement::factory()->create([
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Original Requirement 1',
            'description' => 'Original description 1',
            'is_required' => true,
        ]);

        $originalReq2 = DocumentRequirement::factory()->create([
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Original Requirement 2',
            'description' => 'Original description 2',
            'is_required' => false,
        ]);

        // Now update the scholarship with modified requirements
        $updateData = [
            'name' => 'Updated Scholarship',
            'description' => 'Updated description',
            'total_budget' => $scholarship->total_budget,
            'per_student_budget' => $scholarship->per_student_budget,
            'school_type_eligibility' => $scholarship->school_type_eligibility,
            'min_gpa' => $scholarship->min_gpa,
            'min_units' => $scholarship->min_units,
            'semester' => $scholarship->semester,
            'academic_year' => $scholarship->academic_year,
            'application_deadline' => $scholarship->application_deadline->format('Y-m-d'),
            'community_service_days' => $scholarship->community_service_days,
            'active' => $scholarship->active,
            'available_slots' => $scholarship->available_slots,
            'documentRequirements' => [
                // Update existing requirement 1
                [
                    'id' => $originalReq1->id,
                    'name' => 'Updated Requirement 1',
                    'description' => 'Updated description 1',
                    'is_required' => true,
                    'isNew' => false,
                    'isDeleted' => false,
                ],
                // Mark requirement 2 for deletion
                [
                    'id' => $originalReq2->id,
                    'name' => $originalReq2->name,
                    'description' => $originalReq2->description,
                    'is_required' => $originalReq2->is_required,
                    'isNew' => false,
                    'isDeleted' => true,
                ],
                // Add new requirement
                [
                    'id' => null,
                    'name' => 'New Requirement',
                    'description' => 'New requirement description',
                    'is_required' => true,
                    'isNew' => true,
                    'isDeleted' => false,
                ],
            ],
        ];

        $response = $this->put(route('admin.scholarships.update', $scholarship->id), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify scholarship basic fields were updated
        $this->assertDatabaseHas('scholarship_programs', [
            'id' => $scholarship->id,
            'name' => 'Updated Scholarship',
            'description' => 'Updated description',
        ]);

        // Verify requirement 1 was updated
        $this->assertDatabaseHas('document_requirements', [
            'id' => $originalReq1->id,
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Updated Requirement 1',
            'description' => 'Updated description 1',
            'is_required' => true,
        ]);

        // Verify requirement 2 was deleted
        $this->assertDatabaseMissing('document_requirements', [
            'id' => $originalReq2->id,
        ]);

        // Verify new requirement was created
        $this->assertDatabaseHas('document_requirements', [
            'scholarship_program_id' => $scholarship->id,
            'name' => 'New Requirement',
            'description' => 'New requirement description',
            'is_required' => true,
        ]);

        // Verify final count of requirements (1 updated + 1 new = 2 total)
        $scholarship->refresh();
        $this->assertEquals(2, $scholarship->documentRequirements()->count());
    }

    public function test_can_add_new_document_requirements_to_existing_scholarship(): void
    {
        $this->actingAs($this->adminUser);

        // Create a scholarship without document requirements
        $scholarship = ScholarshipProgram::factory()->create();

        $updateData = [
            'name' => $scholarship->name,
            'description' => $scholarship->description,
            'total_budget' => $scholarship->total_budget,
            'per_student_budget' => $scholarship->per_student_budget,
            'school_type_eligibility' => $scholarship->school_type_eligibility,
            'min_gpa' => $scholarship->min_gpa,
            'min_units' => $scholarship->min_units,
            'semester' => $scholarship->semester,
            'academic_year' => $scholarship->academic_year,
            'application_deadline' => $scholarship->application_deadline->format('Y-m-d'),
            'community_service_days' => $scholarship->community_service_days,
            'active' => $scholarship->active,
            'available_slots' => $scholarship->available_slots,
            'documentRequirements' => [
                [
                    'id' => null,
                    'name' => 'First New Requirement',
                    'description' => 'First new requirement description',
                    'is_required' => true,
                    'isNew' => true,
                    'isDeleted' => false,
                ],
                [
                    'id' => null,
                    'name' => 'Second New Requirement',
                    'description' => 'Second new requirement description',
                    'is_required' => false,
                    'isNew' => true,
                    'isDeleted' => false,
                ],
            ],
        ];

        $response = $this->put(route('admin.scholarships.update', $scholarship->id), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify new requirements were created
        $this->assertDatabaseHas('document_requirements', [
            'scholarship_program_id' => $scholarship->id,
            'name' => 'First New Requirement',
            'description' => 'First new requirement description',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('document_requirements', [
            'scholarship_program_id' => $scholarship->id,
            'name' => 'Second New Requirement',
            'description' => 'Second new requirement description',
            'is_required' => false,
        ]);

        // Verify count
        $scholarship->refresh();
        $this->assertEquals(2, $scholarship->documentRequirements()->count());
    }
}
