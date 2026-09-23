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
                    $presentCount
                    /
                    $studentsEnrolled
                )
                *
                100,
                1
            )
            : 0;


    $absentPercentage =
        $studentsEnrolled > 0
            ? round(
                (
                    $absentCount
                    /
                    $studentsEnrolled
                )
                *
                100,
                1
            )
            : 0;


    $vacationPercentage =
        $studentsEnrolled > 0
            ? round(
                (
                    $vacationCount
                    /
                    $studentsEnrolled
                )
                *
                100,
                1
            )
            : 0;

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div
        class="flex
               flex-col
               gap-4
               lg:flex-row
               lg:items-end
               lg:justify-between"
    >

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
                Select a day and mark each student once,
                even when they attend more than one class.
            </p>

        </div>


        <form
            method="GET"
            action="{{ route('admin.attendance.index') }}"
            class="flex
                   flex-col
                   gap-2
                   sm:flex-row
                   sm:items-end"
        >

            <div>

                <label
                    for="attendance_date_filter"
                    class="mb-1
                           block
                           text-xs
                           font-bold
                           uppercase
                           tracking-wide
                           text-slate-500"
                >
                    Attendance Date
                </label>


                <input
                    id="attendance_date_filter"
                    type="date"
                    name="date"
                    value="{{ $selectedDate?->toDateString() }}"
                    max="{{ now()->toDateString() }}"
                    class="h-11
                           rounded-xl
                           border-2
                           border-slate-400
                           bg-white
                           px-3
                           text-sm
                           font-semibold
                           text-slate-800
                           focus:border-blue-600
                           focus:ring-blue-600"
                    required
                >

            </div>


            <button
                type="submit"
                class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-600
                       px-5
                       text-sm
                       font-bold
                       text-white
                       hover:bg-blue-700"
            >
                Load Date
            </button>


            <a
                href="{{ route('admin.attendance.index') }}"
                class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       border-2
                       border-slate-300
                       bg-white
                       px-4
                       text-sm
                       font-semibold
                       text-slate-600
                       hover:bg-slate-50"
            >
                Today
            </a>

        </form>

    </div>



    {{-- =========================================================
        MESSAGES
    ========================================================== --}}

    @if (session('success'))

        <div
            class="rounded-xl
                   border
                   border-green-200
                   bg-green-50
                   px-5 py-4
                   text-sm
                   font-semibold
                   text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif


    @if (session('error'))

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm
                   font-semibold
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif


    @if ($errors->any())

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm
                   text-red-700"
        >

            <p class="font-bold">
                Attendance was not saved.
            </p>


            <ul class="mt-2 list-disc pl-5">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
        DAY SELECTOR
    ========================================================== --}}

    <div
        class="flex
               flex-wrap
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

                        'date' =>
                            $date
                                ->toDateString(),
                    ]
                ) }}"
                class="min-w-32
                       rounded-xl
                       border-2
                       px-6 py-3
                       text-center
                       transition
                    {{
                        (int)
                        $selectedDayId
                        ===
                        (int)
                        $day->id

                            ? 'border-blue-700 bg-blue-600 text-white shadow-sm'

                            : 'border-slate-400 bg-white text-slate-700 hover:border-blue-500 hover:bg-blue-50'
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

                                : 'text-slate-500'
                        }}"
                >
                    {{ $date->format('M j') }}
                </p>

            </a>

        @endforeach

    </div>



    @if (
        $selectedDate
        &&
        !$selectedDate->isToday()
    )

        <div
            class="flex
                   flex-col
                   gap-2
                   rounded-2xl
                   border
                   border-amber-200
                   bg-amber-50
                   px-5 py-4
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div>

                <p
                    class="text-sm
                           font-bold
                           text-amber-900"
                >
                    Historical attendance
                </p>


                <p
                    class="mt-1
                           text-xs
                           text-amber-700"
                >
                    You are viewing and editing attendance for
                    {{ $selectedDate->format('l, j F Y') }}.
                </p>

            </div>


            <span
                class="inline-flex
                       w-fit
                       rounded-full
                       bg-amber-100
                       px-3 py-1
                       text-xs
                       font-bold
                       text-amber-800"
            >
                Past Date
            </span>

        </div>

    @endif


    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div
        class="grid
               gap-4
               sm:grid-cols-2
               xl:grid-cols-6"
    >


        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Students
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                {{ $studentsEnrolled }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Unique students
            </p>

        </div>


        <div
            class="rounded-2xl
                   border
                   border-blue-100
                   bg-blue-50
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-500"
            >
                Classes
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-blue-700"
            >
                {{ $totalClasses }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Active offerings
            </p>

        </div>


        <div
            class="rounded-2xl
                   border
                   border-green-100
                   bg-green-50
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-500"
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
            </p>

        </div>


        <div
            class="rounded-2xl
                   border
                   border-red-100
                   bg-red-50
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-500"
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
            </p>

        </div>


        <div
            class="rounded-2xl
                   border
                   border-amber-100
                   bg-amber-50
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-500"
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
            </p>

        </div>


        <div
            class="rounded-2xl
                   border
                   border-violet-100
                   bg-violet-50
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-500"
            >
                Not Marked
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-violet-600"
            >
                {{ $notMarkedCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Needs action
            </p>

        </div>

    </div>



    {{-- =========================================================
        WHOLE DAY ATTENDANCE
    ========================================================== --}}

    <section
        class="overflow-hidden
               rounded-2xl
               border
               border-slate-200
               bg-white
               shadow-sm"
    >

        <div
            class="flex
                   flex-col
                   gap-4
                   border-b
                   border-slate-100
                   px-6 py-5
                   lg:flex-row
                   lg:items-center
                   lg:justify-between"
        >

            <div>

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    All Students
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Each student appears once.
                    Their classes for this day are shown beside their name.
                </p>

            </div>


            @if ($attendanceRows->isNotEmpty())

                <button
                    id="markAllPresentButton"
                    type="button"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           border-2
                           border-green-500
                           bg-green-50
                           px-5
                           text-sm
                           font-semibold
                           text-green-700
                           hover:bg-green-100"
                >
                    Mark All Present
                </button>

            @endif

        </div>


        @if (
            $selectedDay
            &&
            $selectedDate
        )

            <form
                method="POST"
                action="{{ route(
                    'admin.attendance.store-day',
                    [
                        'day' =>
                            $selectedDay->id,
                    ]
                ) }}"
            >

                @csrf


                <input
                    type="hidden"
                    name="attendance_date"
                    value="{{ $selectedDate->toDateString() }}"
                >


                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead
                            class="border-b
                                   border-slate-200
                                   bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wide
                                           text-slate-500"
                                >
                                    Student
                                </th>


                                <th
                                    class="px-6 py-4
                                           text-left
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wide
                                           text-slate-500"
                                >
                                    Classes
                                </th>


                                <th
                                    class="px-6 py-4
                                           text-left
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wide
                                           text-slate-500"
                                >
                                    Time
                                </th>


                                <th
                                    class="px-6 py-4
                                           text-center
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wide
                                           text-slate-500"
                                >
                                    Attendance
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y
                                   divide-slate-100"
                        >

                            @forelse (
                                $attendanceRows
                                as $row
                            )

                                @php

                                    $student =
                                        $row[
                                            'student'
                                        ];


                                    $studentId =
                                        $row[
                                            'student_id'
                                        ];


                                    $status =
                                        old(
                                            'attendance.'
                                            .
                                            $studentId,
                                            $row[
                                                'status'
                                            ]
                                        );


                                    $isVacation =
                                        $row[
                                            'is_vacation'
                                        ];

                                @endphp


                                <tr
                                    class="{{
                                        $isVacation
                                            ? 'bg-amber-50/60'
                                            : 'hover:bg-slate-50/70'
                                    }}"
                                >

                                    <td
                                        class="whitespace-nowrap
                                               px-6 py-5"
                                    >

                                        <p
                                            class="font-semibold
                                                   text-slate-900"
                                        >
                                            {{ $student->first_name }}
                                            {{ $student->last_name }}
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-slate-500"
                                        >
                                            ID:
                                            {{ $student->external_id ?: 'Not added yet' }}
                                        </p>

                                    </td>


                                    <td
                                        class="px-6 py-5"
                                    >

                                        <div
                                            class="flex
                                                   flex-wrap
                                                   gap-2"
                                        >

                                            @foreach (
                                                $row[
                                                    'classes'
                                                ]
                                                as $className
                                            )

                                                <span
                                                    class="inline-flex
                                                           rounded-lg
                                                           border
                                                           border-slate-300
                                                           bg-slate-50
                                                           px-2.5 py-1
                                                           text-xs
                                                           font-semibold
                                                           text-slate-700"
                                                >
                                                    {{ $className }}
                                                </span>

                                            @endforeach

                                        </div>

                                    </td>


                                    <td
                                        class="px-6 py-5
                                               text-sm
                                               text-slate-600"
                                    >
                                        {{
                                            $row[
                                                'times'
                                            ]
                                                ->implode(
                                                    ', '
                                                )
                                        }}
                                    </td>


                                    <td
                                        class="px-6 py-5"
                                    >

                                        @if ($isVacation)

                                            <div
                                                class="flex
                                                       justify-center"
                                            >

                                                <span
                                                    class="inline-flex
                                                           items-center
                                                           rounded-full
                                                           border
                                                           border-amber-300
                                                           bg-amber-100
                                                           px-4 py-2
                                                           text-xs
                                                           font-bold
                                                           text-amber-800"
                                                >
                                                    Vacation
                                                </span>

                                            </div>

                                        @else

                                            <div
                                                class="flex
                                                       flex-wrap
                                                       justify-center
                                                       gap-3"
                                            >

                                                <label
                                                    class="inline-flex
                                                           cursor-pointer
                                                           items-center
                                                           gap-2
                                                           rounded-xl
                                                           border-2
                                                           px-4 py-2.5
                                                           transition
                                                        {{
                                                            $status
                                                            ===
                                                            'present'

                                                                ? 'border-green-600 bg-green-50 text-green-700'

                                                                : 'border-slate-400 bg-white text-slate-700'
                                                        }}"
                                                >

                                                    <input
                                                        type="radio"
                                                        name="attendance[{{ $studentId }}]"
                                                        value="present"
                                                        @checked(
                                                            $status
                                                            ===
                                                            'present'
                                                        )
                                                        class="attendance-present
                                                               h-5 w-5
                                                               border-2
                                                               border-slate-500
                                                               text-green-600
                                                               focus:ring-green-500"
                                                        required
                                                    >

                                                    <span
                                                        class="text-sm
                                                               font-semibold"
                                                    >
                                                        Present
                                                    </span>

                                                </label>


                                                <label
                                                    class="inline-flex
                                                           cursor-pointer
                                                           items-center
                                                           gap-2
                                                           rounded-xl
                                                           border-2
                                                           px-4 py-2.5
                                                           transition
                                                        {{
                                                            $status
                                                            ===
                                                            'absent'

                                                                ? 'border-red-600 bg-red-50 text-red-700'

                                                                : 'border-slate-400 bg-white text-slate-700'
                                                        }}"
                                                >

                                                    <input
                                                        type="radio"
                                                        name="attendance[{{ $studentId }}]"
                                                        value="absent"
                                                        @checked(
                                                            $status
                                                            ===
                                                            'absent'
                                                        )
                                                        class="h-5 w-5
                                                               border-2
                                                               border-slate-500
                                                               text-red-600
                                                               focus:ring-red-500"
                                                        required
                                                    >

                                                    <span
                                                        class="text-sm
                                                               font-semibold"
                                                    >
                                                        Absent
                                                    </span>

                                                </label>

                                            </div>

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="px-6 py-16
                                               text-center
                                               text-sm
                                               text-slate-500"
                                    >
                                        No students are scheduled for this day.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if ($attendanceRows->isNotEmpty())

                    <div
                        class="flex
                               justify-end
                               border-t
                               border-slate-100
                               bg-slate-50/60
                               px-6 py-5"
                    >

                        <button
                            type="submit"
                            class="inline-flex
                                   h-12
                                   min-w-48
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-600
                                   px-6
                                   text-sm
                                   font-bold
                                   text-white
                                   shadow-sm
                                   transition
                                   hover:bg-blue-700"
                        >
                            Save Attendance
                        </button>

                    </div>

                @endif

            </form>

        @endif

    </section>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const markAllPresentButton =
            document.getElementById(
                'markAllPresentButton'
            );


        if (!markAllPresentButton) {

            return;
        }


        markAllPresentButton.addEventListener(
            'click',
            function () {

                const presentRadios =
                    document.querySelectorAll(
                        '.attendance-present'
                    );


                presentRadios.forEach(
                    function (radio) {

                        radio.checked =
                            true;
                    }
                );
            }
        );

    }
);

</script>

@endpush
