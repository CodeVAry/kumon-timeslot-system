@extends('layouts.admin')

@section('title', 'Students')

@section('page-title', 'Students')

@php
    $breadcrumbs = [
        [
            'label' => 'Student Setup',
            'url' => null,
        ],
        [
            'label' => 'Student List',
            'url' => null,
        ],
    ];
@endphp


@section('content')

<div class="space-y-6">

    {{-- =====================================================
        MESSAGES
    ====================================================== --}}

    @if (session('success'))

        <div
            class="rounded-xl
                   border border-green-200
                   bg-green-50
                   px-5 py-4
                   text-sm
                   text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif


    @if (session('error'))

        <div
            class="rounded-xl
                   border border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div
        class="flex
               flex-col
               gap-5
               lg:flex-row
               lg:items-center
               lg:justify-between"
    >

        <div>

            <h1
                class="text-3xl
                       font-bold
                       tracking-tight
                       text-cyan-950"
            >
                Students
            </h1>

            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Search, filter and manage student records
                and linked guardians.
            </p>

        </div>


        <div class="flex flex-wrap gap-3">

            @if (Route::has('admin.guardians.index'))

                <a
                    href="{{ route(
                        'admin.guardians.index'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-cyan-200
                           bg-white
                           px-6
                           text-sm
                           font-semibold
                           text-cyan-800
                           hover:bg-cyan-50"
                >
                    Guardians
                </a>

            @endif


            @if (
                auth()->user()
                    ->hasPermission(
                        'students.create'
                    )
            )

                <a
                    href="{{ route(
                        'admin.student-registration.student'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           gap-2
                           rounded-xl
                           bg-cyan-500
                           px-6
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-cyan-600"
                >
                    + Add Student
                </a>

            @endif

        </div>

    </div>



    {{-- =====================================================
        FILTERS
    ====================================================== --}}

    <div
        class="rounded-2xl
               border
               border-cyan-200
               bg-white
               p-5
               shadow-sm"
    >

        <form
            method="GET"
            action="{{ route(
                'admin.students.index'
            ) }}"
        >

            <div
                class="grid
                       gap-5
                       md:grid-cols-2
                       xl:grid-cols-3"
            >

                <div>

                    <label
                        for="search"
                        class="mb-2 block
                               text-xs
                               font-semibold
                               text-slate-600"
                    >
                        Student
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Student name or ID"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>


                <div>

                    <label
                        for="student_status_id"
                        class="mb-2 block
                               text-xs
                               font-semibold
                               text-slate-600"
                    >
                        Status
                    </label>

                    <select
                        id="student_status_id"
                        name="student_status_id"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                        <option value="">
                            Any Status
                        </option>


                        @foreach (
                            $studentStatuses
                            as $studentStatus
                        )

                            <option
                                value="{{
                                    $studentStatus->id
                                }}"
                                @selected(
                                    request(
                                        'student_status_id'
                                    )
                                    ==
                                    $studentStatus->id
                                )
                            >
                                {{
                                    $studentStatus
                                        ->status_name
                                }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label
                        for="guardian"
                        class="mb-2 block
                               text-xs
                               font-semibold
                               text-slate-600"
                    >
                        Guardian
                    </label>

                    <input
                        id="guardian"
                        type="text"
                        name="guardian"
                        value="{{ request('guardian') }}"
                        placeholder="Name, phone or email"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>


                <div>

                    <label
                        for="day_id"
                        class="mb-2 block
                               text-xs
                               font-semibold
                               text-slate-600"
                    >
                        Day
                    </label>

                    <select
                        id="day_id"
                        name="day_id"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                        <option value="">
                            Any Day
                        </option>


                        @foreach ($days as $day)

                            <option
                                value="{{ $day->id }}"
                                @selected(
                                    request('day_id')
                                    ==
                                    $day->id
                                )
                            >
                                {{ $day->day_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label
                        for="timeslot"
                        class="mb-2 block
                               text-xs
                               font-semibold
                               text-slate-600"
                    >
                        Class Time
                    </label>

                    <select
                        id="timeslot"
                        name="timeslot"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                        <option value="">
                            Any Time
                        </option>


                        @foreach (
                            $timeslots
                            as $timeslot
                        )

                            <option
                                value="{{ $timeslot }}"
                                @selected(
                                    request('timeslot')
                                    ==
                                    $timeslot
                                )
                            >
                                {{
                                    \Carbon\Carbon::parse(
                                        $timeslot
                                    )->format(
                                        'g:i A'
                                    )
                                }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label
                        for="section_id"
                        class="mb-2 block
                               text-xs
                               font-semibold
                               text-slate-600"
                    >
                        Class
                    </label>

                    <select
                        id="section_id"
                        name="section_id"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                        <option value="">
                            Any Class
                        </option>


                        @foreach (
                            $sections
                            as $section
                        )

                            <option
                                value="{{ $section->id }}"
                                @selected(
                                    request('section_id')
                                    ==
                                    $section->id
                                )
                            >
                                {{ $section->section_name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <div
                class="mt-5
                       flex
                       flex-col
                       gap-3
                       sm:flex-row
                       sm:justify-end"
            >

                <a
                    href="{{ route(
                        'admin.students.index'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-cyan-200
                           px-8
                           font-semibold
                           text-cyan-800"
                >
                    Clear
                </a>


                <button
                    type="submit"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           bg-cyan-500
                           px-8
                           font-semibold
                           text-white"
                >
                    Apply Filters
                </button>

            </div>

        </form>

    </div>



    {{-- =====================================================
        RESULTS
    ====================================================== --}}

    <div class="flex justify-between">

        <p class="text-sm text-slate-500">

            {{ $students->total() }}

            {{
                $students->total()
                === 1
                    ? 'result'
                    : 'results'
            }}

        </p>

    </div>



    {{-- =====================================================
        TABLE
    ====================================================== --}}

    <div
        class="overflow-hidden
               rounded-2xl
               border
               border-cyan-200
               bg-white
               shadow-sm"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead
                    class="border-b
                           border-cyan-200
                           bg-cyan-50/60"
                >

                    <tr>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Student
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Guardian
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Student ID
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            DOB
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Class
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Day & Time
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Notes
                        </th>

                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase text-slate-500">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-cyan-100">

                    @forelse (
                        $students
                        as $student
                    )

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Guardian
                            |--------------------------------------------------------------------------
                            */

                            $primaryGuardian =
                                $student
                                    ->guardians
                                    ->first(
                                        function ($guardian) {

                                            return
                                                (bool)
                                                $guardian
                                                    ->pivot
                                                    ->is_primary;
                                        }
                                    );


                            if (!$primaryGuardian) {

                                $primaryGuardian =
                                    $student
                                        ->guardians
                                        ->first();
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Enrolments
                            |--------------------------------------------------------------------------
                            */

                            $confirmedEnrolments =
                                $student
                                    ->enrolments
                                    ->filter(
                                        function ($enrolment) {

                                            return
                                                $enrolment
                                                    ->is_active
                                                &&
                                                !$enrolment
                                                    ->is_wishlist;
                                        }
                                    );


                            $wishlistEnrolments =
                                $student
                                    ->enrolments
                                    ->filter(
                                        function ($enrolment) {

                                            return
                                                $enrolment
                                                    ->is_active
                                                &&
                                                $enrolment
                                                    ->is_wishlist;
                                        }
                                    );


                            $classNames =
                                $confirmedEnrolments
                                    ->map(
                                        function ($enrolment) {

                                            $class =
                                                $enrolment
                                                    ->sectionOffering
                                                    ?->section
                                                    ?->section_name;


                                            $subSection =
                                                $enrolment
                                                    ->subSection
                                                    ?->sub_section_name;


                                            if (
                                                $class
                                                &&
                                                $subSection
                                            ) {

                                                return
                                                    $class
                                                    .
                                                    ' - '
                                                    .
                                                    $subSection;
                                            }


                                            return $class;
                                        }
                                    )
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');


                            /*
                            |--------------------------------------------------------------------------
                            | Vacation Override
                            |--------------------------------------------------------------------------
                            */

                            $isVacation =
                                $vacationStudentIds
                                    ->contains(
                                        (int)
                                        $student->id
                                    );


                            if ($isVacation) {

                                $displayStatus =
                                    \App\Models\Admin\StudentLeave::VACATION_LABEL;


                                $statusColour =
                                    \App\Models\Admin\StudentLeave::VACATION_COLOR;

                            } else {

                                $displayStatus =
                                    $student
                                        ->studentStatus
                                        ?->status_name
                                    ??
                                    '—';


                                $statusColour =
                                    $student
                                        ->studentStatus
                                        ?->color_code
                                    ??
                                    '#64748b';
                            }


                            if (
                                !preg_match(
                                    '/^#[0-9A-Fa-f]{6}$/',
                                    $statusColour
                                )
                            ) {

                                $statusColour =
                                    '#64748b';
                            }

                        @endphp


                        <tr
                            class="
                                transition
                                hover:bg-cyan-50/50
                                {{
                                    $isVacation
                                        ? 'bg-cyan-50/30'
                                        : ''
                                }}
                            "
                        >

                            {{-- Student --}}

                            <td
                                class="whitespace-nowrap
                                       px-5 py-5"
                            >

                                <p
                                    class="font-semibold"
                                    style="
                                        color:
                                        {{ $statusColour }};
                                    "
                                >
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-400"
                                >
                                    Record ID:
                                    {{ $student->id }}
                                </p>

                            </td>


                            {{-- Guardian --}}

                            <td
                                class="px-5 py-5
                                       text-sm
                                       text-slate-700"
                            >

                                @if ($primaryGuardian)

                                    <p>
                                        {{
                                            $primaryGuardian
                                                ->first_name
                                        }}

                                        {{
                                            $primaryGuardian
                                                ->last_name
                                        }}
                                    </p>


                                    @if (
                                        $student
                                            ->guardians
                                            ->count()
                                        >
                                        1
                                    )

                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-slate-400"
                                        >
                                            +
                                            {{
                                                $student
                                                    ->guardians
                                                    ->count()
                                                -
                                                1
                                            }}
                                            more
                                        </p>

                                    @endif


                                @else

                                    —

                                @endif

                            </td>


                            {{-- Student ID --}}

                            <td
                                class="px-5 py-5
                                       text-sm
                                       text-slate-700"
                            >
                                {{ $student->external_id }}
                            </td>


                            {{-- DOB --}}

                            <td
                                class="px-5 py-5
                                       text-sm
                                       text-slate-700"
                            >
                                {{
                                    $student
                                        ->date_of_birth
                                        ?->format(
                                            'd M Y'
                                        )
                                    ??
                                    '—'
                                }}
                            </td>


                            {{-- Status --}}

                            <td
                                class="whitespace-nowrap
                                       px-5 py-5"
                            >

                                @if (
                                    $displayStatus
                                    !==
                                    '—'
                                )

                                    <span
                                        class="inline-flex
                                               items-center
                                               gap-2
                                               text-xs
                                               font-semibold
                                               uppercase"
                                        style="
                                            color:
                                            {{ $statusColour }};
                                        "
                                    >

                                        <span
                                            class="h-2.5
                                                   w-2.5
                                                   rounded-full"
                                            style="
                                                background-color:
                                                {{ $statusColour }};
                                            "
                                        ></span>


                                        {{ $displayStatus }}

                                    </span>


                                    @if ($isVacation)

                                        <p
                                            class="mt-1
                                                   text-[11px]
                                                   font-medium"
                                            style="
                                                color:
                                                {{ $statusColour }};
                                            "
                                        >
                                            On Leave
                                        </p>

                                    @endif

                                @else

                                    —

                                @endif

                            </td>


                            {{-- Class --}}

                            <td
                                class="px-5 py-5
                                       text-sm
                                       text-slate-700"
                            >

                                {{
                                    $classNames
                                    ?: '—'
                                }}

                            </td>


                            {{-- Day & Time --}}

                            <td
                                class="px-5 py-5
                                       text-sm
                                       text-slate-700"
                            >

                                @forelse (
                                    $confirmedEnrolments
                                    as $enrolment
                                )

                                    @php

                                        $offering =
                                            $enrolment
                                                ->sectionOffering;

                                    @endphp


                                    @if ($offering)

                                        <div class="mb-2">

                                            {{
                                                $offering
                                                    ->day
                                                    ?->day_name
                                                ??
                                                ''
                                            }}

                                            ·

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $offering
                                                        ->start_time
                                                )->format(
                                                    'g:i A'
                                                )
                                            }}

                                        </div>

                                    @endif


                                @empty

                                    —

                                @endforelse


                                @if (
                                    $wishlistEnrolments
                                        ->count()
                                    >
                                    0
                                )

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-purple-100
                                               px-2.5 py-1
                                               text-xs
                                               font-semibold
                                               text-purple-700"
                                    >
                                        {{
                                            $wishlistEnrolments
                                                ->count()
                                        }}
                                        wishlist
                                    </span>

                                @endif

                            </td>


                            {{-- Notes --}}

                            <td
                                class="max-w-xs
                                       px-5 py-5
                                       text-sm
                                       text-slate-600"
                            >

                                @if ($student->notes)

                                    <p class="line-clamp-2">
                                        {{ $student->notes }}
                                    </p>

                                @endif


                                <span>
                                    Can leave:
                                    {{
                                        $student
                                            ->can_leave_alone
                                            ? 'Yes'
                                            : 'No'
                                    }}
                                </span>

                            </td>


                            {{-- Actions --}}

                            <td class="px-5 py-5">

                                <div
                                    class="flex
                                           justify-center
                                           gap-2"
                                >

                                    <a
                                        href="{{ route(
                                            'admin.students.show',
                                            $student
                                        ) }}"
                                        class="inline-flex
                                               h-10
                                               items-center
                                               rounded-xl
                                               border
                                               border-cyan-300
                                               bg-cyan-50
                                               px-5
                                               font-semibold
                                               text-cyan-800"
                                    >
                                        View
                                    </a>


                                    @if (
                                        auth()->user()
                                            ->hasPermission(
                                                'students.delete'
                                            )
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.students.destroy',
                                                $student
                                            ) }}"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this student?'
                                                );
                                            "
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="inline-flex
                                                       h-10
                                                       items-center
                                                       rounded-xl
                                                       border
                                                       border-red-300
                                                       bg-red-50
                                                       px-5
                                                       font-semibold
                                                       text-red-700"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="px-6 py-16
                                       text-center
                                       text-slate-500"
                            >
                                No students matched the selected filters.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($students->hasPages())

            <div
                class="border-t
                       border-cyan-100
                       px-6 py-4"
            >
                {{ $students->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
