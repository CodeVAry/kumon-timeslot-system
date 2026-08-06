@extends('layouts.admin')

@section('title', 'Permissions')

@section('page-title', 'Permission Management')

@php
    $breadcrumbs = [
        [
            'label' => 'Permissions',
            'url' => null,
        ],
    ];

    $actions = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'print' => 'Print',
    ];
@endphp

@section('content')

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- Heading --}}
        <div class="border-b border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-800">
                Role Permissions
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Select a role and choose the access it should receive.
            </p>
        </div>

        {{-- Role selection --}}
        <div class="border-b border-gray-200 bg-gray-50 p-6">

            <form method="GET" action="{{ route('admin.permissions.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="w-full max-w-md">
                    <label for="role_id" class="mb-2 block text-sm font-semibold text-gray-700">
                        Select Role
                    </label>

                    <select name="role_id" id="role_id"
                        class="w-full rounded-lg border-gray-300
                           shadow-sm focus:border-blue-500
                           focus:ring-blue-500"
                        onchange="this.form.submit()">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected($selectedRole && $selectedRole->id === $role->id)>
                                {{ $role->role_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

        </div>

        @if ($selectedRole)

            <form method="POST"
                action="{{ route('admin.permissions.update', $selectedRole) }}">
                @csrf
                @method('PUT')

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs
                                       font-semibold uppercase
                                       tracking-wider text-gray-500">
                                    Module
                                </th>

                                <th
                                    class="px-4 py-3 text-center text-xs
                                       font-semibold uppercase
                                       tracking-wider text-gray-500">
                                    Full Access
                                </th>

                                @foreach ($actions as $actionLabel)
                                    <th
                                        class="px-4 py-3 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                        {{ $actionLabel }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @foreach ($permissionsByModule as $module => $modulePermissions)
                                @php
                                    $permissionsByAction = $modulePermissions->keyBy(function ($permission) {
                                        return str($permission->permission_key)->after('.')->toString();
                                    });
                                @endphp

                                <tr class="permission-row hover:bg-gray-50">

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="font-semibold text-gray-800">
                                            {{ $module }}
                                        </p>
                                    </td>

                                    {{-- Full Access --}}
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox"
                                            class="full-access-checkbox
                                               rounded border-gray-300
                                               text-blue-600
                                               focus:ring-blue-500">
                                    </td>

                                    {{-- Individual permissions --}}
                                    @foreach ($actions as $action => $label)
                                        <td class="px-4 py-4 text-center">

                                            @if ($permissionsByAction->has($action))
                                                @php
                                                    $permission = $permissionsByAction->get($action);
                                                @endphp

                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                    class="permission-checkbox
                                                       rounded
                                                       border-gray-300
                                                       text-blue-600
                                                       focus:ring-blue-500"
                                                    @checked(in_array($permission->id, $assignedPermissionIds))>
                                            @else
                                                <span class="text-gray-300">
                                                    —
                                                </span>
                                            @endif

                                        </td>
                                    @endforeach

                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>

                <div
                    class="flex items-center justify-between
                        border-t border-gray-200 bg-gray-50 p-6">

                    <p class="text-sm text-gray-500">
                        Editing access for:
                        <span class="font-semibold text-gray-700">
                            {{ $selectedRole->role_name }}
                        </span>
                    </p>

                    @if ($selectedRole->superAdmin)
                        <span
                            class="rounded-lg bg-purple-100 px-4 py-2.5
                 text-sm font-semibold text-purple-700">
                            Protected Full Access
                        </span>
                    @else
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5
               text-sm font-semibold text-white hover:bg-blue-700">
                            Save Permissions
                        </button>
                    @endif

                </div>

            </form>
        @else
            <div class="p-12 text-center">
                <p class="font-semibold text-gray-700">
                    No roles are available.
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    Create a role before assigning permissions.
                </p>
            </div>

        @endif

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.permission-row');

            rows.forEach(function(row) {
                const fullAccess =
                    row.querySelector('.full-access-checkbox');

                const permissionCheckboxes =
                    row.querySelectorAll('.permission-checkbox');

                function updateFullAccess() {
                    if (permissionCheckboxes.length === 0) {
                        fullAccess.checked = false;
                        return;
                    }

                    fullAccess.checked = Array
                        .from(permissionCheckboxes)
                        .every(function(checkbox) {
                            return checkbox.checked;
                        });
                }

                fullAccess.addEventListener('change', function() {
                    permissionCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = fullAccess.checked;
                    });
                });

                permissionCheckboxes.forEach(function(checkbox) {
                    checkbox.addEventListener(
                        'change',
                        updateFullAccess
                    );
                });

                updateFullAccess();
            });
        });
    </script>
@endpush
