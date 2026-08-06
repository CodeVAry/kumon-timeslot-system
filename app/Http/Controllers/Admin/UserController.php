<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display all users.
     */
    public function index(): View
    {
        $users = User::with('role')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the create user form.
     */
    public function create(): View
    {
        /*
         * Do not include the protected Super Admin role.
         * Only active normal roles can be assigned.
         */
        $roles = Role::where('superAdmin', false)
            ->where('is_active', true)
            ->orderBy('role_name', 'asc')
            ->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
         * Prevent someone from manually submitting
         * the protected Super Admin role ID.
         */
        if (!empty($validated['role_id'])) {
            $role = Role::where('id', $validated['role_id'])
                ->where('superAdmin', false)
                ->where('is_active', true)
                ->first();

            if (!$role) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'role_id' => 'The selected role is not available.',
                    ]);
            }
        }

        User::create([
            'name' => $validated['name'],

            'email' => strtolower(
                $validated['email']
            ),

            'phone' => $validated['phone'] ?? null,

            'role_id' => $validated['role_id'] ?? null,

            'is_active' => $validated['is_active'],

            /*
             * Admin-created internal users are verified
             * automatically.
             */
            'email_verified_at' => now(),

            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User created successfully.'
            );
    }

    /**
     * Display the edit user form.
     */
    public function edit(User $user): View|RedirectResponse
    {
        $user->load('role');

        /*
         * Protect the seeded Super Admin user.
         */
        if ($user->role?->superAdmin) {
            return redirect()
                ->route('admin.users.index')
                ->with(
                    'error',
                    'The Super Admin user cannot be edited here.'
                );
        }

        $roles = Role::where('superAdmin', false)
            ->where('is_active', true)
            ->orderBy('role_name', 'asc')
            ->get();

        return view(
            'admin.users.edit',
            compact('user', 'roles')
        );
    }

    /**
     * Update an existing user.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $user->load('role');

        if ($user->role?->superAdmin) {
            return redirect()
                ->route('admin.users.index')
                ->with(
                    'error',
                    'The Super Admin user cannot be updated here.'
                );
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
         * Prevent assigning the protected Super Admin role.
         */
        if (!empty($validated['role_id'])) {
            $role = Role::where('id', $validated['role_id'])
                ->where('superAdmin', false)
                ->where('is_active', true)
                ->first();

            if (!$role) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'role_id' => 'The selected role is not available.',
                    ]);
            }
        }

        /*
         * Prevent the logged-in user from deactivating
         * their own account.
         */
        if (
            auth()->id() === $user->id &&
            !$validated['is_active']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' =>
                        'You cannot deactivate your own account.',
                ]);
        }

        $userData = [
            'name' => $validated['name'],

            'email' => strtolower(
                $validated['email']
            ),

            'phone' => $validated['phone'] ?? null,

            'role_id' => $validated['role_id'] ?? null,

            'is_active' => $validated['is_active'],
        ];

        /*
         * Change password only when a new password
         * has been entered.
         */
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make(
                $validated['password']
            );
        }

        $user->update($userData);

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User updated successfully.'
            );
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->load('role');

        if ($user->role?->superAdmin) {
            return redirect()
                ->route('admin.users.index')
                ->with(
                    'error',
                    'The Super Admin user cannot be deleted.'
                );
        }

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with(
                    'error',
                    'You cannot delete your own account.'
                );
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User deleted successfully.'
            );
    }
}
