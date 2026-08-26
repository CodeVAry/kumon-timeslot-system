@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')


@php

    /*
    |--------------------------------------------------------------------------
    | Dashboard Defaults
    |--------------------------------------------------------------------------
    */

    $dashboardDate =
        $dashboardDate
        ?? now();

    $studentsToday =
        $studentsToday
        ?? 0;

    $currentlyAttending =
        $currentlyAttending
        ?? 0;

    $absentToday =
        $absentToday
        ?? 0;

    $wishlistCount =
        $wishlistCount
        ?? 0;

    $currentClassTime =
        $currentClassTime
        ?? null;

    $currentClassStudents =
        $currentClassStudents
        ?? 0;

    $nextClassTime =
        $nextClassTime
        ?? null;

    $nextClassStudents =
        $nextClassStudents
        ?? 0;

    $todayClasses =
        $todayClasses
        ?? collect();

    $absentStudents =
        $absentStudents
        ?? collect();

    $adminReminders =
        $adminReminders
        ?? collect();

@endphp



@section('content')

<div
    class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8"
>


    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}

    <div
        class="flex
               flex-col
               gap-5
               xl:flex-row
               xl:items-start
               xl:justify-between"
    >

        {{-- Left --}}
        <div>

            <h1
                class="text-3xl
                       font-bold
                       tracking-tight
                       text-slate-900
                       sm:text-4xl"
            >
                Dashboard
            </h1>


            <p
                class="mt-2
                       text-base
                       text-slate-500
                       sm:text-lg"
            >

                {{ $dashboardDate->format('l, j F Y') }}

                <span class="mx-1">
                    ·
                </span>

                <span id="dashboardHeaderTime">
                    {{ $dashboardDate->format('g:i A') }}
                </span>

            </p>

        </div>



        {{-- Right --}}
        <div
            class="flex
                   flex-wrap
                   items-center
                   gap-3"
        >

            {{-- Current Time --}}
            <div
                class="inline-flex
                       h-11
                       min-w-[140px]
                       items-center
                       justify-center
                       rounded-full
                       bg-cyan-500
                       px-6
                       text-sm
                       font-semibold
                       text-white
                       shadow-sm"
            >

                <span class="mr-1">
                    Now ·
                </span>

                <span id="dashboardCurrentTime">
                    {{ $dashboardDate->format('g:i A') }}
                </span>

            </div>



            {{-- Next Class --}}
            @if ($nextClassTime)

                <div
                    class="inline-flex
                           h-11
                           min-w-[190px]
                           items-center
                           justify-center
                           rounded-full
                           border
                           border-cyan-300
                           bg-white
                           px-7
                           text-sm
                           font-semibold
                           text-cyan-800
                           shadow-sm"
                >

                    <span class="text-slate-500">
                        Upcoming Class&nbsp;·&nbsp;
                    </span>

                    <span
                        class="font-bold
                               text-cyan-800"
                    >
                        {{ $nextClassTime }}
                    </span>

                </div>

            @else

                <div
                    class="inline-flex
                           h-11
                           min-w-[190px]
                           items-center
                           justify-center
                           rounded-full
                           border
                           border-slate-200
                           bg-white
                           px-7
                           text-sm
                           font-semibold
                           text-slate-500
                           shadow-sm"
                >
                    No upcoming class
                </div>

            @endif

        </div>

    </div>



    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}

    <div
        class="mt-8
               grid
               gap-5
               md:grid-cols-2
               xl:grid-cols-4"
    >


        {{-- Students Today --}}
        <div
            class="rounded-[24px]
                   border
                   border-cyan-100
                   bg-cyan-100/70
                   p-6"
        >

            <div
                class="flex
                       items-start
                       justify-between
                       gap-4"
            >

                <div>

                    <p
                        class="text-sm
                               font-semibold
                               text-slate-600"
                    >
                        Students today
                    </p>


                    <p
                        class="mt-4
                               text-4xl
                               font-bold
                               text-cyan-800"
                    >
                        {{ number_format($studentsToday) }}
                    </p>


                    <p
                        class="mt-3
                               text-sm
                               text-slate-500"
                    >
                        Across all confirmed classes
                    </p>

                </div>


                <div
                    class="flex
                           h-12 w-12
                           items-center
                           justify-center
                           rounded-2xl
                           bg-white/80
                           text-cyan-700"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-6 w-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0
                               3.741-.479 3 3 0 0 0-4.682-2.72
                               m.94 3.198.001.031c0 .225-.012.447
                               -.037.666A11.944 11.944 0 0 1
                               12 21c-2.17 0-4.206-.576-5.963-1.584
                               A6.062 6.062 0 0 1 6 18.719
                               m12 0a5.971 5.971 0 0 0-.941-3.197
                               m0 0A5.995 5.995 0 0 0 12
                               12.75a5.995 5.995 0 0 0-5.058
                               2.772m0 0a3 3 0 0 0-4.681
                               2.72 8.986 8.986 0 0 0 3.74.477
                               m.94-3.197a5.971 5.971 0 0 0-.94
                               3.197M15 6.75a3 3 0 1 1-6 0
                               3 3 0 0 1 6 0Zm6 3a2.25 2.25
                               0 1 1-4.5 0 2.25 2.25 0 0 1
                               4.5 0Zm-13.5 0a2.25 2.25 0 1 1
                               -4.5 0 2.25 2.25 0 0 1 4.5 0Z"
                        />
                    </svg>

                </div>

            </div>

        </div>



        {{-- Currently Scheduled --}}
        <div
            class="rounded-[24px]
                   border
                   border-green-100
                   bg-green-50
                   p-6"
        >

            <div
                class="flex
                       items-start
                       justify-between
                       gap-4"
            >

                <div>

                    <p
                        class="text-sm
                               font-semibold
                               text-slate-600"
                    >
                        Currently scheduled
                    </p>


                    <p
                        class="mt-4
                               text-4xl
                               font-bold
                               text-green-600"
                    >
                        {{ number_format($currentlyAttending) }}
                    </p>


                    <p
                        class="mt-3
                               text-sm
                               text-slate-500"
                    >

                        @if ($currentClassTime)

                            {{ $currentClassTime }}
                            class

                        @else

                            No class currently running

                        @endif

                    </p>

                </div>


                <div
                    class="flex
                           h-12 w-12
                           items-center
                           justify-center
                           rounded-2xl
                           bg-white/80
                           text-green-600"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-6 w-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m4.5 12.75 6 6 9-13.5"
                        />
                    </svg>

                </div>

            </div>

        </div>



        {{-- Upcoming Class --}}
        <div
            class="rounded-[24px]
                   border
                   border-amber-100
                   bg-amber-50
                   p-6"
        >

            <div
                class="flex
                       items-start
                       justify-between
                       gap-4"
            >

                <div>

                    <p
                        class="text-sm
                               font-semibold
                               text-slate-600"
                    >
                        Upcoming class
                    </p>


                    <p
                        class="mt-4
                               text-4xl
                               font-bold
                               text-amber-600"
                    >
                        {{ $nextClassTime ?? '—' }}
                    </p>


                    <p
                        class="mt-3
                               text-sm
                               text-slate-500"
                    >

                        @if ($nextClassTime)

                            {{ number_format($nextClassStudents) }}

                            {{
                                $nextClassStudents === 1
                                    ? 'student'
                                    : 'students'
                            }}

                            expected

                        @else

                            No upcoming class today

                        @endif

                    </p>

                </div>


                <div
                    class="flex
                           h-12 w-12
                           items-center
                           justify-center
                           rounded-2xl
                           bg-white/80
                           text-amber-600"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-6 w-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9
                               0 1 1-18 0 9 9 0 0 1
                               18 0Z"
                        />
                    </svg>

                </div>

            </div>

        </div>



        {{-- Absent Today --}}
        <div
            class="rounded-[24px]
                   border
                   border-red-100
                   bg-red-50
                   p-6"
        >

            <div
                class="flex
                       items-start
                       justify-between
                       gap-4"
            >

                <div>

                    <p
                        class="text-sm
                               font-semibold
                               text-slate-600"
                    >
                        Absent today
                    </p>


                    <p
                        class="mt-4
                               text-4xl
                               font-bold
                               text-red-500"
                    >
                        {{ number_format($absentToday) }}
                    </p>


                    <p
                        class="mt-3
                               text-sm
                               text-slate-500"
                    >
                        Review absence records
                    </p>

                </div>


                <div
                    class="flex
                           h-12 w-12
                           items-center
                           justify-center
                           rounded-2xl
                           bg-white/80
                           text-red-500"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-6 w-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0
                               1 1-18 0 9 9 0 0 1 18
                               0Zm-9 3.75h.008v.008H12
                               v-.008Z"
                        />
                    </svg>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
        MAIN DASHBOARD AREA
    ========================================================== --}}

    <div
        class="mt-7
               grid
               gap-6
               xl:grid-cols-3"
    >


        {{-- =====================================================
            TODAY'S CLASSES
        ====================================================== --}}

        <section
            class="overflow-hidden
                   rounded-[26px]
                   border
                   border-cyan-100
                   bg-white
                   shadow-sm
                   xl:col-span-2"
        >

            <div
                class="flex
                       flex-col
                       gap-4
                       border-b
                       border-slate-100
                       p-6
                       sm:flex-row
                       sm:items-start
                       sm:justify-between"
            >

                <div>

                    <h2
                        class="text-2xl
                               font-bold
                               text-slate-900"
                    >
                        Today’s classes
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        Confirmed student totals
                        for today’s class times.
                    </p>

                </div>



                {{-- =================================================
                    SCHEDULE / PRINT BUTTONS
                ================================================== --}}

                <div
                    class="flex
                           flex-wrap
                           items-center
                           gap-3"
                >

                    {{-- Open Schedule --}}
                    @if (
                        Route::has(
                            'admin.schedule.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'admin.schedule.index'
                            ) }}"
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-cyan-500
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-white
                                   transition
                                   hover:bg-cyan-600"
                        >
                            Open Schedule
                        </a>

                    @endif



                    {{-- Print / Export --}}
                    @if (
                        Route::has(
                            'admin.schedule.export.form'
                        )
                        &&
                        auth()
                            ->user()
                            ->hasPermission(
                                'enrolments.print'
                            )
                    )

                        <a
                            href="{{ route(
                                'admin.schedule.export.form'
                            ) }}"
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   gap-2
                                   rounded-xl
                                   border
                                   border-blue-300
                                   bg-blue-50
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-blue-700
                                   transition
                                   hover:bg-blue-100"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 9V2h12v7
                                       M6 18H4a2 2 0 0 1-2-2v-5
                                       a2 2 0 0 1 2-2h16
                                       a2 2 0 0 1 2 2v5
                                       a2 2 0 0 1-2 2h-2
                                       M6 14h12v8H6z"
                                />
                            </svg>

                            Print / Export

                        </a>

                    @endif

                </div>

            </div>



            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead>

                        <tr
                            class="border-b
                                   border-slate-200"
                        >

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Class time
                            </th>


                            <th
                                class="px-6 py-4
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Regular classes
                            </th>


                            <th
                                class="px-6 py-4
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Interactive
                            </th>


                            <th
                                class="px-6 py-4
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Total students
                            </th>


                            <th
                                class="px-6 py-4
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Status
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse (
                            $todayClasses
                            as $class
                        )

                            @php

                                $classTime =
                                    data_get(
                                        $class,
                                        'class_time',
                                        '—'
                                    );


                                $regularStudents =
                                    data_get(
                                        $class,
                                        'regular_students',
                                        0
                                    );


                                $interactiveStudents =
                                    data_get(
                                        $class,
                                        'interactive_students',
                                        0
                                    );


                                $totalStudents =
                                    data_get(
                                        $class,
                                        'total_students',
                                        $regularStudents
                                        +
                                        $interactiveStudents
                                    );


                                $classStatus =
                                    data_get(
                                        $class,
                                        'status',
                                        'Upcoming'
                                    );


                                $normalStatus =
                                    strtolower(
                                        $classStatus
                                    );


                                if (
                                    str_contains(
                                        $normalStatus,
                                        'progress'
                                    )
                                ) {

                                    $statusStyle =
                                        'bg-green-100 text-green-700';

                                }
                                elseif (
                                    str_contains(
                                        $normalStatus,
                                        'start'
                                    )
                                ) {

                                    $statusStyle =
                                        'bg-amber-100 text-amber-700';

                                }
                                elseif (
                                    str_contains(
                                        $normalStatus,
                                        'complete'
                                    )
                                ) {

                                    $statusStyle =
                                        'bg-slate-100 text-slate-600';

                                }
                                else {

                                    $statusStyle =
                                        'bg-cyan-100 text-cyan-700';
                                }

                            @endphp


                            <tr
                                class="transition
                                       hover:bg-cyan-50/40"
                            >

                                <td
                                    class="whitespace-nowrap
                                           px-6 py-6
                                           text-base
                                           font-semibold
                                           text-slate-800"
                                >
                                    {{ $classTime }}
                                </td>


                                <td
                                    class="px-6 py-6
                                           text-center
                                           text-base
                                           text-slate-700"
                                >
                                    {{ number_format($regularStudents) }}
                                </td>


                                <td
                                    class="px-6 py-6
                                           text-center
                                           text-base
                                           text-slate-700"
                                >
                                    {{ number_format($interactiveStudents) }}
                                </td>


                                <td
                                    class="px-6 py-6
                                           text-center
                                           text-lg
                                           font-bold
                                           text-cyan-700"
                                >
                                    {{ number_format($totalStudents) }}
                                </td>


                                <td
                                    class="px-6 py-6
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               px-4 py-2
                                               text-xs
                                               font-semibold
                                               {{ $statusStyle }}"
                                    >
                                        {{ $classStatus }}
                                    </span>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-16
                                           text-center"
                                >

                                    <div
                                        class="mx-auto
                                               flex
                                               h-14 w-14
                                               items-center
                                               justify-center
                                               rounded-full
                                               bg-cyan-50
                                               text-cyan-600"
                                    >
                                        📅
                                    </div>


                                    <p
                                        class="mt-4
                                               font-semibold
                                               text-slate-700"
                                    >
                                        No classes today
                                    </p>


                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500"
                                    >
                                        Today’s active classes
                                        will appear here.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>



        {{-- =====================================================
            RIGHT COLUMN
        ====================================================== --}}

        <div class="space-y-6">


            {{-- =================================================
                ABSENT STUDENTS
            ================================================== --}}

            <section
                class="rounded-[26px]
                       border
                       border-cyan-100
                       bg-white
                       p-6
                       shadow-sm"
            >

                <div>

                    <h2
                        class="text-2xl
                               font-bold
                               text-slate-900"
                    >
                        Absent today
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        {{ number_format($absentToday) }}

                        {{
                            $absentToday === 1
                                ? 'student'
                                : 'students'
                        }}
                    </p>

                </div>



                <div class="mt-5">

                    @forelse (
                        $absentStudents
                        as $student
                    )

                        <div
                            class="flex
                                   items-start
                                   justify-between
                                   gap-4
                                   border-b
                                   border-slate-100
                                   py-4
                                   last:border-0"
                        >

                            <div>

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{ $student->first_name ?? '' }}
                                    {{ $student->last_name ?? '' }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    Student ID:
                                    {{ $student->external_id ?? '—' }}
                                </p>

                            </div>


                            @if (
                                Route::has(
                                    'admin.students.show'
                                )
                            )

                                <a
                                    href="{{ route(
                                        'admin.students.show',
                                        $student
                                    ) }}"
                                    class="text-xs
                                           font-semibold
                                           text-cyan-700
                                           hover:text-cyan-900"
                                >
                                    View
                                </a>

                            @endif

                        </div>


                    @empty

                        <div
                            class="py-9
                                   text-center"
                        >

                            <div
                                class="mx-auto
                                       flex
                                       h-12 w-12
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-green-50
                                       text-green-600"
                            >
                                ✓
                            </div>


                            <p
                                class="mt-3
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                No absence information
                            </p>


                            <p
                                class="mt-1
                                       text-xs
                                       text-slate-500"
                            >
                                Absence records will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>


                @if (
                    Route::has(
                        'admin.attendance.index'
                    )
                )

                    <a
                        href="{{ route(
                            'admin.attendance.index'
                        ) }}"
                        class="mt-4
                               inline-flex
                               h-11 w-full
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-cyan-300
                               bg-cyan-50
                               text-sm
                               font-semibold
                               text-cyan-800
                               hover:bg-cyan-100"
                    >
                        Review Attendance
                    </a>

                @endif

            </section>



            {{-- =================================================
                STUDENT REVIEWS
            ================================================== --}}

            <section
                class="rounded-[26px]
                       border
                       border-cyan-100
                       bg-white
                       p-6
                       shadow-sm"
            >

                <div
                    class="flex
                           items-start
                           justify-between
                           gap-4"
                >

                    <div>

                        <h2
                            class="text-2xl
                                   font-bold
                                   text-slate-900"
                        >
                            Student Reviews
                        </h2>


                        <p
                            class="mt-1
                                   text-sm
                                   text-slate-500"
                        >
                            Trial, absence and inactive reviews
                        </p>

                    </div>


                    <span
                        class="inline-flex
                               min-w-8
                               items-center
                               justify-center
                               rounded-full
                               bg-purple-100
                               px-2 py-1
                               text-xs
                               font-semibold
                               text-purple-700"
                    >
                        {{ $adminReminders->count() }}
                    </span>

                </div>



                <div
                    class="mt-5
                           space-y-3"
                >

                    @forelse (
                        $adminReminders
                        as $reminder
                    )

                        @php

                            $reminderType =
                                data_get(
                                    $reminder,
                                    'type',
                                    'review'
                                );


                            $reminderTitle =
                                data_get(
                                    $reminder,
                                    'title',
                                    'Student review required'
                                );


                            $reminderMessage =
                                data_get(
                                    $reminder,
                                    'message',
                                    'A student requires review.'
                                );


                            $reminderUrl =
                                data_get(
                                    $reminder,
                                    'url'
                                );


                            if (
                                $reminderType
                                ===
                                'absence'
                                ||
                                $reminderType
                                ===
                                'deletion'
                            ) {

                                $reminderCardStyle =
                                    'border-red-200 bg-red-50';

                                $reminderDotStyle =
                                    'bg-red-500';

                                $reminderTitleStyle =
                                    'text-red-700';

                                $reminderTextStyle =
                                    'text-red-600';

                            }
                            elseif (
                                $reminderType
                                ===
                                'inactive-warning'
                            ) {

                                $reminderCardStyle =
                                    'border-amber-200 bg-amber-50';

                                $reminderDotStyle =
                                    'bg-amber-500';

                                $reminderTitleStyle =
                                    'text-amber-700';

                                $reminderTextStyle =
                                    'text-amber-600';

                            }
                            else {

                                $reminderCardStyle =
                                    'border-purple-200 bg-purple-50';

                                $reminderDotStyle =
                                    'bg-purple-500';

                                $reminderTitleStyle =
                                    'text-purple-700';

                                $reminderTextStyle =
                                    'text-purple-600';
                            }

                        @endphp


                        @if ($reminderUrl)

                            <a
                                href="{{ $reminderUrl }}"
                                class="group
                                       block
                                       rounded-xl
                                       border
                                       px-4 py-4
                                       transition
                                       hover:-translate-y-0.5
                                       hover:shadow-sm
                                       {{ $reminderCardStyle }}"
                            >

                                <div
                                    class="flex
                                           items-start
                                           gap-3"
                                >

                                    <span
                                        class="mt-2
                                               h-2.5 w-2.5
                                               shrink-0
                                               rounded-full
                                               {{ $reminderDotStyle }}"
                                    ></span>


                                    <div class="min-w-0 flex-1">

                                        <div
                                            class="flex
                                                   items-start
                                                   justify-between
                                                   gap-3"
                                        >

                                            <p
                                                class="font-semibold
                                                       {{ $reminderTitleStyle }}"
                                            >
                                                {{ $reminderTitle }}
                                            </p>


                                            <span
                                                class="shrink-0
                                                       font-bold
                                                       {{ $reminderTitleStyle }}
                                                       transition
                                                       group-hover:translate-x-1"
                                            >
                                                →
                                            </span>

                                        </div>


                                        <p
                                            class="mt-1
                                                   text-sm
                                                   {{ $reminderTextStyle }}"
                                        >
                                            {{ $reminderMessage }}
                                        </p>

                                    </div>

                                </div>

                            </a>


                        @else

                            <div
                                class="rounded-xl
                                       border
                                       px-4 py-4
                                       {{ $reminderCardStyle }}"
                            >

                                <div
                                    class="flex
                                           items-start
                                           gap-3"
                                >

                                    <span
                                        class="mt-2
                                               h-2.5 w-2.5
                                               shrink-0
                                               rounded-full
                                               {{ $reminderDotStyle }}"
                                    ></span>


                                    <div>

                                        <p
                                            class="font-semibold
                                                   {{ $reminderTitleStyle }}"
                                        >
                                            {{ $reminderTitle }}
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-sm
                                                   {{ $reminderTextStyle }}"
                                        >
                                            {{ $reminderMessage }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif


                    @empty

                        <div
                            class="rounded-xl
                                   border
                                   border-slate-100
                                   bg-slate-50
                                   px-4 py-7
                                   text-center"
                        >

                            <div
                                class="mx-auto
                                       flex
                                       h-10 w-10
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-green-100
                                       text-green-600"
                            >
                                ✓
                            </div>


                            <p
                                class="mt-3
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                No reviews requiring attention
                            </p>


                            <p
                                class="mt-1
                                       text-xs
                                       text-slate-500"
                            >
                                New student review warnings will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>



                @if (
                    Route::has(
                        'admin.student-reviews.index'
                    )
                    &&
                    auth()
                        ->user()
                        ->hasPermission(
                            'students.view'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.student-reviews.index'
                        ) }}"
                        class="mt-5
                               inline-flex
                               h-11 w-full
                               items-center
                               justify-center
                               gap-2
                               rounded-xl
                               border
                               border-purple-300
                               bg-purple-50
                               text-sm
                               font-semibold
                               text-purple-700
                               transition
                               hover:bg-purple-100"
                    >
                        Open Student Reviews

                        <span>
                            →
                        </span>
                    </a>

                @endif

            </section>



            {{-- =================================================
                WISHLIST NOTIFICATION CARD
            ================================================== --}}

            @if (
                Route::has(
                    'admin.wishlist.index'
                )
                &&
                auth()
                    ->user()
                    ->hasPermission(
                        'wishlist.view'
                    )
            )

                <a
                    href="{{ route(
                        'admin.wishlist.index'
                    ) }}"
                    class="group block"
                >

                    <section
                        class="relative
                               rounded-[26px]
                               border
                               border-purple-100
                               bg-purple-50
                               p-6
                               shadow-sm
                               transition
                               duration-200
                               hover:-translate-y-0.5
                               hover:border-purple-300
                               hover:shadow-md"
                    >

                        @if ($wishlistCount > 0)

                            <div
                                class="absolute
                                       right-4
                                       top-4
                                       inline-flex
                                       h-6
                                       min-w-6
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-red-500
                                       px-2
                                       text-xs
                                       font-bold
                                       text-white
                                       shadow-sm"
                            >
                                {{
                                    $wishlistCount > 99
                                        ? '99+'
                                        : $wishlistCount
                                }}
                            </div>

                        @endif



                        <div
                            class="flex
                                   items-center
                                   justify-between
                                   gap-4"
                        >

                            <div>

                                <div
                                    class="flex
                                           items-center
                                           gap-2"
                                >

                                    <p
                                        class="text-sm
                                               font-semibold
                                               text-purple-700"
                                    >
                                        Wishlist requests
                                    </p>


                                    @if ($wishlistCount > 0)

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   rounded-full
                                                   bg-red-100
                                                   px-2 py-0.5
                                                   text-[10px]
                                                   font-bold
                                                   uppercase
                                                   tracking-wide
                                                   text-red-600"
                                        >
                                            New
                                        </span>

                                    @endif

                                </div>



                                <p
                                    class="mt-2
                                           text-3xl
                                           font-bold
                                           text-purple-800"
                                >
                                    {{ number_format($wishlistCount) }}
                                </p>



                                <p
                                    class="mt-1
                                           text-xs
                                           text-purple-600"
                                >

                                    @if ($wishlistCount > 0)

                                        {{
                                            $wishlistCount === 1
                                                ? '1 active wishlist request needs review'
                                                : $wishlistCount . ' active wishlist requests need review'
                                        }}

                                    @else

                                        No active wishlist requests

                                    @endif

                                </p>



                                <div
                                    class="mt-4
                                           inline-flex
                                           items-center
                                           gap-1
                                           text-xs
                                           font-semibold
                                           text-purple-700
                                           transition
                                           group-hover:text-purple-900"
                                >
                                    Open Wishlist

                                    <span
                                        class="transition
                                               group-hover:translate-x-1"
                                    >
                                        →
                                    </span>

                                </div>

                            </div>



                            <div
                                class="relative
                                       flex
                                       h-12 w-12
                                       shrink-0
                                       items-center
                                       justify-center
                                       rounded-2xl
                                       bg-white
                                       text-purple-600
                                       shadow-sm
                                       transition
                                       group-hover:bg-purple-100"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    class="h-6 w-6"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M11.48 3.499a.562.562 0
                                           0 1 1.04 0l2.125 5.111a.563
                                           .563 0 0 0 .475.345l5.518
                                           .442c.499.04.701.663.321
                                           .988l-4.204 3.602a.563.563
                                           0 0 0-.182.557l1.285
                                           5.385a.562.562 0 0 1-.84
                                           .61l-4.725-2.885a.563.563
                                           0 0 0-.586 0L5.982
                                           20.54a.562.562 0 0 1-.84
                                           -.61l1.285-5.386a.562.562
                                           0 0 0-.182-.557l-4.204
                                           -3.602a.562.562 0 0 1
                                           .321-.988l5.518-.442a.563
                                           .563 0 0 0 .475-.345L11.48
                                           3.5Z"
                                    />
                                </svg>


                                @if ($wishlistCount > 0)

                                    <span
                                        class="absolute
                                               -right-1
                                               -top-1
                                               flex
                                               h-4 w-4"
                                    >

                                        <span
                                            class="absolute
                                                   inline-flex
                                                   h-full w-full
                                                   animate-ping
                                                   rounded-full
                                                   bg-red-400
                                                   opacity-75"
                                        ></span>


                                        <span
                                            class="relative
                                                   inline-flex
                                                   h-4 w-4
                                                   rounded-full
                                                   border-2
                                                   border-white
                                                   bg-red-500"
                                        ></span>

                                    </span>

                                @endif

                            </div>

                        </div>

                    </section>

                </a>


            @else

                <section
                    class="rounded-[26px]
                           border
                           border-purple-100
                           bg-purple-50
                           p-6
                           shadow-sm"
                >

                    <div
                        class="flex
                               items-center
                               justify-between
                               gap-4"
                    >

                        <div>

                            <p
                                class="text-sm
                                       font-semibold
                                       text-purple-700"
                            >
                                Wishlist requests
                            </p>


                            <p
                                class="mt-2
                                       text-3xl
                                       font-bold
                                       text-purple-800"
                            >
                                {{ number_format($wishlistCount) }}
                            </p>


                            <p
                                class="mt-1
                                       text-xs
                                       text-purple-600"
                            >
                                Students waiting for
                                future class places
                            </p>

                        </div>


                        <div
                            class="flex
                                   h-12 w-12
                                   items-center
                                   justify-center
                                   rounded-2xl
                                   bg-white
                                   text-purple-600"
                        >
                            ★
                        </div>

                    </div>

                </section>

            @endif

        </div>

    </div>

</div>

@endsection



{{-- =============================================================
    DASHBOARD LIVE CLOCK
============================================================= --}}

@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const currentTimeElement =
            document.getElementById(
                'dashboardCurrentTime'
            );


        const headerTimeElement =
            document.getElementById(
                'dashboardHeaderTime'
            );


        function updateDashboardTime() {

            const now =
                new Date();


            const formattedTime =
                now.toLocaleTimeString(
                    'en-AU',
                    {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true,
                    }
                );


            if (currentTimeElement) {

                currentTimeElement.textContent =
                    formattedTime;
            }


            if (headerTimeElement) {

                headerTimeElement.textContent =
                    formattedTime;
            }
        }


        updateDashboardTime();


        setInterval(
            updateDashboardTime,
            1000
        );

    }
);

</script>

@endpush
