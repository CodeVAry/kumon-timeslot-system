@php
    $loggedInUser = auth()->user();

    $auditNotificationCount =
        session(
            'audit_notification_count',
            0
        );
@endphp


<aside
    class="fixed left-0 top-0 z-40
           h-screen w-64
           bg-slate-900 text-white"
>

    {{-- =========================================================
        SYSTEM LOGO
    ========================================================== --}}

    <div
        class="flex h-16
               items-center
               border-b
               border-slate-700
               px-5"
    >

        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-3"
        >

            <div
                class="flex h-10 w-10
                       items-center
                       justify-center
                       rounded-lg
                       bg-blue-600
                       text-lg
                       font-bold"
            >
                K
            </div>


            <div>

                <h1
                    class="text-sm
                           font-bold"
                >
                    Kumon
                </h1>


                <p
                    class="text-xs
                           text-slate-400"
                >
                    Timeslot System
                </p>

            </div>

        </a>

    </div>



    {{-- =========================================================
        SIDEBAR NAVIGATION
    ========================================================== --}}

    <nav
        class="h-[calc(100vh-145px)]
               overflow-y-auto
               px-4 py-5"
    >


        {{-- =====================================================
            DASHBOARD
        ====================================================== --}}

        <a
            href="{{ route('dashboard') }}"
            class="mb-1
                   flex items-center
                   gap-3
                   rounded-lg
                   px-3 py-2.5
                   text-sm
                   font-medium
                {{ request()->routeIs('dashboard')
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
        >

            <svg
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7m-2
                       2v6a2 2 0 01-2 2h-3m-4
                       0H7a2 2 0 01-2-2v-6m3
                       8v-6h4v6"
                />

            </svg>


            <span>
                Dashboard
            </span>

        </a>



        {{-- =====================================================
            USER & ACCESS
        ====================================================== --}}

        @if (
            $loggedInUser
            &&
            $loggedInUser->hasAnyPermission([
                'roles.view',
                'permissions.view',
                'users.view',
                'audit_logs.view',
            ])
        )

            <p
                class="mb-2 mt-6
                       px-3
                       text-xs
                       font-semibold
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                User & Access
            </p>


            <div class="space-y-1">


                {{-- =============================================
                    ROLES
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'roles.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.roles.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.roles.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0
                                   00-5.356-1.857M17
                                   20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7
                                   20H2v-2a3 3 0
                                   015.356-1.857M7
                                   20v-2c0-.656.126-1.283.356-1.857m0
                                   0a5.002 5.002 0
                                   019.288 0M15 7a3 3 0
                                   11-6 0 3 3 0
                                   016 0z"
                            />

                        </svg>


                        <span>
                            Roles
                        </span>

                    </a>

                @endif



                {{-- =============================================
                    PERMISSIONS
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'permissions.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.permissions.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.permissions.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0
                                   002-2v-6a2 2 0
                                   00-2-2H6a2 2 0
                                   00-2 2v6a2 2 0
                                   002 2zm10-10V7a4 4 0
                                   00-8 0v4h8z"
                            />

                        </svg>


                        <span>
                            Permissions
                        </span>

                    </a>

                @endif



                {{-- =============================================
                    USERS
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'users.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.users.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.users.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5.121 17.804A9.004
                                   9.004 0 0112 15c2.21
                                   0 4.236.795 5.879
                                   2.115M15 11a3 3
                                   0 11-6 0 3 3
                                   0 016 0zm6 1a9
                                   9 0 11-18 0 9
                                   9 0 0118 0z"
                            />

                        </svg>


                        <span>
                            Users
                        </span>

                    </a>

                @endif



                {{-- =============================================
                    AUDIT LOGS
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'audit_logs.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.audit-logs.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.audit-logs.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <svg
                            class="h-5 w-5
                                   shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0
                                   00-2 2v12a2 2 0
                                   002 2h10a2 2 0
                                   002-2V7a2 2 0
                                   00-2-2h-2M9
                                   5a2 2 0 002
                                   2h2a2 2 0
                                   002-2M9 12h6m-6 4h6"
                            />

                        </svg>


                        <span>
                            Audit Logs
                        </span>


                        {{-- =====================================
                            AUDIT NOTIFICATION BADGE
                        ====================================== --}}

                        @if (
                            $auditNotificationCount
                            >
                            0
                        )

                            <span
                                class="ml-auto
                                       inline-flex
                                       min-w-5
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-red-500
                                       px-1.5
                                       py-0.5
                                       text-[10px]
                                       font-bold
                                       leading-none
                                       text-white
                                       shadow-sm"
                            >
                                {{
                                    $auditNotificationCount
                                    >
                                    99
                                        ? '99+'
                                        : $auditNotificationCount
                                }}
                            </span>

                        @endif

                    </a>

                @endif

            </div>

        @endif



        {{-- =====================================================
            CLASS SETUP
        ====================================================== --}}

        @if (
            $loggedInUser
            &&
            $loggedInUser->hasAnyPermission([
                'days.view',
                'timeslots.view',
                'sections.view',
                'section_offerings.view',
            ])
        )

            <p
                class="mb-2 mt-6
                       px-3
                       text-xs
                       font-semibold
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                Class Setup
            </p>


            <div class="space-y-1">


                {{-- =============================================
                    SECTIONS
                ============================================== --}}

                @if (
                    $loggedInUser
                    &&
                    $loggedInUser
                        ->hasPermission(
                            'sections.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.sections.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.sections.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <span
                            class="h-2 w-2
                                   rounded-full
                                   bg-current"
                        ></span>


                        <span>
                            Sections
                        </span>

                    </a>

                @endif



                {{-- =============================================
                    SECTION OFFERINGS
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'section_offerings.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.section-offerings.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.section-offerings.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <span
                            class="h-2 w-2
                                   rounded-full
                                   bg-current"
                        ></span>


                        <span>
                            Section Offerings
                        </span>

                    </a>

                @endif

            </div>

        @endif



        {{-- =====================================================
            STUDENT SETUP
        ====================================================== --}}

        @if (
            $loggedInUser
            &&
            $loggedInUser->hasAnyPermission([
                'student_statuses.view',
                'students.view',
            ])
        )

            <p
                class="mb-2 mt-6
                       px-3
                       text-xs
                       font-semibold
                       uppercase
                       tracking-wider
                       text-slate-500"
            >
                Student Setup
            </p>


            <div class="space-y-1">


                {{-- =============================================
                    STUDENT STATUSES
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'student_statuses.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.student-statuses.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.student-statuses.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <span
                            class="h-2 w-2
                                   rounded-full
                                   bg-current"
                        ></span>


                        <span>
                            Student Statuses
                        </span>

                    </a>

                @endif



                {{-- =============================================
                    STUDENT LIST
                ============================================== --}}

                @if (
                    $loggedInUser
                        ->hasPermission(
                            'students.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.students.index'
                        ) }}"
                        class="flex
                               items-center
                               gap-3
                               rounded-lg
                               px-3 py-2.5
                               text-sm
                               font-medium
                            {{ request()->routeIs('admin.students.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0
                                   2.625.372 9.337 9.337 0 0 0
                                   4.121-.952 4.125 4.125 0 0 0
                                   -7.533-2.493M15 19.128v-.003
                                   c0-1.113-.285-2.16-.786-3.07M15
                                   19.128v.106A12.318 12.318 0 0 1
                                   8.624 21c-2.331 0-4.512-.645
                                   -6.374-1.766l-.002-.109a6.375
                                   6.375 0 0 1 11.964-3.07M12
                                   6.375a3.375 3.375 0 1 1
                                   -6.75 0 3.375 3.375 0 0 1
                                   6.75 0Zm8.25 2.25a2.625
                                   2.625 0 1 1-5.25 0
                                   2.625 2.625 0 0 1 5.25 0Z"
                            />

                        </svg>


                        <span>
                            Student List
                        </span>

                    </a>

                @endif

            </div>

        @endif



        {{-- =====================================================
            SCHEDULE
        ====================================================== --}}

        @if (
            $loggedInUser
            &&
            $loggedInUser
                ->hasPermission(
                    'section_offerings.view'
                )
        )

            <a
                href="{{ route(
                    'admin.schedule.index'
                ) }}"
                class="mt-1
                       flex
                       items-center
                       gap-3
                       rounded-xl
                       px-4 py-3
                       text-sm
                       font-medium
                    {{ request()->routeIs('admin.schedule.*')
                        ? 'bg-blue-600 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="h-5 w-5"
                >

                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="16"
                        rx="2"
                    />

                    <path
                        d="M16 3v4
                           M8 3v4
                           M3 10h18"
                    />

                </svg>


                <span>
                    Schedule
                </span>

            </a>

        @endif



        {{-- =====================================================
            ATTENDANCE
        ====================================================== --}}

        @if (
            Route::has(
                'admin.attendance.index'
            )
            &&
            $loggedInUser
            &&
            $loggedInUser
                ->hasPermission(
                    'enrolments.view'
                )
        )

            <a
                href="{{ route(
                    'admin.attendance.index'
                ) }}"
                class="flex
                       items-center
                       gap-3
                       rounded-xl
                       px-4 py-3
                       text-sm
                       font-medium
                    {{ request()->routeIs('admin.attendance.*')
                        ? 'bg-blue-600 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="h-5 w-5"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6.75 3v2.25
                           M17.25 3v2.25
                           M3.75 9.75h16.5
                           M5.25 5.25h13.5
                           A1.5 1.5 0 0 1
                           20.25 6.75v12
                           a1.5 1.5 0 0 1
                           -1.5 1.5H5.25
                           a1.5 1.5 0 0 1
                           -1.5-1.5v-12
                           a1.5 1.5 0 0 1
                           1.5-1.5Z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m9 15 2 2 4-4"
                    />

                </svg>


                <span>
                    Attendance
                </span>

            </a>

        @endif



        {{-- =====================================================
            LEAVE MANAGEMENT
        ====================================================== --}}

        @if (
            $loggedInUser
            &&
            $loggedInUser
                ->hasPermission(
                    'leave.view'
                )
        )

            <a
                href="{{ route(
                    'admin.leave.index'
                ) }}"
                class="flex
                       items-center
                       gap-3
                       rounded-xl
                       px-4 py-3
                       text-sm
                       font-medium
                       transition
                    {{ request()->routeIs('admin.leave.*')
                        ? 'bg-blue-600 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="h-5 w-5"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8.25 6.75V5.25
                           A2.25 2.25 0 0 1 10.5 3h3
                           a2.25 2.25 0 0 1 2.25 2.25v1.5
                           M3.75 8.25h16.5
                           v10.5A2.25 2.25 0 0 1 18 21H6
                           a2.25 2.25 0 0 1-2.25-2.25V8.25Z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 11.25v4.5
                           M9.75 13.5h4.5"
                    />

                </svg>


                <span>
                    Leave Management
                </span>

            </a>

        @endif



        {{-- =====================================================
            WISHLIST
        ====================================================== --}}

        @if (
            $loggedInUser
            &&
            $loggedInUser
                ->hasPermission(
                    'wishlist.view'
                )
        )

            <a
                href="{{ route(
                    'admin.wishlist.index'
                ) }}"
                class="flex
                       items-center
                       gap-3
                       rounded-xl
                       px-4 py-3
                       text-sm
                       font-medium
                       transition
                    {{ request()->routeIs('admin.wishlist.*')
                        ? 'bg-blue-600 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="h-5 w-5"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 21s-7-4.35-7-10
                           a4 4 0 0 1 7-2.65
                           A4 4 0 0 1 19 11
                           c0 5.65-7 10-7 10Z"
                    />

                </svg>


                <span>
                    Wishlist
                </span>

            </a>

        @endif

    </nav>



    {{-- =========================================================
        LOGGED-IN USER / LOGOUT
    ========================================================== --}}

    <div
        class="absolute
               bottom-0
               left-0
               w-full
               border-t
               border-slate-700
               bg-slate-900
               p-4"
    >

        <div
            class="mb-3
                   flex
                   items-center
                   gap-3"
        >

            <div
                class="flex
                       h-9 w-9
                       items-center
                       justify-center
                       rounded-full
                       bg-blue-600
                       text-sm
                       font-bold"
            >
                {{
                    strtoupper(
                        substr(
                            $loggedInUser->name
                                ?? 'A',
                            0,
                            1
                        )
                    )
                }}
            </div>


            <div class="min-w-0">

                <p
                    class="truncate
                           text-sm
                           font-semibold"
                >
                    {{
                        $loggedInUser->name
                            ?? 'User'
                    }}
                </p>


                <p
                    class="truncate
                           text-xs
                           text-slate-400"
                >
                    {{
                        $loggedInUser->email
                            ?? ''
                    }}
                </p>


                @if (
                    $loggedInUser
                    &&
                    $loggedInUser->role
                )

                    <p
                        class="truncate
                               text-xs
                               text-blue-300"
                    >
                        {{
                            $loggedInUser
                                ->role
                                ->role_name
                        }}
                    </p>

                @else

                    <p
                        class="truncate
                               text-xs
                               text-amber-300"
                    >
                        Role not assigned
                    </p>

                @endif

            </div>

        </div>



        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf


            <button
                type="submit"
                class="flex
                       w-full
                       items-center
                       gap-3
                       rounded-lg
                       px-3 py-2
                       text-sm
                       font-medium
                       text-slate-300
                       hover:bg-red-600
                       hover:text-white"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0
                           0l-4-4m4 4H7m6
                           4v1a3 3 0
                           01-3 3H6a3 3 0
                           01-3-3V7a3 3 0
                           013-3h4a3 3 0
                           013 3v1"
                    />

                </svg>


                <span>
                    Logout
                </span>

            </button>

        </form>

    </div>

</aside>
