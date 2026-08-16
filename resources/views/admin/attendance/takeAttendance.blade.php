@extends('layouts.admin')

@section('title', 'Take Attendance')

@section('page-title', 'Take Attendance')


@php

    $breadcrumbs = [
        [
            'label' => 'Attendance',
            'url' => route(
                'admin.attendance.index',
                [
                    'day_id' =>
                        $sectionOffering->day_id,
                ]
            ),
        ],
        [
            'label' => 'Take Attendance',
            'url' => null,
        ],
    ];

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        BACK
    ========================================================== --}}

    <div>

        <a
            href="{{ route(
                'admin.attendance.index',
                [
                    'day_id' =>
                        $sectionOffering->day_id,
                ]
            ) }}"
            class="inline-flex
                   items-center
                   gap-2
                   text-sm
                   font-semibold
                   text-blue-600
                   hover:text-blue-800"
        >
            ← Back to Attendance
        </a>

    </div>



    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section
        class="rounded-2xl
               border
               border-blue-100
               bg-blue-50
               p-6"
    >

        <p
            class="text-xs
                   font-semibold
                   uppercase
                   tracking-wide
                   text-blue-600"
        >
            Take Attendance
        </p>


        <h1
            class="mt-2
                   text-3xl
                   font-bold
                   text-slate-900"
        >
            {{
                $sectionOffering
                    ->section
                    ?->section_name
                ?? 'Class'
            }}
        </h1>


        <div
            class="mt-3
                   flex flex-wrap
                   items-center
                   gap-2
                   text-sm
                   text-slate-600"
        >

            <span>
                {{
                    $attendanceDate
                        ->format(
                            'l, j F Y'
                        )
                }}
            </span>


            <span class="text-slate-300">
                •
            </span>


            <span>

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

            </span>


            <span class="text-slate-300">
                •
            </span>


            <span>
                {{ $enrolments->count() }}

                {{
                    $enrolments->count() === 1
                        ? 'student'
                        : 'students'
                }}
            </span>

        </div>

    </section>



    {{-- =========================================================
        SUCCESS
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



    {{-- =========================================================
        ERRORS
    ========================================================== --}}

    @if ($errors->any())

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            <p
                class="font-semibold
                       text-red-700"
            >
                Please mark attendance
                for every student.
            </p>

        </div>

    @endif



    {{-- =========================================================
        FORM
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route(
            'admin.attendance.store',
            $sectionOffering
        ) }}"
        id="attendanceForm"
    >

        @csrf


        <input
            type="hidden"
            name="attendance_date"
            value="{{
                $attendanceDate
                    ->toDateString()
            }}"
        >



        <section
            class="overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >


            {{-- Header --}}
            <div
                class="flex flex-col
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
                        Student Attendance List
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        Select Present, Absent
                        or Vacation for each student.
                    </p>

                </div>



                <div
                    class="flex flex-wrap
                           gap-2"
                >

                    <button
                        type="button"
                        onclick="markAll('present')"
                        class="rounded-xl
                               border
                               border-green-300
                               bg-green-50
                               px-4 py-2
                               text-xs
                               font-semibold
                               text-green-700"
                    >
                        Mark All Present
                    </button>


                    <button
                        type="button"
                        onclick="markAll('absent')"
                        class="rounded-xl
                               border
                               border-red-300
                               bg-red-50
                               px-4 py-2
                               text-xs
                               font-semibold
                               text-red-600"
                    >
                        Mark All Absent
                    </button>


                    <button
                        type="button"
                        onclick="markAll('vacation')"
                        class="rounded-xl
                               border
                               border-amber-300
                               bg-amber-50
                               px-4 py-2
                               text-xs
                               font-semibold
                               text-amber-700"
                    >
                        Mark All Vacation
                    </button>

                </div>

            </div>



            {{-- Table --}}
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
                                #
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Student
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Guardian
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Status
                            </th>


                            <th
                                class="px-5 py-4
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Mark Attendance
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse ($enrolments as $enrolment)

                            @php

                                $student =
                                    $enrolment->student;


                                $guardian =
                                    $student
                                        ?->guardians
                                        ?->first(
                                            function (
                                                $guardian
                                            ) {

                                                return
                                                    $guardian
                                                        ->pivot
                                                        ->is_primary;
                                            }
                                        );


                                if (
                                    !$guardian &&
                                    $student
                                ) {

                                    $guardian =
                                        $student
                                            ->guardians
                                            ->first();
                                }


                                $existing =
                                    $attendanceRecords
                                        ->get(
                                            $enrolment->id
                                        );


                                $selectedStatus =
                                    old(
                                        'attendance.'
                                        .
                                        $enrolment->id,
                                        $existing
                                            ?->status
                                    );

                            @endphp


                            <tr>

                                <td
                                    class="px-5 py-5
                                           text-sm
                                           text-slate-500"
                                >
                                    {{ $loop->iteration }}
                                </td>



                                <td
                                    class="px-5 py-5"
                                >

                                    <p
                                        class="font-bold
                                               text-slate-900"
                                    >
                                        {{
                                            $student
                                                ?->first_name
                                        }}

                                        {{
                                            $student
                                                ?->last_name
                                        }}
                                    </p>


                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-400"
                                    >
                                        {{
                                            $student
                                                ?->external_id
                                            ?? '—'
                                        }}
                                    </p>

                                </td>



                                <td
                                    class="px-5 py-5
                                           text-sm
                                           text-slate-600"
                                >

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



                                <td
                                    class="px-5 py-5"
                                >

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-blue-50
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-blue-700"
                                    >

                                        {{
                                            $student
                                                ?->studentStatus
                                                ?->status_name
                                            ?? 'Active'
                                        }}

                                    </span>

                                </td>



                                {{-- Attendance --}}
                                <td
                                    class="px-5 py-5"
                                >

                                    <div
                                        class="flex
                                               justify-center
                                               gap-2"
                                    >


                                        {{-- Present --}}
                                        <label
                                            class="attendance-option
                                                   cursor-pointer"
                                        >

                                            <input
                                                type="radio"
                                                name="attendance[{{ $enrolment->id }}]"
                                                value="present"
                                                class="peer sr-only"
                                                @checked(
                                                    $selectedStatus
                                                    ===
                                                    'present'
                                                )
                                            >


                                            <span
                                                class="inline-flex
                                                       min-w-[100px]
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       border
                                                       border-slate-200
                                                       bg-white
                                                       px-4 py-2.5
                                                       text-xs
                                                       font-semibold
                                                       text-slate-600
                                                       transition
                                                       peer-checked:border-green-500
                                                       peer-checked:bg-green-100
                                                       peer-checked:text-green-700"
                                            >
                                                Present
                                            </span>

                                        </label>



                                        {{-- Absent --}}
                                        <label
                                            class="attendance-option
                                                   cursor-pointer"
                                        >

                                            <input
                                                type="radio"
                                                name="attendance[{{ $enrolment->id }}]"
                                                value="absent"
                                                class="peer sr-only"
                                                @checked(
                                                    $selectedStatus
                                                    ===
                                                    'absent'
                                                )
                                            >


                                            <span
                                                class="inline-flex
                                                       min-w-[100px]
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       border
                                                       border-slate-200
                                                       bg-white
                                                       px-4 py-2.5
                                                       text-xs
                                                       font-semibold
                                                       text-slate-600
                                                       transition
                                                       peer-checked:border-red-500
                                                       peer-checked:bg-red-500
                                                       peer-checked:text-white"
                                            >
                                                Absent
                                            </span>

                                        </label>



                                        {{-- Vacation --}}
                                        <label
                                            class="attendance-option
                                                   cursor-pointer"
                                        >

                                            <input
                                                type="radio"
                                                name="attendance[{{ $enrolment->id }}]"
                                                value="vacation"
                                                class="peer sr-only"
                                                @checked(
                                                    $selectedStatus
                                                    ===
                                                    'vacation'
                                                )
                                            >


                                            <span
                                                class="inline-flex
                                                       min-w-[100px]
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       border
                                                       border-slate-200
                                                       bg-white
                                                       px-4 py-2.5
                                                       text-xs
                                                       font-semibold
                                                       text-slate-600
                                                       transition
                                                       peer-checked:border-amber-500
                                                       peer-checked:bg-amber-500
                                                       peer-checked:text-white"
                                            >
                                                Vacation
                                            </span>

                                        </label>

                                    </div>

                                </td>

                            </tr>


                        @empty


                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-14
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >
                                    No confirmed students
                                    are enrolled in this class.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>



            {{-- Buttons --}}
            @if ($enrolments->isNotEmpty())

                <div
                    class="flex
                           justify-end
                           border-t
                           border-slate-100
                           bg-slate-50/50
                           px-6 py-5"
                >

                    <button
                        type="submit"
                        class="inline-flex
                               h-12
                               min-w-[180px]
                               items-center
                               justify-center
                               rounded-xl
                               bg-blue-600
                               px-6
                               text-sm
                               font-semibold
                               text-white
                               shadow-sm
                               transition
                               hover:bg-blue-700"
                    >
                        Save Attendance
                    </button>

                </div>

            @endif

        </section>

    </form>

</div>

@endsection



@push('scripts')

<script>

function markAll(status) {

    const radios =
        document.querySelectorAll(
            'input[type="radio"][value="'
            +
            status
            +
            '"]'
        );


    radios.forEach(
        function (radio) {

            radio.checked =
                true;
        }
    );
}


/*
|--------------------------------------------------------------------------
| Make sure every student is marked
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const form =
            document.getElementById(
                'attendanceForm'
            );


        if (!form) {
            return;
        }


        form.addEventListener(
            'submit',
            function (event) {

                const groups =
                    new Set();


                document
                    .querySelectorAll(
                        'input[type="radio"][name^="attendance["]'
                    )
                    .forEach(
                        function (radio) {

                            groups.add(
                                radio.name
                            );
                        }
                    );


                for (
                    const groupName
                    of groups
                ) {

                    const checked =
                        document.querySelector(
                            'input[name="'
                            +
                            groupName
                            +
                            '"]:checked'
                        );


                    if (!checked) {

                        event.preventDefault();


                        alert(
                            'Please mark attendance for every student.'
                        );


                        return;
                    }
                }
            }
        );

    }
);

</script>

@endpush
