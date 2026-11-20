<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Check whether logged-in user is Admin.
     *
     * This uses the existing User -> role relationship.
     */
    private function isAdmin()
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->role && strtolower($user->role->name) === 'admin';
    }

    /**
     * Display a listing of users with roles.
     */
    public function index(Request $request)
    {
        $loggedInUser = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */
        $query = User::with('role')->latest();

        /*
        |--------------------------------------------------------------------------
        | ADMIN vs NON-ADMIN
        |--------------------------------------------------------------------------
        |
        | Admin:
        |     Can see ALL users.
        |
        | Non-admin:
        |     Can see ONLY users having the same role_id.
        |
        |--------------------------------------------------------------------------
        */
        if (!$this->isAdmin()) {
            $query->where('role_id', $loggedInUser->role_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Role Filter
        |--------------------------------------------------------------------------
        |
        | Admin can filter any role.
        |
        | Non-admin cannot change the role filter to another role.
        | We force their own role_id.
        |
        |--------------------------------------------------------------------------
        */
        if ($this->isAdmin()) {

            if ($request->filled('role_id')) {
                $query->where('role_id', $request->role_id);
            }

        } else {

            // Always force non-admin to their own role
            $query->where('role_id', $loggedInUser->role_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        $allowedSorts = [
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.username',
            'users.email',
            'users.status',
            'users.created_at',
        ];

        $sort = $request->get('sort', 'users.created_at');
        $direction = strtolower($request->get('direction', 'desc'));

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'users.created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $listing = $query->paginate(10)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        |
        | Admin:
        |     All active roles.
        |
        | Non-admin:
        |     Only their own role.
        |
        |--------------------------------------------------------------------------
        */
        if ($this->isAdmin()) {

            $roles = Role::where('status', Role::STATUS_ACTIVE)
                ->orderBy('name')
                ->get();

        } else {

            $roles = Role::where('status', Role::STATUS_ACTIVE)
                ->where('id', $loggedInUser->role_id)
                ->orderBy('name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        if ($this->isAdmin()) {

            $stats = [
                'total' => User::count(),

                'active' => User::where(
                    'status',
                    User::STATUS_ACTIVE
                )->count(),

                'inactive' => User::where(
                    'status',
                    User::STATUS_INACTIVE
                )->count(),

                'roles_count' => Role::count(),
            ];

        } else {

            $stats = [
                'total' => User::where(
                    'role_id',
                    $loggedInUser->role_id
                )->count(),

                'active' => User::where(
                    'role_id',
                    $loggedInUser->role_id
                )
                    ->where('status', User::STATUS_ACTIVE)
                    ->count(),

                'inactive' => User::where(
                    'role_id',
                    $loggedInUser->role_id
                )
                    ->where('status', User::STATUS_INACTIVE)
                    ->count(),

                'roles_count' => 1,
            ];
        }

        return view(
            'admin.users.index',
            compact('listing', 'roles', 'stats')
        );
    }

    /**
     * Show the form for creating a new user.
     *
     * Only Admin can add users.
     */
    public function add(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Only Admin Can Add New User
        |--------------------------------------------------------------------------
        */
        if (!$this->isAdmin()) {
            abort(403, 'You are not authorized to add users.');
        }

        /*
        |--------------------------------------------------------------------------
        | Store New User
        |--------------------------------------------------------------------------
        */
        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'role_id' => [
                    'required',
                    'exists:roles,id'
                ],

                'first_name' => [
                    'required',
                    'string',
                    'max:100'
                ],

                'last_name' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'username' => [
                    'required',
                    'string',
                    'max:100',
                    'alpha_dash',
                    'unique:users,username'
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email'
                ],

                'password' => [
                    'required',
                    'string',
                    'min:6'
                ],

                'phone_number' => [
                    'nullable',
                    'string',
                    'max:30'
                ],

                'image' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'max:2048'
                ],

                'status' => [
                    'required',
                    Rule::in([
                        User::STATUS_ACTIVE,
                        User::STATUS_INACTIVE
                    ])
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Image Upload
            |--------------------------------------------------------------------------
            */
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request
                    ->file('image')
                    ->store('users', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */
            User::create([
                'role_id' => $validated['role_id'],

                'first_name' => $validated['first_name'],

                'last_name' => $validated['last_name'] ?? null,

                'username' => strtolower(
                    $validated['username']
                ),

                'email' => strtolower(
                    $validated['email']
                ),

                'password' => Hash::make(
                    $validated['password']
                ),

                'phone_number' => $validated['phone_number'] ?? null,

                'image' => $imagePath,

                'status' => (int) $validated['status'],

                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('users.users')
                ->with(
                    'success',
                    'User added successfully with assigned role.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Active Roles
        |--------------------------------------------------------------------------
        */
        $roles = Role::where(
            'status',
            Role::STATUS_ACTIVE
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.users.create',
            compact('roles')
        );
    }

    /**
     * Display the specified user.
     */
    public function view(Request $request, $id)
    {
        $user = User::with('role')->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Non-admin can only view same-role users
        |--------------------------------------------------------------------------
        */
        if (
            !$this->isAdmin()
            && $user->role_id != auth()->user()->role_id
        ) {
            abort(403, 'You are not authorized to view this user.');
        }

        return view(
            'admin.users.view',
            compact('user')
        );
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */
        if (
            !$this->isAdmin()
            && $user->role_id != auth()->user()->role_id
        ) {
            abort(403, 'You are not authorized to edit this user.');
        }

        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */
        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'role_id' => [
                    'required',
                    'exists:roles,id'
                ],

                'first_name' => [
                    'required',
                    'string',
                    'max:100'
                ],

                'last_name' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'username' => [
                    'required',
                    'string',
                    'max:100',
                    'alpha_dash',

                    Rule::unique(
                        'users',
                        'username'
                    )->ignore($user->id),
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',

                    Rule::unique(
                        'users',
                        'email'
                    )->ignore($user->id),
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:6'
                ],

                'phone_number' => [
                    'nullable',
                    'string',
                    'max:30'
                ],

                'image' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,png,jpg,webp',
                    'max:2048'
                ],

                'status' => [
                    'required',
                    Rule::in([
                        User::STATUS_ACTIVE,
                        User::STATUS_INACTIVE
                    ])
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Non-admin cannot assign/change role
            |--------------------------------------------------------------------------
            |
            | They can edit users belonging to their role, but cannot move
            | that user to another role.
            |
            |--------------------------------------------------------------------------
            */
            if (!$this->isAdmin()) {
                $validated['role_id'] = auth()->user()->role_id;
            }

            /*
            |--------------------------------------------------------------------------
            | Image
            |--------------------------------------------------------------------------
            */
            $imagePath = $user->image;

            if ($request->hasFile('image')) {

                if (
                    $user->image
                    && Storage::disk('public')->exists($user->image)
                ) {
                    Storage::disk('public')->delete(
                        $user->image
                    );
                }

                $imagePath = $request
                    ->file('image')
                    ->store('users', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | Update Data
            |--------------------------------------------------------------------------
            */
            $data = [
                'role_id' => $validated['role_id'],

                'first_name' => $validated['first_name'],

                'last_name' => $validated['last_name'] ?? null,

                'username' => strtolower(
                    $validated['username']
                ),

                'email' => strtolower(
                    $validated['email']
                ),

                'phone_number' => $validated['phone_number'] ?? null,

                'image' => $imagePath,

                'status' => (int) $validated['status'],
            ];

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */
            if (!empty($validated['password'])) {
                $data['password'] = Hash::make(
                    $validated['password']
                );
            }

            $user->update($data);

            return redirect()
                ->route('users.users')
                ->with(
                    'success',
                    'User updated successfully.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles For Edit
        |--------------------------------------------------------------------------
        */
        if ($this->isAdmin()) {

            $roles = Role::where(
                'status',
                Role::STATUS_ACTIVE
            )
                ->orderBy('name')
                ->get();

        } else {

            $roles = Role::where(
                'status',
                Role::STATUS_ACTIVE
            )
                ->where(
                    'id',
                    auth()->user()->role_id
                )
                ->orderBy('name')
                ->get();
        }

        return view(
            'admin.users.edit',
            compact('user', 'roles')
        );
    }

    /**
     * Update the specified user in storage.
     *
     * Kept for compatibility if this method is used by another route.
     */
    public function update(Request $request, User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */
        if (
            !$this->isAdmin()
            && $user->role_id != auth()->user()->role_id
        ) {
            abort(403, 'You are not authorized to update this user.');
        }

        $validated = $request->validate([
            'role_id' => [
                'required',
                'exists:roles,id'
            ],

            'first_name' => [
                'required',
                'string',
                'max:100'
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100'
            ],

            'username' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',

                Rule::unique(
                    'users',
                    'username'
                )->ignore($user->id),
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6'
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:30'
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048'
            ],

            'status' => [
                'required',
                Rule::in([
                    User::STATUS_ACTIVE,
                    User::STATUS_INACTIVE
                ])
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Non-admin stays in own role
        |--------------------------------------------------------------------------
        */
        if (!$this->isAdmin()) {
            $validated['role_id'] = auth()->user()->role_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Image
        |--------------------------------------------------------------------------
        */
        $imagePath = $user->image;

        if ($request->hasFile('image')) {

            if (
                $user->image
                && Storage::disk('public')->exists($user->image)
            ) {
                Storage::disk('public')->delete(
                    $user->image
                );
            }

            $imagePath = $request
                ->file('image')
                ->store('users', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */
        $data = [
            'role_id' => $validated['role_id'],

            'first_name' => $validated['first_name'],

            'last_name' => $validated['last_name'] ?? null,

            'username' => strtolower(
                $validated['username']
            ),

            'email' => strtolower(
                $validated['email']
            ),

            'phone_number' => $validated['phone_number'] ?? null,

            'image' => $imagePath,

            'status' => (int) $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make(
                $validated['password']
            );
        }

        $user->update($data);

        return redirect()
            ->route('users.users')
            ->with(
                'success',
                'User updated successfully.'
            );
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Only Admin Can Delete
        |--------------------------------------------------------------------------
        */
        if (!$this->isAdmin()) {
            abort(403, 'You are not authorized to delete users.');
        }

        /*
        |--------------------------------------------------------------------------
        | Cannot Delete Own Account
        |--------------------------------------------------------------------------
        */
        if (
            auth()->check()
            && auth()->id() === $user->id
        ) {
            return redirect()
                ->route('users.users')
                ->with(
                    'error',
                    'You cannot delete your own active account.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete User Image
        |--------------------------------------------------------------------------
        */
        if (
            $user->image
            && Storage::disk('public')->exists($user->image)
        ) {
            Storage::disk('public')->delete(
                $user->image
            );
        }

        $user->delete();

        return redirect()
            ->route('users.users')
            ->with(
                'success',
                'User deleted successfully.'
            );
    }

    /**
     * Quick toggle active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Non-admin can only toggle same-role users
        |--------------------------------------------------------------------------
        */
        if (
            !$this->isAdmin()
            && $user->role_id != auth()->user()->role_id
        ) {
            abort(
                403,
                'You are not authorized to change this user status.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Toggle Status
        |--------------------------------------------------------------------------
        */
        $user->status = (
            $user->status == User::STATUS_ACTIVE
        )
            ? User::STATUS_INACTIVE
            : User::STATUS_ACTIVE;

        $user->save();

        $statusLabel = (
            $user->status == User::STATUS_ACTIVE
        )
            ? 'Active'
            : 'Inactive';

        /*
        |--------------------------------------------------------------------------
        | Name
        |--------------------------------------------------------------------------
        */
        $userName = trim(
            $user->first_name . ' ' . $user->last_name
        );

        if (empty($userName)) {
            $userName = $user->username;
        }

        /*
        |--------------------------------------------------------------------------
        | JSON Response
        |--------------------------------------------------------------------------
        */
        if (
            request()->expectsJson()
            || request()->ajax()
        ) {
            return response()->json([
                'success' => true,

                'status' => $user->status,

                'status_label' => $statusLabel,

                'message' => "Operator '{$userName}' status changed to {$statusLabel}.",
            ]);
        }

        return back()->with(
            'success',
            "Operator '{$userName}' is now marked as {$statusLabel}."
        );
    }

    public function branches(Request $request)
    {
        // DELETE
        if ($request->isMethod('delete')) {

            $branch = Branch::findOrFail($request->id);

            if ($branch->logo) {
                Storage::disk('public')->delete($branch->logo);
            }

            if ($branch->favicon) {
                Storage::disk('public')->delete($branch->favicon);
            }

            $branch->delete();

            return redirect()->route('users.branches')
                ->with('success', 'Company deleted successfully!');
        }

        // SAVE / UPDATE
        if ($request->isMethod('post')) {

            $request->validate([
                'company_name' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:1024',
                'gst' => 'nullable|string|max:50',
                'pan' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'certificate_desc' => 'nullable|string',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'phone' => 'nullable|string|max:20',
            ]);

            // Find existing record or create new
            $branch = $request->id
                ? Branch::findOrFail($request->id)
                : new Branch();

            $branch->company_name = $request->company_name;
            $branch->gst = $request->gst;
            $branch->pan = $request->pan;
            $branch->address = $request->address;
            $branch->certificate_desc = $request->certificate_desc;
            $branch->email = $request->email;
            $branch->website = $request->website;
            $branch->phone = $request->phone;

            // Logo Upload
            if ($request->hasFile('logo')) {

                if ($branch->logo) {
                    Storage::disk('public')->delete($branch->logo);
                }

                $branch->logo = $request->file('logo')
                    ->store('branches/logos', 'public');
            }

            // Favicon Upload
            if ($request->hasFile('favicon')) {

                if ($branch->favicon) {
                    Storage::disk('public')->delete($branch->favicon);
                }

                $branch->favicon = $request->file('favicon')
                    ->store('branches/favicons', 'public');
            }

            $branch->save();

            return redirect()->route('users.branches')
                ->with('success', $request->id
                    ? 'Company updated successfully!'
                    : 'Company added successfully!');
        }

        // EDIT
        $editBranch = $request->id
            ? Branch::findOrFail($request->id)
            : null;

        // LIST
        $branches = Branch::latest()->get();

        return view('admin.users.branches', compact(
            'branches',
            'editBranch'
        ));
    }
}
