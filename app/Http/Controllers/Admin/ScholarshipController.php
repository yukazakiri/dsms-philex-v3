<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class ScholarshipController extends Controller
{
    /**
     * Display a listing of scholarship programs.
     */
    public function index(): Response
    {
        $scholarships = \App\Models\ScholarshipProgram::query()->withCount(['scholarshipApplications'])
            ->latest()
            ->get()
            ->map(fn($scholarship) =>
                // Optional: Transform for index page if needed, though not strictly required by the prompt
                // For now, keeping it simple for index.
                $scholarship);

        return Inertia::render('Admin/Scholarship/Index', [
            'scholarships' => $scholarships,
        ]);
    }

    /**
     * Show the form for creating a new scholarship program.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Scholarship/Create');
    }

    /**
     * Store a newly created scholarship program.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'total_budget' => ['required', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'per_student_budget' => ['required', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'school_type_eligibility' => ['required', 'in:high_school,college,both'],
            'min_gpa' => ['required', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'min_units' => ['nullable', 'string', 'regex:/^\d+$/'],
            'semester' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:255'],
            'application_deadline' => ['required', 'date'],
            'community_service_days' => ['required', 'string', 'regex:/^\d+$/'],
            'active' => ['boolean'],
            'available_slots' => ['required', 'string', 'regex:/^\d+$/'],
            'document_requirements' => ['array'],
            'document_requirements.*.name' => ['required_with:document_requirements', 'string', 'max:255'],
            'document_requirements.*.description' => ['required_with:document_requirements', 'string'],
            'document_requirements.*.is_required' => ['required_with:document_requirements', 'boolean'],
        ]);

        $scholarship = \App\Models\ScholarshipProgram::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'total_budget' => (float) $validated['total_budget'],
            'per_student_budget' => (float) $validated['per_student_budget'],
            'school_type_eligibility' => $validated['school_type_eligibility'],
            'min_gpa' => (float) $validated['min_gpa'],
            'min_units' => $validated['min_units'] ? (int) $validated['min_units'] : null,
            'semester' => $validated['semester'],
            'academic_year' => $validated['academic_year'],
            'application_deadline' => $validated['application_deadline'],
            'community_service_days' => (int) $validated['community_service_days'],
            'active' => $validated['active'] ?? true,
            'available_slots' => (int) $validated['available_slots'],
        ]);

        if (isset($validated['document_requirements'])) {
            foreach ($validated['document_requirements'] as $requirement) {
                $scholarship->documentRequirements()->create($requirement);
            }
        }

        return Redirect::route('admin.scholarships.index')
            ->with('success', 'Scholarship program created successfully.');
    }

    /**
     * Display the specified scholarship program.
     */
    public function show(ScholarshipProgram $scholarship): Response
    {
        // Load relationships
        $scholarship->load([
            'documentRequirements',
            'scholarshipApplications.studentProfile.user',
            'scholarshipApplications.documentUploads', // Added for completeness if needed on Show page
            'scholarshipApplications.disbursements',    // Added for completeness
        ]);

        // Transform data to camelCase
        $scholarshipData = $scholarship->toArray();

        if (isset($scholarshipData['document_requirements'])) {
            $scholarshipData['documentRequirements'] = $scholarshipData['document_requirements'];
            unset($scholarshipData['document_requirements']);
        }

        if (isset($scholarshipData['scholarship_applications'])) {
            $scholarshipData['scholarshipApplications'] = array_map(function ($application) {
                if (isset($application['student_profile'])) {
                    $application['studentProfile'] = $application['student_profile'];
                    unset($application['student_profile']);
                    // User within studentProfile should be fine as it's typically an object of attributes
                    // If user itself had snake_case keys needing transformation, do it here.
                }

                if (isset($application['document_uploads'])) {
                    $application['documentUploads'] = $application['document_uploads'];
                    unset($application['document_uploads']);
                }

                if (isset($application['disbursements'])) {
                    unset($application['disbursements']);
                }

                // If ScholarshipProgram is nested under application and needs casing:
                if (isset($application['scholarship_program'])) {
                    $application['scholarshipProgram'] = $application['scholarship_program'];
                    unset($application['scholarship_program']);
                }

                return $application;
            }, $scholarshipData['scholarship_applications']);
            unset($scholarshipData['scholarship_applications']);
        } else {
            $scholarshipData['scholarshipApplications'] = [];
        }

        return Inertia::render('Admin/Scholarship/Show', [
            'scholarship' => $scholarshipData,
        ]);
    }

    /**
     * Show the form for editing the specified scholarship program.
     */
    public function edit(ScholarshipProgram $scholarship): Response
    {
        $scholarship->load('documentRequirements');
        $scholarshipData = $scholarship->toArray();

        if (isset($scholarshipData['document_requirements'])) {
            $scholarshipData['documentRequirements'] = $scholarshipData['document_requirements'];
            unset($scholarshipData['document_requirements']);
        }

        return Inertia::render('Admin/Scholarship/Edit', [
            'scholarship' => $scholarshipData,
        ]);
    }

    /**
     * Update the specified scholarship program.
     */
    public function update(Request $request, ScholarshipProgram $scholarship): RedirectResponse
    {
        // Log incoming request data for debugging
        Log::info('Scholarship Update Request Data:', [
            'scholarship_id' => $scholarship->id,
            'request_data' => $request->all(),
            'has_document_requirements' => $request->has('documentRequirements'),
            'document_requirements_count' => $request->has('documentRequirements') ? count($request->input('documentRequirements', [])) : 0
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'total_budget' => ['required', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'per_student_budget' => ['required', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'school_type_eligibility' => ['required', 'in:high_school,college,both'],
            'min_gpa' => ['required', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'min_units' => ['nullable', 'string', 'regex:/^\d+$/'],
            'semester' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:255'],
            'application_deadline' => ['required', 'date'],
            'community_service_days' => ['required', 'string', 'regex:/^\d+$/'],
            'active' => ['boolean'],
            'available_slots' => ['required', 'string', 'regex:/^\d+$/'],
            // Add validation for document requirements
            'documentRequirements' => ['array'],
            'documentRequirements.*.id' => ['nullable', 'integer'],
            'documentRequirements.*.name' => ['required_with:documentRequirements', 'string', 'max:255'],
            'documentRequirements.*.description' => ['required_with:documentRequirements', 'string'],
            'documentRequirements.*.is_required' => ['required_with:documentRequirements', 'boolean'],
            'documentRequirements.*.isNew' => ['nullable', 'boolean'],
            'documentRequirements.*.isDeleted' => ['nullable', 'boolean'],
        ]);

        Log::info('Scholarship Update Validated Data:', [
            'scholarship_id' => $scholarship->id,
            'validated_data' => $validated,
            'document_requirements_validated' => $validated['documentRequirements'] ?? 'not present'
        ]);

        // Update the scholarship basic fields
        $scholarshipData = collect($validated)->except(['documentRequirements'])->toArray();

        // Convert string numeric fields to proper types
        $scholarshipData['total_budget'] = (float) $scholarshipData['total_budget'];
        $scholarshipData['per_student_budget'] = (float) $scholarshipData['per_student_budget'];
        $scholarshipData['min_gpa'] = (float) $scholarshipData['min_gpa'];
        $scholarshipData['min_units'] = $scholarshipData['min_units'] ? (int) $scholarshipData['min_units'] : null;
        $scholarshipData['community_service_days'] = (int) $scholarshipData['community_service_days'];
        $scholarshipData['available_slots'] = (int) $scholarshipData['available_slots'];

        $scholarship->update($scholarshipData);

        // Handle document requirements update
        if (isset($validated['documentRequirements'])) {
            $this->updateDocumentRequirements($scholarship, $validated['documentRequirements']);
        }

        Log::info('Scholarship Update Completed:', [
            'scholarship_id' => $scholarship->id,
            'updated_scholarship' => $scholarship->fresh()->load('documentRequirements')->toArray()
        ]);

        return Redirect::route('admin.scholarships.show', $scholarship->id)
            ->with('success', 'Scholarship program updated successfully.');
    }

    /**
     * Update document requirements for a scholarship program.
     */
    private function updateDocumentRequirements(ScholarshipProgram $scholarship, array $documentRequirements): void
    {
        Log::info('Updating Document Requirements:', [
            'scholarship_id' => $scholarship->id,
            'requirements_data' => $documentRequirements
        ]);

        // Get existing requirements
        $existingRequirements = $scholarship->documentRequirements()->get()->keyBy('id');
        $submittedIds = collect($documentRequirements)->pluck('id')->filter()->toArray();
        $deletedIds = collect($documentRequirements)
            ->filter(fn($req) => ($req['isDeleted'] ?? false) === true)
            ->pluck('id')
            ->filter()
            ->toArray();

        Log::info('Document Requirements Analysis:', [
            'existing_count' => $existingRequirements->count(),
            'submitted_count' => count($documentRequirements),
            'existing_ids' => $existingRequirements->keys()->toArray(),
            'submitted_ids' => $submittedIds,
            'deleted_ids' => $deletedIds
        ]);

        // First, handle deletions
        if (!empty($deletedIds)) {
            $scholarship->documentRequirements()->whereIn('id', $deletedIds)->delete();

            Log::info('Deleted Document Requirements:', [
                'scholarship_id' => $scholarship->id,
                'deleted_ids' => $deletedIds
            ]);
        }

        // Process each submitted requirement (excluding deleted ones)
        foreach ($documentRequirements as $requirementData) {
            $requirementId = $requirementData['id'] ?? null;
            $isNew = $requirementData['isNew'] ?? false;
            $isDeleted = $requirementData['isDeleted'] ?? false;

            // Skip deleted requirements (already handled above)
            if ($isDeleted) {
                continue;
            }

            $data = [
                'name' => $requirementData['name'],
                'description' => $requirementData['description'],
                'is_required' => $requirementData['is_required'],
            ];

            if ($isNew || !$requirementId || !$existingRequirements->has($requirementId)) {
                // Create new requirement
                $data['scholarship_program_id'] = $scholarship->id;
                $newRequirement = $scholarship->documentRequirements()->create($data);

                Log::info('Created New Document Requirement:', [
                    'scholarship_id' => $scholarship->id,
                    'requirement_id' => $newRequirement->id,
                    'requirement_data' => $data
                ]);
            } else {
                // Update existing requirement
                $existingRequirement = $existingRequirements->get($requirementId);
                $existingRequirement->update($data);

                Log::info('Updated Existing Document Requirement:', [
                    'scholarship_id' => $scholarship->id,
                    'requirement_id' => $requirementId,
                    'requirement_data' => $data
                ]);
            }
        }

        Log::info('Document Requirements Update Completed:', [
            'scholarship_id' => $scholarship->id,
            'final_requirements' => $scholarship->documentRequirements()->get()->toArray()
        ]);
    }

    /**
     * Remove the specified scholarship program.
     */
    public function destroy(ScholarshipProgram $scholarship): RedirectResponse
    {
        try {
            // Load all related data for proper cleanup
            $scholarship->load([
                'scholarshipApplications.documentUploads',
                'scholarshipApplications.communityServiceReports',
                'scholarshipApplications.communityServiceEntries',
                'scholarshipApplications.disbursements',
                'documentRequirements'
            ]);

            Log::info('Starting scholarship deletion process', [
                'scholarship_id' => $scholarship->id,
                'scholarship_name' => $scholarship->name,
                'applications_count' => $scholarship->scholarshipApplications->count(),
                'document_requirements_count' => $scholarship->documentRequirements->count()
            ]);

            // Delete all document upload files from storage
            foreach ($scholarship->scholarshipApplications as $application) {
                foreach ($application->documentUploads as $documentUpload) {
                    $this->deleteDocumentFile($documentUpload->file_path);
                }

                // Delete community service report PDF files if they exist
                foreach ($application->communityServiceReports as $report) {
                    if ($report->pdf_report_path) {
                        $this->deleteDocumentFile($report->pdf_report_path);
                    }
                }

                // Delete community service entry photos if they exist
                foreach ($application->communityServiceEntries as $entry) {
                    if ($entry->photos) {
                        $photos = is_string($entry->photos) ? json_decode($entry->photos, true) : $entry->photos;
                        if (is_array($photos)) {
                            foreach ($photos as $photoPath) {
                                $this->deleteDocumentFile($photoPath);
                            }
                        }
                    }
                }
            }

            // The database foreign key constraints with cascadeOnDelete() will handle
            // the deletion of related records automatically:
            // - scholarship_applications (cascadeOnDelete)
            // - document_uploads (cascadeOnDelete via scholarship_applications)
            // - community_service_reports (cascadeOnDelete via scholarship_applications)
            // - community_service_entries (cascadeOnDelete via scholarship_applications)
            // - disbursements (cascadeOnDelete via scholarship_applications)

            // Manually delete document requirements (no cascade constraint)
            $scholarship->documentRequirements()->delete();

            // Delete the scholarship program (this will trigger cascade deletions)
            $scholarship->delete();

            Log::info('Scholarship deletion completed successfully', [
                'scholarship_id' => $scholarship->id,
                'scholarship_name' => $scholarship->name
            ]);

            return Redirect::route('admin.scholarships.index')
                ->with('success', 'Scholarship program and all related data deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting scholarship program', [
                'scholarship_id' => $scholarship->id,
                'scholarship_name' => $scholarship->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::back()->with('error', 'An error occurred while deleting the scholarship program. Please try again.');
        }
    }

    /**
     * Delete a file from storage if it exists.
     */
    private function deleteDocumentFile(string $filePath): void
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info('Deleted file from storage', ['file_path' => $filePath]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete file from storage', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }
}
