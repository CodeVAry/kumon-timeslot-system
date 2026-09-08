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


    {{-- =====================================================
        BACK
    ====================================================== --}}

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
                   text-blue-600"
        >
            ← Back to Class Schedule
        </a>

    </div>



    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <section
        class="rounded-2xl
               border border-blue-100
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
            Centre Schedule Export
        </h1>


        <p
            class="mt-2
                   max-w-3xl
                   text-sm
                   text-slate-600"
        >
            Generate the centre schedule using
            the client printing format.
        </p>

    </section>



    {{-- =====================================================
        ERRORS
    ====================================================== --}}

    @if ($errors->any())

        <div
            class="rounded-xl
                   border border-red-200
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



    {{-- =====================================================
        EXPORT FORM
    ====================================================== --}}

    <form
        method="POST"
        action="{{ route(
            'admin.schedule.export'
        ) }}"
        class="rounded-2xl
               border border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        @csrf


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

        </div>



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
                    onchange="
                        changeDay(
                            this.value
                        )
                    "
                    required
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300"
                >

                    @foreach (
                        $days
                        as $day
                    )

                        <option
                            value="{{
                                $day->id
                            }}"
                            @selected(
                                old(
                                    'day_id',
                                    $selectedDay?->id
                                )
                                ==
                                $day->id
                            )
                        >

                            {{
                                $day
                                    ->day_name
                            }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                CLASS
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
                        All Classes
                    </option>


                    @foreach (
                        $sections
                        as $section
                    )

                        <option
                            value="{{
                                $section->id
                            }}"
                            @selected(
                                old(
                                    'section_id'
                                )
                                ==
                                $section->id
                            )
                        >

                            {{
                                $section
                                    ->section_name
                            }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                MATH SUB-SECTION
            ================================================== --}}

            <div>

                <label
                    for="sub_section_id"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Math Sub-section
                </label>


                <select
                    id="sub_section_id"
                    name="sub_section_id"
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300"
                >

                    <option value="">
                        All Sub-sections
                    </option>


                    @foreach (
                        $subSections
                        as $subSection
                    )

                        <option
                            value="{{
                                $subSection->id
                            }}"
                            @selected(
                                old(
                                    'sub_section_id'
                                )
                                ==
                                $subSection->id
                            )
                        >

                            {{
                                $subSection
                                    ->section
                                    ?->section_name
                            }}

                            —

                            {{
                                $subSection
                                    ->sub_section_name
                            }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                CLASS TIME
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
                           border-slate-300"
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
                                old(
                                    'time'
                                )
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
                           border-slate-300"
                >

                    <option value="">
                        All Matching Classes
                    </option>


                    @foreach (
                        $offerings
                        as $offering
                    )

                        <option
                            value="{{
                                $offering->id
                            }}"
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


                            @if (
                                $offering
                                    ->subSections
                                    ->isNotEmpty()
                            )

                                (

                                {{
                                    $offering
                                        ->subSections
                                        ->pluck(
                                            'sub_section_name'
                                        )
                                        ->implode(
                                            ', '
                                        )
                                }}

                                )

                            @endif


                            —

                            {{
                                \Carbon\Carbon::parse(
                                    $offering
                                        ->start_time
                                )->format(
                                    'g:i A'
                                )
                            }}

                        </option>

                    @endforeach

                </select>

            </div>



            {{-- =================================================
                STUDENT FILTER
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
                    Student Filter
                </label>


                <select
                    id="attendance_status"
                    name="attendance_status"
                    required
                    class="h-12
                           w-full
                           rounded-xl
                           border-slate-300"
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


            <div
                class="mt-4
                       grid
                       gap-4
                       md:grid-cols-2"
            >


                {{-- PDF --}}

                <label class="cursor-pointer">

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
                               p-5
                               peer-checked:border-red-500
                               peer-checked:bg-red-50"
                    >

                        <strong>
                            PDF
                        </strong>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            Client-style printable schedule.
                        </p>

                    </div>

                </label>



                {{-- Excel --}}

                <label class="cursor-pointer">

                    <input
                        type="radio"
                        name="format"
                        value="excel"
                        class="peer sr-only"

                        @checked(
                            old(
                                'format'
                            )
                            ===
                            'excel'
                        )
                    >


                    <div
                        class="rounded-2xl
                               border
                               border-slate-200
                               p-5
                               peer-checked:border-green-500
                               peer-checked:bg-green-50"
                    >

                        <strong>
                            Excel (.xlsx)
                        </strong>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            Formatted Excel file with vertical
                            time and status-coloured student names.
                        </p>

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
                       rounded-xl
                       border
                       border-slate-300
                       px-6
                       text-sm
                       font-semibold
                       text-slate-700"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="inline-flex
                       h-12
                       items-center
                       rounded-xl
                       bg-blue-600
                       px-8
                       text-sm
                       font-semibold
                       text-white"
            >
                Generate Export
            </button>

        </div>

    </form>

</div>

@endsection



@push('scripts')

<script>

function changeDay(dayId)
{
    const url =
        new URL(
            window.location.href
        );


    url.searchParams.set(
        'day_id',
        dayId
    );


    url.searchParams.delete(
        'offering_id'
    );


    window.location.href =
        url.toString();
}

</script>

@endpush
