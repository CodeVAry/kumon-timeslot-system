@extends('layouts.admin')

@section('title', 'Roles')

@section('page-title', 'Role Management')

@php
    $breadcrumbs = [
        [
            'label' => 'Roles',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- Header --}}
        <div
            class="flex flex-col gap-4 border-b border-gray-200 p-6
                sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Role List
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Create, edit and delete system roles.
                </p>
            </div>

            <a href="{{ route('admin.roles.create') }}"
                class="inline-flex items-center justify-center rounded-lg
                   bg-blue-600 px-4 py-2.5 text-sm font-semibold
                   text-white hover:bg-blue-700">
                + Create Role
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold
                               uppercase tracking-wider text-gray-500">
                            ID
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold
                               uppercase tracking-wider text-gray-500">
                            Role Name
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold
                               uppercase tracking-wider text-gray-500">
                            Description
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold
                               uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold
                               uppercase tracking-wider text-gray-500">
                            Created
                        </th>

                        <th
                            class="px-6 py-3 text-right text-xs font-semibold
                               uppercase tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse ($roles as $role)
                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $role->id }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $role->role_name }}
                                </span>
                            </td>

                            <td class="max-w-md px-6 py-4 text-sm text-gray-600">
                                {{ $role->description ?: 'No description' }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($role->is_active)
                                    <span
                                        class="inline-flex rounded-full
                                             bg-green-100 px-3 py-1
                                             text-xs font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex rounded-full
                                             bg-red-100 px-3 py-1
                                             text-xs font-semibold text-red-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $role->created_at->format('d M Y') }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">

                                <div class="flex justify-end gap-2">

                                    @if ($role->is_system)
                                        <span
                                            class="inline-flex rounded-lg bg-purple-100
                 px-3 py-2 text-xs font-semibold text-purple-700">
                                            Protected
                                        </span>
                                    @else
                                        <a href="{{ route('admin.roles.edit', $role) }}"
                                            class="rounded-lg bg-amber-500 px-3 py-2
               text-xs font-semibold text-white hover:bg-amber-600">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                            onsubmit="return confirm(
            'Are you sure you want to delete this role?'
        );">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="rounded-lg bg-red-600 px-3 py-2
                   text-xs font-semibold text-white hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-semibold text-gray-600">
                                    No roles found.
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Click Create Role to add your first role.
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>

        {{-- Pagination --}}
        @if ($roles->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $roles->links() }}
            </div>
        @endif

    </div>

@endsection
