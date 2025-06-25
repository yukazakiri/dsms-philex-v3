<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $status = $request->input('status');
        $role = $request->input('role');

        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $usersQuery->where('status', $status);
        }

        if ($role && $role !== 'all') {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(function (User $user) {
                return [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'role'              => $user->role,
                    'status'            => $user->status,
                    'last_login_at'     => $user->last_login_at?->format('M j, Y g:i A'),
                    'created_at'        => $user->created_at->format('M j, Y'),
                    'email_verified_at' => $user->email_verified_at,
                    'status_badge_color' => $user->getStatusBadgeColor(),
                    'avatar_url'        => $user->getBestAvatarUrl(),
                ];
            });

        return Inertia::render('Admin/User/Index', [
            'users'   => $users,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'role'   => $role,
            ],
            'statusOptions' => [
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'inactive', 'label' => 'Inactive'],
                ['value' => 'suspended', 'label' => 'Suspended'],
                ['value' => 'pending', 'label' => 'Pending'],
            ],
            'roleOptions' => [
                ['value' => 'admin', 'label' => 'Admin'],
                ['value' => 'student', 'label' => 'Student'],
            ],
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('studentProfile')->findOrFail($id);

        return Inertia::render('Admin/User/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at?->format('M j, Y g:i A'),
                'created_at' => $user->created_at->format('M j, Y g:i A'),
                'updated_at' => $user->updated_at->format('M j, Y g:i A'),
                'email_verified_at' => $user->email_verified_at?->format('M j, Y g:i A'),
                'avatar_url' => $user->getBestAvatarUrl(),
                'cover_image_url' => $user->getCoverImageUrl(),
                'provider' => $user->provider,
                'student_profile' => $user->studentProfile ? [
                    'address' => $user->studentProfile->address,
                    'city' => $user->studentProfile->city,
                    'state' => $user->studentProfile->state,
                    'zip_code' => $user->studentProfile->zip_code,
                    'phone_number' => $user->studentProfile->phone_number,
                    'school_type' => $user->studentProfile->school_type,
                    'school_level' => $user->studentProfile->school_level,
                    'school_name' => $user->studentProfile->school_name,
                    'student_id' => $user->studentProfile->student_id,
                    'gpa' => $user->studentProfile->gpa,
                ] : null,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return Inertia::render('Admin/User/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
            ],
            'statusOptions' => [
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'inactive', 'label' => 'Inactive'],
                ['value' => 'suspended', 'label' => 'Suspended'],
                ['value' => 'pending', 'label' => 'Pending'],
            ],
            'roleOptions' => [
                ['value' => 'admin', 'label' => 'Admin'],
                ['value' => 'student', 'label' => 'Student'],
            ],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('Admin/User/Create', [
            'statusOptions' => [
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'inactive', 'label' => 'Inactive'],
                ['value' => 'suspended', 'label' => 'Suspended'],
                ['value' => 'pending', 'label' => 'Pending'],
            ],
            'roleOptions' => [
                ['value' => 'admin', 'label' => 'Admin'],
                ['value' => 'student', 'label' => 'Student'],
            ],
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,student'],
            'status' => ['required', 'in:active,inactive,suspended,pending'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,student'],
            'status' => ['required', 'in:active,inactive,suspended,pending'],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive,suspended,pending'],
        ]);

        // Prevent admin from suspending themselves
        if ($user->id === auth()->id() && $validated['status'] === 'suspended') {
            return redirect()->route('admin.users.index')->with('error', 'You cannot suspend your own account.');
        }

        $user->update(['status' => $validated['status']]);

        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully.');
    }

    /**
     * Bulk update user status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive,suspended,pending'],
        ]);

        $userIds = collect($validated['user_ids']);

        // Prevent admin from affecting their own account in bulk operations
        if ($userIds->contains(auth()->id()) && $validated['status'] === 'suspended') {
            return redirect()->route('admin.users.index')->with('error', 'You cannot suspend your own account.');
        }

        User::whereIn('id', $validated['user_ids'])->update(['status' => $validated['status']]);

        return redirect()->route('admin.users.index')->with('success', 'User statuses updated successfully.');
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $userIds = collect($validated['user_ids']);

        // Prevent admin from deleting themselves
        if ($userIds->contains(auth()->id())) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        User::whereIn('id', $validated['user_ids'])->delete();

        return redirect()->route('admin.users.index')->with('success', 'Users deleted successfully.');
    }

    /**
     * Send password reset email to user
     */
    public function sendPasswordReset(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Generate password reset token
        $token = app('auth.password.broker')->createToken($user);

        // Send password reset notification
        $user->sendPasswordResetNotification($token);

        return redirect()->back()->with('success', 'Password reset email sent successfully.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully.');
    }

    /**
     * Force user to change password on next login
     */
    public function forcePasswordChange(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // You could add a field to track this, for now we'll just send a reset email
        $token = app('auth.password.broker')->createToken($user);
        $user->sendPasswordResetNotification($token);

        return redirect()->back()->with('success', 'User will be required to change password on next login.');
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply same filters as index
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Email', 'Role', 'Status', 'Last Login', 'Created At', 'Email Verified'];

        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->status,
                $user->last_login_at?->format('Y-m-d H:i:s') ?? '',
                $user->created_at->format('Y-m-d H:i:s'),
                $user->email_verified_at ? 'Yes' : 'No',
            ];
        }

        $filename = 'users_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import users from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        $imported = 0;
        $errors = [];

        foreach ($csvData as $index => $row) {
            try {
                $data = array_combine($header, $row);

                // Validate required fields
                if (empty($data['name']) || empty($data['email'])) {
                    $errors[] = "Row " . ($index + 2) . ": Name and email are required";
                    continue;
                }

                // Check if user already exists
                if (User::where('email', $data['email'])->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": User with email {$data['email']} already exists";
                    continue;
                }

                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => $data['role'] ?? 'student',
                    'status' => $data['status'] ?? 'active',
                    'password' => bcrypt($data['password'] ?? 'password123'),
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Successfully imported $imported users.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " and " . (count($errors) - 5) . " more...";
            }
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }
}
