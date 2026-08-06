@extends('layouts.admin')

@section('title', 'Users')

@section('page-title', 'User Management')

@php
    $breadcrumbs = [
        [
            'label' => 'Users',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    {{-- Success message --}}
    @if (session('success'))
        <div class="mb-5 rounded-lg border border-green-200
                    bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error message --}}
    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200
                bg-white shadow-sm">

        {{-- Page header --}}
        <div class="flex flex-col gap-4 border-b
                    border-gray-200 p-6
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    System Users
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage user accounts, roles and status.
                </p>
            </div>

            @if (auth()->user()->hasPermission('users.create'))
                <a
                    href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center justify-center
                           rounded-lg bg-blue-600 px-4 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700"
                >
                    + Create User
                </a>
            @endif

        </div>

        {{-- Users table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            ID
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            User
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Phone
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Role
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Created
                        </th>

                        <th class="px-6 py-3 text-right text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse ($users as $user)

                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-6 py-4
                                       text-sm text-gray-500">
                                {{ $user->id }}
                            </td>

                            <td class="px-6 py-4">

                                <p class="text-sm font-semibold
                                          text-gray-800">
                                    {{ $user->name }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $user->email }}
                                </p>

                                @if ($user->id === auth()->id())
                                    <span class="mt-1 inline-flex
                                                 rounded-full bg-gray-100
                                                 px-2 py-0.5 text-xs
                                                 font-semibold text-gray-600">
                                        Current User
                                    </span>
                                @endif

                            </td>

                            <td class="whitespace-nowrap px-6 py-4
                                       text-sm text-gray-600">
                                {{ $user->phone ?: 'Not provided' }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($user->role?->superAdmin)

                                    <span class="inline-flex rounded-full
                                                 bg-purple-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-purple-700">
                                        Super Admin
                                    </span>

                                @elseif ($user->role)

                                    <span class="inline-flex rounded-full
                                                 bg-blue-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-blue-700">
                                        {{ $user->role->role_name }}
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full
                                                 bg-amber-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-amber-700">
                                        Not Assigned
                                    </span>

                                @endif

                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($user->is_active)

                                    <span class="inline-flex rounded-full
                                                 bg-green-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-green-700">
                                        Active
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full
                                                 bg-red-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-red-700">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td class="whitespace-nowrap px-6 py-4
                                       text-sm text-gray-500">
                                {{ $user->created_at?->format('d M Y') }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4
                                       text-right">

                                @if ($user->role?->superAdmin)

                                    <span class="inline-flex rounded-lg
                                                 bg-purple-100 px-3 py-2
                                                 text-xs font-semibold
                                                 text-purple-700">
                                        Protected
                                    </span>

                                @else

                                    <div class="flex justify-end gap-2">

                                        @if (auth()->user()->hasPermission('users.edit'))
                                            <a
                                                href="{{ route(
                                                    'admin.users.edit',
                                                    $user
                                                ) }}"
                                                class="rounded-lg
                                                       bg-amber-500
                                                       px-3 py-2 text-xs
                                                       font-semibold
                                                       text-white
                                                       hover:bg-amber-600"
                                            >
                                                Edit
                                            </a>
                                        @endif

                                        @if (
                                            auth()->user()->hasPermission('users.delete') &&
                                            auth()->id() !== $user->id
                                        )
                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.users.destroy',
                                                    $user
                                                ) }}"
                                                onsubmit="return confirm(
                                                    'Are you sure you want to delete this user?'
                                                );"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="rounded-lg
                                                           bg-red-600
                                                           px-3 py-2
                                                           text-xs
                                                           font-semibold
                                                           text-white
                                                           hover:bg-red-700"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        @endif

                                    </div>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="7"
                                class="px-6 py-12 text-center
                                       text-sm text-gray-500"
                            >
                                No users found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($users->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif

    </div>

@endsection
