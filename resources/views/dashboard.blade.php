@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Welcome, {{ auth()->user()->name }}
        </h2>

        <p class="mt-1 text-gray-500">
            Welcome to the Kumon Timeslot Management System.
        </p>
    </div>

    {{-- User has no assigned role --}}
    @if (!auth()->user()->role_id)

        <div class="rounded-xl border border-amber-200
                    bg-amber-50 p-6 shadow-sm">

            <div class="flex items-start gap-4">

                <div class="flex h-11 w-11 shrink-0 items-center
                            justify-center rounded-full bg-amber-100
                            text-xl text-amber-700">
                    !
                </div>

                <div>
                    <h3 class="text-lg font-bold text-amber-800">
                        Waiting for role assignment
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-amber-700">
                        Your account has been registered successfully,
                        but no role has been assigned yet. The Super Admin
                        must assign a role before you can access system
                        modules.
                    </p>
                </div>

            </div>

        </div>

    {{-- Assigned role is inactive --}}
    @elseif (!auth()->user()->role || !auth()->user()->role->is_active)

        <div class="rounded-xl border border-red-200
                    bg-red-50 p-6 shadow-sm">

            <h3 class="text-lg font-bold text-red-800">
                Role unavailable
            </h3>

            <p class="mt-2 text-sm text-red-700">
                Your assigned role is inactive or unavailable.
                Contact the Super Admin.
            </p>

        </div>

    {{-- User has an active assigned role --}}
    @else

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">

            @if (auth()->user()->hasPermission('roles.view'))
                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">
                        Total Roles
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-800">
                        0
                    </p>
                </div>
            @endif

            @if (auth()->user()->hasPermission('users.view'))
                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">
                        System Users
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-800">
                        1
                    </p>
                </div>
            @endif

            @if (auth()->user()->hasPermission('students.view'))
                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">
                        Active Students
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-800">
                        0
                    </p>
                </div>
            @endif

            @if (auth()->user()->hasPermission('enrolments.view'))
                <div class="rounded-lg bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">
                        Enrolments
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-800">
                        0
                    </p>
                </div>
            @endif

        </div>

    @endif

@endsection
