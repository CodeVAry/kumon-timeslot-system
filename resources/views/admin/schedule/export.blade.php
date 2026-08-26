@extends('layouts.admin')

@section('title', 'Print / Export')

@section('page-title', 'Print / Export')


@php

    $breadcrumbs = [
        [
            'label' => 'Schedule',
            'url' => route(
                'admin.schedule.index',
                [
                    'day_id' =>
                        $selectedDay?->id,
                ]
            ),
        ],
        [
            'label' => 'Print / Export',
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
                'admin.schedule.index',
                [
                    'day_id' =>
                        $selectedDay?->id,
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
            ← Back to Class Schedule
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
                   font-bold
                   uppercase
                   tracking-wide
                   text-blue-600"
        >
            Print / Export
        </p>


        <h1
            class="mt-2
                   text-3xl
                   font-bold
                   text-slate-900"
        >
            Class List Export
        </h1>


        <p
            class="mt-2
                   max-w-3xl
                   text-sm
                   text-slate-600"
        >
            Select any combination of day, class,
            time and attendance status, then generate
            a PDF or Excel-compatible file.
        </p>

    </section>



    {{-- =========================================================
        VALIDATION ERRORS
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
                Please check the selected options.
            </p>


            <ul
                class="mt-2
                       list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600"
            >

                @foreach (
                    $errors->all()
                    as $error
                )

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
        EXPORT FORM
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route(
            'admin.schedule.export'
        ) }}"
        class="rounded-2xl
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        @csrf


        {{-- =====================================================
            FILTER TITLE
        ====================================================== --}}

        <div
            class="mb-6
                   border-b
                   border-slate-100
                   pb-5"
        >

            <h2
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                Export Filters
            </h2>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Leave a filter as “All” if you want
                to include every matching record.
            </p>

        </div>



        {{-- =====================================================
            FILTER GRID
        ====================================================== --}}

        <div
            class="grid
                   gap-6
                   md:grid-cols-2
                   xl:grid-cols-3"
        >


            {{-- =================================================
                DAY
            ================================================== --}}

            <div>

                <label
                    for="day_id"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Day
                </label>


                <select
                    id="day_id"
                    name="day_id"
                    onchange="changeDay(this.value)"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300
                           text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                    required
                >

                    @foreach (
                        $days
                        as $day
                    )

                        <option
                            value="{{ $day->id }}"
                            @selected(
                                old(
                                    'day_id',
                                    $selectedDay?->id
                                )
                                ==
                                $day->id
                            )
                        >
                            {{ $day->day_name }}
                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                DATE
            ================================================== --}}

            <div>

                <label
                    for="date"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Date
                </label>


                <input
                    id="date"
                    type="date"
                    name="date"
                    value="{{
                        old(
                            'date',
                            $selectedDate
                                ?->toDateString()
                        )
                    }}"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300
                           text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                    required
                >

            </div>



            {{-- =================================================
                SUBJECT / CLASS
            ================================================== --}}

            <div>

                <label
                    for="section_id"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Subject / Class
                </label>


                <select
                    id="section_id"
                    name="section_id"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300
                           text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                >

                    <option value="">
                        All Subjects / Classes
                    </option>


                    @foreach (
                        $sections
                        as $section
                    )

                        <option
                            value="{{ $section->id }}"
                            @selected(
                                old('section_id')
                                ==
                                $section->id
                            )
                        >
                            {{ $section->section_name }}
                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                TIME
            ================================================== --}}

            <div>

                <label
                    for="time"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Class Time
                </label>


                <select
                    id="time"
                    name="time"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300
                           text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                >

                    <option value="">
                        All Times
                    </option>


                    @foreach (
                        $times
                        as $time
                    )

                        <option
                            value="{{ $time }}"
                            @selected(
                                old('time')
                                ==
                                $time
                            )
                        >

                            {{
                                \Carbon\Carbon::parse(
                                    $time
                                )->format(
                                    'g:i A'
                                )
                            }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                SPECIFIC CLASS
            ================================================== --}}

            <div>

                <label
                    for="offering_id"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Specific Class
                </label>


                <select
                    id="offering_id"
                    name="offering_id"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300
                           text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                >

                    <option value="">
                        All Matching Classes
                    </option>


                    @foreach (
                        $offerings
                        as $offering
                    )

                        <option
                            value="{{ $offering->id }}"
                            @selected(
                                old(
                                    'offering_id',
                                    $selectedOfferingId
                                )
                                ==
                                $offering->id
                            )
                        >

                            {{
                                $offering
                                    ->section
                                    ?->section_name
                                ??
                                'Class'
                            }}

                            —

                            {{
                                \Carbon\Carbon::parse(
                                    $offering
                                        ->start_time
                                )->format(
                                    'g:i A'
                                )
                            }}

                            to

                            {{
                                \Carbon\Carbon::parse(
                                    $offering
                                        ->end_time
                                )->format(
                                    'g:i A'
                                )
                            }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                ATTENDANCE STATUS
            ================================================== --}}

            <div>

                <label
                    for="attendance_status"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Attendance
                </label>


                <select
                    id="attendance_status"
                    name="attendance_status"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300
                           text-sm
                           shadow-sm
                           focus:border-blue-500
                           focus:ring-blue-500"
                    required
                >

                    <option
                        value="all"
                        @selected(
                            old(
                                'attendance_status',
                                'all'
                            )
                            ===
                            'all'
                        )
                    >
                        All Students
                    </option>


                    <option
                        value="present"
                        @selected(
                            old(
                                'attendance_status'
                            )
                            ===
                            'present'
                        )
                    >
                        Present Only
                    </option>


                    <option
                        value="absent"
                        @selected(
                            old(
                                'attendance_status'
                            )
                            ===
                            'absent'
                        )
                    >
                        Absent Only
                    </option>


                    <option
                        value="vacation"
                        @selected(
                            old(
                                'attendance_status'
                            )
                            ===
                            'vacation'
                        )
                    >
                        Vacation Only
                    </option>


                    <option
                        value="not_marked"
                        @selected(
                            old(
                                'attendance_status'
                            )
                            ===
                            'not_marked'
                        )
                    >
                        Attendance Not Marked
                    </option>

                </select>

            </div>

        </div>



        {{-- =====================================================
            EXAMPLES
        ====================================================== --}}

        <div
            class="mt-7
                   rounded-xl
                   border
                   border-blue-100
                   bg-blue-50/60
                   px-5 py-4"
        >

            <p
                class="text-sm
                       font-semibold
                       text-blue-900"
            >
                Filter examples
            </p>


            <p
                class="mt-1
                       text-xs
                       leading-5
                       text-blue-700"
            >
                Whole day: leave Subject, Time and Specific Class as All.
                Subject list: choose only Subject.
                Time list: choose only Time.
                One class: choose Specific Class.
                Present/Absent/Vacation lists: choose the Attendance filter.
            </p>

        </div>



        {{-- =====================================================
            EXPORT FORMAT
        ====================================================== --}}

        <div
            class="mt-8
                   border-t
                   border-slate-100
                   pt-6"
        >

            <h2
                class="text-lg
                       font-bold
                       text-slate-900"
            >
                Export Format
            </h2>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Choose how the class list should be generated.
            </p>



            <div
                class="mt-4
                       grid
                       gap-4
                       md:grid-cols-2"
            >


                {{-- =================================================
                    PDF
                ================================================== --}}

                <label
                    class="cursor-pointer"
                >

                    <input
                        type="radio"
                        name="format"
                        value="pdf"
                        class="peer sr-only"
                        @checked(
                            old(
                                'format',
                                'pdf'
                            )
                            ===
                            'pdf'
                        )
                    >


                    <div
                        class="rounded-2xl
                               border
                               border-slate-200
                               bg-white
                               p-5
                               transition
                               hover:border-red-300
                               peer-checked:border-red-500
                               peer-checked:bg-red-50
                               peer-checked:ring-2
                               peer-checked:ring-red-100"
                    >

                        <div
                            class="flex
                                   items-center
                                   gap-3"
                        >

                            <div
                                class="flex
                                       h-11 w-11
                                       items-center
                                       justify-center
                                       rounded-xl
                                       bg-red-100
                                       font-bold
                                       text-red-700"
                            >
                                PDF
                            </div>


                            <div>

                                <p
                                    class="font-bold
                                           text-slate-900"
                                >
                                    PDF
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    Recommended for printing
                                    or sharing.
                                </p>

                            </div>

                        </div>

                    </div>

                </label>



                {{-- =================================================
                    EXCEL / CSV
                ================================================== --}}

                <label
                    class="cursor-pointer"
                >

                    <input
                        type="radio"
                        name="format"
                        value="excel"
                        class="peer sr-only"
                        @checked(
                            old('format')
                            ===
                            'excel'
                        )
                    >


                    <div
                        class="rounded-2xl
                               border
                               border-slate-200
                               bg-white
                               p-5
                               transition
                               hover:border-green-300
                               peer-checked:border-green-500
                               peer-checked:bg-green-50
                               peer-checked:ring-2
                               peer-checked:ring-green-100"
                    >

                        <div
                            class="flex
                                   items-center
                                   gap-3"
                        >

                            <div
                                class="flex
                                       h-11 w-11
                                       items-center
                                       justify-center
                                       rounded-xl
                                       bg-green-100
                                       text-xs
                                       font-bold
                                       text-green-700"
                            >
                                XLS
                            </div>


                            <div>

                                <p
                                    class="font-bold
                                           text-slate-900"
                                >
                                    Excel
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    Downloads a CSV file that
                                    opens directly in Excel.
                                </p>

                            </div>

                        </div>

                    </div>

                </label>

            </div>

        </div>



        {{-- =====================================================
            ACTIONS
        ====================================================== --}}

        <div
            class="mt-8
                   flex
                   flex-wrap
                   justify-end
                   gap-3
                   border-t
                   border-slate-100
                   pt-6"
        >

            <a
                href="{{ route(
                    'admin.schedule.index',
                    [
                        'day_id' =>
                            $selectedDay?->id,
                    ]
                ) }}"
                class="inline-flex
                       h-12
                       items-center
                       justify-center
                       rounded-xl
                       border
                       border-slate-300
                       bg-white
                       px-6
                       text-sm
                       font-semibold
                       text-slate-700
                       hover:bg-slate-50"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="inline-flex
                       h-12
                       items-center
                       justify-center
                       gap-2
                       rounded-xl
                       bg-blue-600
                       px-8
                       text-sm
                       font-semibold
                       text-white
                       shadow-sm
                       hover:bg-blue-700"
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
                        d="M12 3v12m0 0
                           4-4m-4 4-4-4
                           M5 21h14"
                    />
                </svg>

                Generate Export

            </button>

        </div>

    </form>

</div>

@endsection



@push('scripts')

<script>

/*
|--------------------------------------------------------------------------
| Reload Export Page When Day Changes
|--------------------------------------------------------------------------
|
| This refreshes the available times and specific classes for the chosen day.
|--------------------------------------------------------------------------
*/

function changeDay(dayId) {

    const url =
        new URL(
            window.location.href
        );


    url.searchParams.set(
        'day_id',
        dayId
    );


    /*
     * Remove old specific class because
     * it may belong to another day.
     */
    url.searchParams.delete(
        'offering_id'
    );


    window.location.href =
        url.toString();
}

</script>

@endpush
