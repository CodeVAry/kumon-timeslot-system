<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('id', 'asc')->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:100',
                'unique:roles,role_name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        Role::create($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->superAdmin) {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'The Super Admin role cannot be edited.'
                );
        }
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->superAdmin) {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'The Super Admin role cannot be updated.'
                );
        }
        $validated = $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:100',
                'unique:roles,role_name,' . $role->id,
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $role->update($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->superAdmin) {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'The Super Admin role cannot be deleted.'
                );
        }

        if ($role->users()->exists()) {
            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'error',
                    'This role is assigned to users and cannot be deleted.'
                );
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
