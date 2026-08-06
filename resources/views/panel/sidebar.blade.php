@php
    $loggedInUser = auth()->user();
@endphp

<aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-slate-900 text-white">

    {{-- System logo and name --}}
    <div class="flex h-16 items-center border-b border-slate-700 px-5">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">

            <div
                class="flex h-10 w-10 items-center justify-center
                        rounded-lg bg-blue-600 text-lg font-bold">
                K
            </div>

            <div>
                <h1 class="text-sm font-bold">
                    Kumon
                </h1>

                <p class="text-xs text-slate-400">
                    Timeslot System
                </p>
            </div>

        </a>

    </div>


    {{-- Sidebar navigation --}}
    <nav class="h-[calc(100vh-145px)] overflow-y-auto px-4 py-5">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="mb-1 flex items-center gap-3 rounded-lg
                  px-3 py-2.5 text-sm font-medium
                {{ request()->routeIs('dashboard')
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-2
                       2v6a2 2 0 01-2 2h-3m-4
                       0H7a2 2 0 01-2-2v-6m3
                       8v-6h4v6" />

            </svg>

            <span>Dashboard</span>

        </a>


        {{-- User and Access section --}}
        @if (
            $loggedInUser &&
                $loggedInUser->hasAnyPermission(['roles.view', 'permissions.view', 'users.view', 'audit_logs.view']))

            <p
                class="mb-2 mt-6 px-3 text-xs font-semibold
                      uppercase tracking-wider text-slate-500">
                User & Access
            </p>

            <div class="space-y-1">

                {{-- Roles --}}
                @if ($loggedInUser->hasPermission('roles.view'))
                    <a href="{{ route('admin.roles.index') }}"
                        class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->routeIs('admin.roles.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                                                                           hover:text-white' }}">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0
                                   00-5.356-1.857M17
                                   20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7
                                   20H2v-2a3 3 0
                                   015.356-1.857M7
                                   20v-2c0-.656.126-1.283.356-1.857m0
                                   0a5.002 5.002 0
                                   019.288 0M15 7a3 3 0
                                   11-6 0 3 3 0
                                   016 0z" />

                        </svg>

                        <span>Roles</span>

                    </a>
                @endif


                {{-- Permissions --}}
                @if ($loggedInUser->hasPermission('permissions.view'))
                    <a href="{{ route('admin.permissions.index') }}"
                        class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->routeIs('admin.permissions.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                                                                           hover:text-white' }}">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0
                                   002-2v-6a2 2 0
                                   00-2-2H6a2 2 0
                                   00-2 2v6a2 2 0
                                   002 2zm10-10V7a4 4 0
                                   00-8 0v4h8z" />

                        </svg>

                        <span>Permissions</span>

                    </a>
                @endif


                {{-- Users --}}
                @if ($loggedInUser->hasPermission('users.view'))
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->routeIs('admin.users.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                                                                           hover:text-white' }}">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9.004
                                   9.004 0 0112 15c2.21
                                   0 4.236.795 5.879
                                   2.115M15 11a3 3
                                   0 11-6 0 3 3
                                   0 016 0zm6 1a9
                                   9 0 11-18 0 9
                                   9 0 0118 0z" />

                        </svg>

                        <span>Users</span>

                    </a>
                @endif


                {{-- Audit Logs --}}
                @if ($loggedInUser->hasPermission('audit_logs.view'))
                    <a href="{{ url('/admin/audit-logs') }}"
                        class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->is('admin/audit-logs*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                                                                           hover:text-white' }}">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0
                                   00-2 2v12a2 2 0
                                   002 2h10a2 2 0
                                   002-2V7a2 2 0
                                   00-2-2h-2M9
                                   5a2 2 0 002
                                   2h2a2 2 0
                                   002-2M9 12h6m-6 4h6" />

                        </svg>

                        <span>Audit Logs</span>

                    </a>
                @endif

            </div>

        @endif


        {{-- Class Setup section --}}
        @if (
            $loggedInUser &&
                $loggedInUser->hasAnyPermission(['days.view', 'timeslots.view', 'sections.view', 'section_offerings.view']))

            <p
                class="mb-2 mt-6 px-3 text-xs font-semibold
                      uppercase tracking-wider text-slate-500">
                Class Setup
            </p>

            <div class="space-y-1">

                {{-- Days --}}
                {{-- @if ($loggedInUser->hasPermission('days.view'))

                    <a href="{{ url('/admin/days') }}"
                       class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->is('admin/days*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                   hover:text-white' }}">

                        <span class="h-2 w-2 rounded-full bg-current"></span>

                        <span>Days</span>

                    </a>

                @endif --}}


                {{-- Timeslots --}}
                {{-- @if ($loggedInUser->hasPermission('timeslots.view'))

                    <a href="{{ url('/admin/timeslots') }}"
                       class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->is('admin/timeslots*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                   hover:text-white' }}">

                        <span class="h-2 w-2 rounded-full bg-current"></span>

                        <span>Timeslots</span>

                    </a>

                @endif --}}


                {{-- Sections --}}
                @if ($loggedInUser && $loggedInUser->hasPermission('sections.view'))
                    <div class="space-y-1">

                        {{-- Sections --}}
                        <a href="{{ route('admin.sections.index') }}"
                            class="flex items-center gap-3
                   rounded-lg px-3 py-2.5
                   text-sm font-medium
                {{ request()->routeIs('admin.sections.*')
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-300
                                                       hover:bg-slate-800
                                                       hover:text-white' }}">

                            <span class="h-2 w-2 rounded-full
                         bg-current">
                            </span>

                            <span>Sections</span>

                        </a>

                    </div>
                @endif


                {{-- Section Offerings --}}
                @if ($loggedInUser->hasPermission('section_offerings.view'))
                    <a href="{{ route('admin.section-offerings.index') }}"
                        class="flex items-center gap-3
               rounded-lg px-3 py-2.5
               text-sm font-medium
            {{ request()->routeIs('admin.section-offerings.*')
                ? 'bg-blue-600 text-white'
                : 'text-slate-300
                               hover:bg-slate-800
                               hover:text-white' }}">
                        <span class="h-2 w-2 rounded-full
                     bg-current">
                        </span>

                        <span>Section Offerings</span>
                    </a>
                @endif

            </div>

        @endif


        {{-- Student Setup --}}
        @if ($loggedInUser && $loggedInUser->hasAnyPermission(['student_statuses.view', 'students.view']))

            <p
                class="mb-2 mt-6 px-3 text-xs
              font-semibold uppercase tracking-wider
              text-slate-500">
                Student Setup
            </p>

            <div class="space-y-1">

                @if ($loggedInUser->hasPermission('student_statuses.view'))
                    <a href="{{ route('admin.student-statuses.index') }}"
                        class="flex items-center gap-3 rounded-lg
                       px-3 py-2.5 text-sm font-medium
                    {{ request()->routeIs('admin.student-statuses.*')
                        ? 'bg-blue-600 text-white'
                        : 'text-slate-300 hover:bg-slate-800
                                               hover:text-white' }}">
                        <span class="h-2 w-2 rounded-full bg-current">
                        </span>

                        <span>Student Statuses</span>
                    </a>
                @endif

            </div>

        @endif

        {{-- Enrolment section --}}
        @if ($loggedInUser && $loggedInUser->hasAnyPermission(['enrolments.view', 'enrolment_statuses.view']))

            <p
                class="mb-2 mt-6 px-3 text-xs font-semibold
                      uppercase tracking-wider text-slate-500">
                Enrolment
            </p>

            <div class="space-y-1">

                {{-- Enrolments --}}
                @if ($loggedInUser->hasPermission('enrolments.view'))
                    <a href="{{ url('/admin/enrolments') }}"
                        class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->is('admin/enrolments*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                                                                           hover:text-white' }}">

                        <span class="h-2 w-2 rounded-full bg-current"></span>

                        <span>Enrolments</span>

                    </a>
                @endif


                {{-- Enrolment Statuses --}}
                @if ($loggedInUser->hasPermission('enrolment_statuses.view'))
                    <a href="{{ url('/admin/enrolment-statuses') }}"
                        class="flex items-center gap-3 rounded-lg
                              px-3 py-2.5 text-sm font-medium
                            {{ request()->is('admin/enrolment-statuses*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800
                                                                                           hover:text-white' }}">

                        <span class="h-2 w-2 rounded-full bg-current"></span>

                        <span>Enrolment Statuses</span>

                    </a>
                @endif

            </div>

        @endif

    </nav>


    {{-- Logged-in user and logout --}}
    <div class="absolute bottom-0 left-0 w-full
                border-t border-slate-700 bg-slate-900 p-4">

        <div class="mb-3 flex items-center gap-3">

            <div
                class="flex h-9 w-9 items-center justify-center
                        rounded-full bg-blue-600 text-sm font-bold">

                {{ strtoupper(substr($loggedInUser->name ?? 'A', 0, 1)) }}

            </div>

            <div class="min-w-0">

                <p class="truncate text-sm font-semibold">
                    {{ $loggedInUser->name ?? 'User' }}
                </p>

                <p class="truncate text-xs text-slate-400">
                    {{ $loggedInUser->email ?? '' }}
                </p>

                @if ($loggedInUser && $loggedInUser->role)
                    <p class="truncate text-xs text-blue-300">
                        {{ $loggedInUser->role->role_name }}
                    </p>
                @else
                    <p class="truncate text-xs text-amber-300">
                        Role not assigned
                    </p>
                @endif

            </div>

        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="flex w-full items-center gap-3 rounded-lg
                       px-3 py-2 text-sm font-medium text-slate-300
                       hover:bg-red-600 hover:text-white">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0
                           0l-4-4m4 4H7m6
                           4v1a3 3 0
                           01-3 3H6a3 3 0
                           01-3-3V7a3 3 0
                           013-3h4a3 3 0
                           013 3v1" />

                </svg>

                <span>Logout</span>

            </button>

        </form>

    </div>

</aside>
