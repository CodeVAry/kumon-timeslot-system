<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::orderBy('role_name', 'asc')->get();

        if ($request->filled('role_id')) {
            $selectedRole = Role::find(
                $request->integer('role_id')
            );
        } else {
            $selectedRole = $roles->first();
        }

        $permissionsByModule = Permission::where(
            'is_active',
            true
        )
            ->orderBy('module', 'asc')
            ->orderBy('permission_key', 'asc')
            ->get()
            ->groupBy('module');

        $assignedPermissionIds = [];

        if ($selectedRole) {
            $assignedPermissionIds = $selectedRole
                ->permissions()
                ->pluck('permissions.id')
                ->toArray();
        }

        return view(
            'admin.permissions.index',
            compact(
                'roles',
                'selectedRole',
                'permissionsByModule',
                'assignedPermissionIds'
            )
        );
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->superAdmin) {
            return redirect()
                ->route('admin.permissions.index', [
                    'role_id' => $role->id,
                ])
                ->with(
                    'error',
                    'Super Admin permissions cannot be changed.'
                );
        }
        $request->validate([
            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $requestedPermissionIds = $request->input(
            'permissions',
            []
        );

        $activePermissionIds = Permission::where(
            'is_active',
            true
        )
            ->whereIn('id', $requestedPermissionIds)
            ->pluck('id')
            ->toArray();

        $role->permissions()->sync($activePermissionIds);

        return redirect()
            ->route('admin.permissions.index', [
                'role_id' => $role->id,
            ])
            ->with(
                'success',
                'Permissions updated successfully for ' .
                $role->role_name .
                '.'
            );
    }
}
