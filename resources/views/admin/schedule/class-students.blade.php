@extends('layouts.admin')

@section('title', 'Class Student List')

@section('page-title', 'Class Student List')


@section('content')

<div class="space-y-6">

    <a
        href="{{ route(
            'admin.schedule.index',
            [
                'day_id' =>
                    $sectionOffering->day_id,

                'time' =>
                    \Carbon\Carbon::parse(
                        $sectionOffering
                            ->start_time
                    )->format(
                        'H:i:s'
                    ),

                'offering_id' =>
                    $sectionOffering->id,
            ]
        ) }}"
        class="text-sm
               font-semibold
               text-blue-600"
    >
        ← Back to Schedule
    </a>



    <div
        class="flex
               flex-col
               gap-4
               sm:flex-row
               sm:items-end
               sm:justify-between"
    >

        <div>

            <h1
                class="text-3xl
                       font-bold
                       text-slate-900"
            >
                {{
                    $sectionOffering
                        ->section
                        ?->section_name
                    ??
                    'Class'
                }}
            </h1>


            @if (
                $sectionOffering
                    ->subSections
                    ->isNotEmpty()
            )

                <p
                    class="mt-1
                           font-semibold
                           text-blue-600"
                >
                    Sub-sections:

                    {{
                        $sectionOffering
                            ->subSections
                            ->pluck(
                                'sub_section_name'
                            )
                            ->implode(', ')
                    }}
                </p>

            @endif


            <p class="mt-2 text-sm text-slate-500">

                {{
                    $sectionOffering
                        ->day
                        ?->day_name
                }}

                ·

                {{
                    \Carbon\Carbon::parse(
                        $sectionOffering
                            ->start_time
                    )->format(
                        'g:i A'
                    )
                }}

                –

                {{
                    \Carbon\Carbon::parse(
                        $sectionOffering
                            ->end_time
                    )->format(
                        'g:i A'
                    )
                }}

            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-400"
            >
                Leave status checked for
                {{ $classDate->format('d M Y') }}
            </p>

        </div>


        @if (
            auth()->user()
                ->hasPermission(
                    'enrolments.print'
                )
        )

            <a
                href="{{ route(
                    'admin.schedule.class-students.print',
                    $sectionOffering
                ) }}"
                target="_blank"
                class="inline-flex
                       h-11
                       items-center
                       rounded-xl
                       bg-blue-600
                       px-5
                       font-semibold
                       text-white"
            >
                Print Class List
            </a>

        @endif

    </div>



    {{-- Summary --}}

    <div
        class="grid
               gap-4
               sm:grid-cols-3"
    >

        <div class="rounded-xl border bg-white p-5">

            <p class="text-xs text-slate-400">
                Confirmed Students
            </p>

            <p class="mt-1 text-2xl font-bold">
                {{ $enrolments->total() }}
            </p>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-xs text-slate-400">

                @if (
                    strtolower(
                        $sectionOffering
                            ->section
                            ?->section_name
                        ??
                        ''
                    )
                    ===
                    'math'
                )

                    Shared Math Capacity

                @else

                    Maximum Seats

                @endif

            </p>

            <p class="mt-1 text-2xl font-bold">
                {{ $sectionOffering->max_seats }}
            </p>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-xs text-slate-400">
                Wishlist
            </p>

            <p class="mt-1 text-2xl font-bold">
                {{ $wishlistCount }}
            </p>

        </div>

    </div>



    {{-- Student table --}}

    <div
        class="overflow-hidden
               rounded-2xl
               border
               bg-white"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-4 text-left">
                            Student
                        </th>

                        <th class="px-6 py-4 text-left">
                            Student ID
                        </th>

                        <th class="px-6 py-4 text-left">
                            Sub-section
                        </th>

                        <th class="px-6 py-4 text-left">
                            Guardian
                        </th>

                        <th class="px-6 py-4 text-left">
                            Phone
                        </th>

                        <th class="px-6 py-4 text-left">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y">

                    @forelse (
                        $enrolments
                        as $enrolment
                    )

                        @php

                            $student =
                                $enrolment
                                    ->student;


                            $guardian =
                                $student
                                    ?->guardians
                                    ?->first(
                                        function ($guardian) {

                                            return
                                                (bool)
                                                $guardian
                                                    ->pivot
                                                    ->is_primary;
                                        }
                                    );


                            if (
                                !$guardian
                                &&
                                $student
                            ) {

                                $guardian =
                                    $student
                                        ->guardians
                                        ->first();
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Vacation Override
                            |--------------------------------------------------------------------------
                            */

                            $isVacation =
                                $student
                                &&
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
                                        ?->studentStatus
                                        ?->status_name
                                    ??
                                    '—';


                                $statusColour =
                                    $student
                                        ?->studentStatus
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
                                {{
                                    $isVacation
                                        ? 'bg-cyan-50/50'
                                        : ''
                                }}
                            "
                        >

                            <td
                                class="px-6 py-4
                                       font-semibold"
                                style="
                                    color:
                                    {{ $statusColour }};
                                "
                            >
                                {{
                                    $student
                                        ?->first_name
                                }}

                                {{
                                    $student
                                        ?->last_name
                                }}
                            </td>


                            <td class="px-6 py-4">

                                {{
                                    $student
                                        ?->external_id
                                    ??
                                    '—'
                                }}

                            </td>


                            <td
                                class="px-6 py-4
                                       font-semibold"
                            >

                                {{
                                    $enrolment
                                        ->subSection
                                        ?->sub_section_name
                                    ??
                                    '—'
                                }}

                            </td>


                            <td class="px-6 py-4">

                                @if ($guardian)

                                    {{
                                        $guardian
                                            ->first_name
                                    }}

                                    {{
                                        $guardian
                                            ->last_name
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            <td class="px-6 py-4">

                                {{
                                    $guardian
                                        ?->phone
                                    ??
                                    '—'
                                }}

                            </td>


                            <td class="px-6 py-4">

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
                                               font-semibold"
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
                                                   text-[11px]"
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

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12
                                       text-center
                                       text-slate-500"
                            >
                                No students found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($enrolments->hasPages())

            <div
                class="border-t
                       px-6 py-4"
            >
                {{ $enrolments->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
