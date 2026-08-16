@extends('layouts.admin')

@section('title', 'Attendance')

@section('page-title', 'Attendance')


@php

    $breadcrumbs = [
        [
            'label' => 'Attendance',
            'url' => null,
        ],
    ];


    $presentPercentage =
        $studentsEnrolled > 0
            ? round(
                (
                    $presentCount /
                    $studentsEnrolled
                ) * 100,
                1
            )
            : 0;


    $absentPercentage =
        $studentsEnrolled > 0
            ? round(
                (
                    $absentCount /
                    $studentsEnrolled
                ) * 100,
                1
            )
            : 0;


    $vacationPercentage =
        $studentsEnrolled > 0
            ? round(
                (
                    $vacationCount /
                    $studentsEnrolled
                ) * 100,
                1
            )
            : 0;

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div>

        <h1
            class="text-4xl
                   font-bold
                   tracking-tight
                   text-slate-900"
        >
            Attendance
        </h1>


        <p
            class="mt-2
                   text-sm
                   text-slate-500"
        >
            View daily classes and record
            student attendance.
        </p>

    </div>



    {{-- =========================================================
        DAY SELECTOR
    ========================================================== --}}

    <div
        class="flex flex-wrap
               gap-3
               rounded-2xl
               border
               border-slate-200
               bg-white
               p-3
               shadow-sm"
    >

        @foreach ($days as $day)

            @php

                $date =
                    $dayDates[
                        $day->id
                    ];

            @endphp


            <a
                href="{{ route(
                    'admin.attendance.index',
                    [
                        'day_id' =>
                            $day->id,
                    ]
                ) }}"
                class="min-w-32
                       rounded-xl
                       border
                       px-6 py-3
                       text-center
                       transition
                    {{
                        (int)
                        $selectedDayId
                        ===
                        (int)
                        $day->id

                            ? 'border-blue-600 bg-blue-600 text-white shadow-sm'

                            : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:bg-blue-50'
                    }}"
            >

                <p
                    class="text-sm
                           font-bold"
                >

                    @if ($date->isToday())

                        Today

                    @else

                        {{ $day->day_name }}

                    @endif

                </p>


                <p
                    class="mt-1
                           text-xs
                        {{
                            (int)
                            $selectedDayId
                            ===
                            (int)
                            $day->id

                                ? 'text-blue-100'

                                : 'text-slate-400'
                        }}"
                >
                    {{ $date->format('M j') }}
                </p>

            </a>

        @endforeach

    </div>



    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}

    <div
        class="grid
               gap-4
               md:grid-cols-2
               xl:grid-cols-5"
    >


        {{-- Selected Day --}}
        <div
            class="rounded-2xl
                   border
                   border-blue-100
                   bg-white
                   p-5
                   shadow-sm"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Selected Day
            </p>


            <p
                class="mt-2
                       text-lg
                       font-bold
                       text-slate-900"
            >
                {{
                    $selectedDate
                        ?->format(
                            'l, j F Y'
                        )
                    ?? '—'
                }}
            </p>

        </div>



        {{-- Classes --}}
        <div
            class="rounded-2xl
                   border
                   border-blue-100
                   bg-blue-50/50
                   p-5
                   shadow-sm"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Total Classes
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                {{ $totalClasses }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-400"
            >
                Scheduled
            </p>

        </div>



        {{-- Present --}}
        <div
            class="rounded-2xl
                   border
                   border-green-100
                   bg-green-50
                   p-5
                   shadow-sm"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Present
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-green-600"
            >
                {{ $presentCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                {{ $presentPercentage }}%
                of enrolled
            </p>

        </div>



        {{-- Absent --}}
        <div
            class="rounded-2xl
                   border
                   border-red-100
                   bg-red-50
                   p-5
                   shadow-sm"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Absent
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-red-500"
            >
                {{ $absentCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                {{ $absentPercentage }}%
                of enrolled
            </p>

        </div>



        {{-- Vacation --}}
        <div
            class="rounded-2xl
                   border
                   border-amber-100
                   bg-amber-50
                   p-5
                   shadow-sm"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Vacation
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-amber-600"
            >
                {{ $vacationCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                {{ $vacationPercentage }}%
                of enrolled
            </p>

        </div>

    </div>



    {{-- =========================================================
        MAIN AREA
    ========================================================== --}}

    <div
        class="grid
               gap-5
               xl:grid-cols-[0.85fr_1.15fr]"
    >


        {{-- =====================================================
            CLASS TIMES
        ====================================================== --}}

        <section
            class="overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >


            <div
                class="border-b
                       border-slate-100
                       px-6 py-5"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Class Times
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    All classes scheduled for
                    {{
                        $selectedDate
                            ?->format(
                                'l, j F Y'
                            )
                    }}.
                </p>

            </div>



            <div class="overflow-x-auto">

                <table class="min-w-full">


                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Time
                            </th>


                            <th
                                class="px-5 py-4
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Classes
                            </th>


                            <th
                                class="px-5 py-4
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Students
                            </th>


                            <th
                                class="px-5 py-4
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse ($classTimes as $rawTime => $classes)

                            @php

                                $endTime =
                                    $classes
                                        ->max(
                                            'end_time'
                                        );

                                $studentTotal =
                                    $classes
                                        ->sum(
                                            'enrolled_count'
                                        );

                            @endphp


                            <tr
                                class="
                                    {{
                                        $selectedTime
                                        ===
                                        $rawTime

                                            ? 'bg-blue-50'

                                            : 'hover:bg-slate-50'
                                    }}"
                            >

                                <td
                                    class="whitespace-nowrap
                                           px-5 py-5
                                           font-bold
                                           text-slate-900"
                                >

                                    {{
                                        \Carbon\Carbon::parse(
                                            $rawTime
                                        )->format(
                                            'g:i A'
                                        )
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::parse(
                                            $endTime
                                        )->format(
                                            'g:i A'
                                        )
                                    }}

                                </td>


                                <td
                                    class="px-5 py-5
                                           text-center
                                           text-sm
                                           text-slate-700"
                                >
                                    {{ $classes->count() }}
                                </td>


                                <td
                                    class="px-5 py-5
                                           text-center
                                           text-sm
                                           font-semibold
                                           text-slate-700"
                                >
                                    {{ $studentTotal }}
                                </td>


                                <td
                                    class="px-5 py-5
                                           text-center"
                                >

                                    <a
                                        href="{{ route(
                                            'admin.attendance.index',
                                            [
                                                'day_id' =>
                                                    $selectedDayId,

                                                'time' =>
                                                    $rawTime,
                                            ]
                                        ) }}"
                                        class="inline-flex
                                               h-10
                                               items-center
                                               justify-center
                                               rounded-xl
                                               border
                                               border-blue-300
                                               px-4
                                               text-xs
                                               font-semibold
                                               text-blue-700
                                               hover:bg-blue-50"
                                    >
                                        View Classes
                                    </a>

                                </td>

                            </tr>


                        @empty


                            <tr>

                                <td
                                    colspan="4"
                                    class="px-6 py-14
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >
                                    No classes scheduled.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>



        {{-- =====================================================
            CLASSES AT SELECTED TIME
        ====================================================== --}}

        <section
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-6
                   shadow-sm"
        >

            <div>

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >

                    @if ($selectedTime)

                        Classes at

                        {{
                            \Carbon\Carbon::parse(
                                $selectedTime
                            )->format(
                                'g:i A'
                            )
                        }}

                    @else

                        Classes

                    @endif

                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Select a class to take
                    attendance.
                </p>

            </div>



            <div
                class="mt-5
                       grid
                       gap-4
                       md:grid-cols-2"
            >

                @forelse ($selectedTimeOfferings as $offering)

                    <div
                        class="rounded-2xl
                               border
                               border-slate-200
                               bg-white
                               p-5
                               shadow-sm"
                    >

                        <div
                            class="flex
                                   items-start
                                   justify-between
                                   gap-3"
                        >

                            <div>

                                <h3
                                    class="text-lg
                                           font-bold
                                           text-slate-900"
                                >
                                    {{
                                        $offering
                                            ->section
                                            ?->section_name
                                        ?? 'Class'
                                    }}
                                </h3>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >

                                    {{
                                        $offering
                                            ->enrolled_count
                                    }}

                                    /

                                    {{
                                        $offering
                                            ->max_seats
                                    }}

                                    students

                                </p>

                            </div>


                            <span
                                class="rounded-full
                                       bg-blue-50
                                       px-3 py-1
                                       text-xs
                                       font-semibold
                                       text-blue-700"
                            >

                                {{
                                    \Carbon\Carbon::parse(
                                        $offering
                                            ->start_time
                                    )->format(
                                        'g:i A'
                                    )
                                }}

                            </span>

                        </div>



                        <a
                            href="{{ route(
                                'admin.attendance.takeAttendance',
                                [
                                    'sectionOffering' =>
                                        $offering->id,

                                    'date' =>
                                        $selectedDate
                                            ->toDateString(),
                                ]
                            ) }}"
                            class="mt-5
                                   inline-flex
                                   h-11 w-full
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-600
                                   text-sm
                                   font-semibold
                                   text-white
                                   transition
                                   hover:bg-blue-700"
                        >
                            Take Attendance
                        </a>

                    </div>


                @empty


                    <div
                        class="col-span-full
                               rounded-xl
                               border
                               border-slate-200
                               bg-slate-50
                               p-10
                               text-center"
                    >

                        <p
                            class="font-semibold
                                   text-slate-700"
                        >
                            No class selected
                        </p>


                        <p
                            class="mt-1
                                   text-sm
                                   text-slate-500"
                        >
                            Choose a class time
                            from the left.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>

    </div>

</div>

@endsection
