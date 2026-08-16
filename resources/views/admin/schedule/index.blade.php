@extends('layouts.admin')

@section('title', 'Schedule')

@section('page-title', 'Schedule')


@php

    $breadcrumbs = [
        [
            'label' => 'Schedule',
            'url' => null,
        ],
    ];

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div
        class="flex flex-col gap-4
               lg:flex-row
               lg:items-end
               lg:justify-between"
    >

        <div>

            <h1
                class="text-4xl font-bold
                       tracking-tight
                       text-slate-900"
            >
                Class Schedule
            </h1>

            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                View daily classes and
                student lists.
            </p>

        </div>


        <div
            class="flex flex-wrap gap-3"
        >

            @if (
                auth()->user()
                    ->hasPermission(
                        'section_offerings.view'
                    )
            )

                <a
                    href="{{ route(
                        'admin.section-offerings.index'
                    ) }}"
                    class="inline-flex h-11
                           items-center
                           justify-center
                           rounded-xl border
                           border-blue-300
                           bg-white px-5
                           text-sm font-semibold
                           text-blue-700
                           hover:bg-blue-50"
                >
                    Manage Class Offerings
                </a>

            @endif


            @if (
                $selectedDay &&
                auth()->user()
                    ->hasPermission(
                        'enrolments.print'
                    )
            )

                <a
                    href="{{ route(
                        'admin.schedule.print-day',
                        [
                            'day_id' =>
                                $selectedDay->id,
                        ]
                    ) }}"
                    target="_blank"
                    class="inline-flex h-11
                           items-center
                           justify-center
                           gap-2 rounded-xl
                           bg-blue-600
                           px-5
                           text-sm font-semibold
                           text-white
                           hover:bg-blue-700"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-4 w-4"
                    >
                        <path
                            d="M6 9V3h12v6
                               M6 18H4a2 2 0 0 1-2-2v-5
                               a2 2 0 0 1 2-2h16
                               a2 2 0 0 1 2 2v5
                               a2 2 0 0 1-2 2h-2
                               M6 14h12v7H6z"
                        />
                    </svg>

                    Print Whole Day

                </a>

            @endif

        </div>

    </div>



    {{-- =========================================================
        DAY SELECTOR
    ========================================================== --}}
    @if ($days->isNotEmpty())

        <div
            class="flex flex-wrap
                   gap-3
                   rounded-2xl
                   border border-slate-200
                   bg-white p-3
                   shadow-sm"
        >

            @foreach ($days as $day)

                @php

                    $date =
                        $dayDates[
                            $day->id
                        ];

                    $isToday =
                        $date->isToday();

                @endphp


                <a
                    href="{{ route(
                        'admin.schedule.index',
                        [
                            'day_id' =>
                                $day->id,
                        ]
                    ) }}"
                    class="min-w-32
                           rounded-xl
                           border px-5 py-3
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
                        @if ($isToday)

                            Today

                        @else

                            {{ $day->day_name }}

                        @endif
                    </p>


                    <p
                        class="mt-1 text-xs
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
                        {{
                            $date->format(
                                'M j'
                            )
                        }}
                    </p>

                </a>

            @endforeach

        </div>

    @endif



    {{-- =========================================================
        NO DAYS
    ========================================================== --}}
    @if (!$selectedDay)

        <div
            class="rounded-2xl
                   border border-slate-200
                   bg-white
                   p-12 text-center"
        >
            <p
                class="font-semibold
                       text-slate-700"
            >
                No active class days configured.
            </p>
        </div>

    @else


        {{-- =====================================================
            SUMMARY CARDS
        ====================================================== --}}
        <div
            class="grid gap-4
                   md:grid-cols-2
                   xl:grid-cols-4"
        >


            {{-- Selected Day --}}
            <div
                class="rounded-2xl
                       border border-slate-200
                       bg-white p-5
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
                                'l, M j, Y'
                            )
                    }}
                </p>

            </div>


            {{-- Classes --}}
            <div
                class="rounded-2xl
                       border border-slate-200
                       bg-white p-5
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
                    Active offerings
                </p>

            </div>


            {{-- Students --}}
            <div
                class="rounded-2xl
                       border border-slate-200
                       bg-white p-5
                       shadow-sm"
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
                    {{ $totalStudents }}
                </p>

                <p
                    class="mt-1
                           text-xs
                           text-slate-400"
                >
                    Unique students
                </p>

            </div>


            {{-- Wishlist --}}
            <div
                class="rounded-2xl
                       border border-slate-200
                       bg-white p-5
                       shadow-sm"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Wishlist
                </p>

                <p
                    class="mt-2
                           text-3xl
                           font-bold
                           text-slate-900"
                >
                    {{ $totalWishlist }}
                </p>

                <p
                    class="mt-1
                           text-xs
                           text-slate-400"
                >
                    Waiting placements
                </p>

            </div>

        </div>



        {{-- =====================================================
            MAIN GRID
        ====================================================== --}}
        <div
            class="grid gap-5
                   xl:grid-cols-[0.95fr_1.05fr]"
        >


            {{-- =================================================
                LEFT SIDE
            ================================================== --}}
            <div class="space-y-5">


                {{-- =============================================
                    TIME TABLE
                ============================================== --}}
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
                            class="text-lg
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
                            Select a time to view
                            scheduled classes.
                        </p>

                    </div>


                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="bg-slate-50">

                                <tr>

                                    <th
                                        class="px-5 py-3
                                               text-left
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Time
                                    </th>

                                    <th
                                        class="px-5 py-3
                                               text-center
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Classes
                                    </th>

                                    <th
                                        class="px-5 py-3
                                               text-center
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Enrolments
                                    </th>

                                    <th
                                        class="px-5 py-3
                                               text-center
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Wishlist
                                    </th>

                                    <th
                                        class="px-5 py-3
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

                                @forelse ($scheduleRows as $row)

                                    <tr
                                        class="
                                            {{
                                                $selectedTime
                                                ===
                                                $row['raw_time']

                                                    ? 'bg-blue-50'

                                                    : 'hover:bg-slate-50'
                                            }}"
                                    >

                                        <td
                                            class="px-5 py-4
                                                   font-bold
                                                   text-slate-900"
                                        >
                                            {{ $row['time'] }}

                                            @if (
                                                $selectedDate
                                                    ?->isToday() &&
                                                now()->format(
                                                    'H:i'
                                                )
                                                ===
                                                \Carbon\Carbon::parse(
                                                    $row[
                                                        'raw_time'
                                                    ]
                                                )->format(
                                                    'H:i'
                                                )
                                            )

                                                <span
                                                    class="ml-2
                                                           rounded-full
                                                           bg-blue-600
                                                           px-2 py-1
                                                           text-[10px]
                                                           text-white"
                                                >
                                                    Current
                                                </span>

                                            @endif

                                        </td>


                                        <td
                                            class="px-5 py-4
                                                   text-center
                                                   text-sm
                                                   text-slate-700"
                                        >
                                            {{
                                                $row[
                                                    'class_count'
                                                ]
                                            }}
                                        </td>


                                        <td
                                            class="px-5 py-4
                                                   text-center
                                                   text-sm
                                                   font-semibold
                                                   text-slate-700"
                                        >
                                            {{
                                                $row[
                                                    'enrolment_count'
                                                ]
                                            }}
                                        </td>


                                        <td
                                            class="px-5 py-4
                                                   text-center
                                                   text-sm
                                                   text-purple-600"
                                        >
                                            {{
                                                $row[
                                                    'wishlist_count'
                                                ]
                                            }}
                                        </td>


                                        <td
                                            class="px-5 py-4
                                                   text-center"
                                        >

                                            <a
                                                href="{{ route(
                                                    'admin.schedule.index',
                                                    [
                                                        'day_id' =>
                                                            $selectedDay->id,

                                                        'time' =>
                                                            $row[
                                                                'raw_time'
                                                            ],
                                                    ]
                                                ) }}"
                                                class="inline-flex
                                                       rounded-lg
                                                       border
                                                       border-blue-300
                                                       px-4 py-2
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
                                            colspan="5"
                                            class="px-6 py-12
                                                   text-center
                                                   text-sm
                                                   text-slate-500"
                                        >
                                            No classes scheduled
                                            for this day.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>



                {{-- =============================================
                    CLASSES AT SELECTED TIME
                ============================================== --}}
                @if ($selectedTime)

                    <section>

                        <div class="mb-3">

                            <h2
                                class="text-lg
                                       font-bold
                                       text-slate-900"
                            >
                                Classes at
                                {{
                                    \Carbon\Carbon::parse(
                                        $selectedTime
                                    )->format(
                                        'g:i A'
                                    )
                                }}
                            </h2>

                        </div>


                        <div
                            class="grid gap-4
                                   md:grid-cols-2"
                        >

                            @forelse ($timeOfferings as $offering)

                                <div
                                    class="rounded-2xl
                                           border
                                           bg-white p-5
                                           shadow-sm
                                        {{
                                            $selectedOffering &&
                                            (int)
                                            $selectedOffering->id
                                            ===
                                            (int)
                                            $offering->id

                                                ? 'border-blue-500 ring-2 ring-blue-100'

                                                : 'border-slate-200'
                                        }}"
                                >

                                    <a
                                        href="{{ route(
                                            'admin.schedule.index',
                                            [
                                                'day_id' =>
                                                    $selectedDay->id,

                                                'time' =>
                                                    $selectedTime,

                                                'offering_id' =>
                                                    $offering->id,
                                            ]
                                        ) }}"
                                        class="block"
                                    >

                                        <p
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
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-sm
                                                   text-slate-500"
                                        >
                                            {{
                                                $offering
                                                    ->allocated_seats
                                            }}
                                            /
                                            {{
                                                $offering
                                                    ->max_seats
                                            }}
                                            students
                                        </p>

                                    </a>


                                    <div
                                        class="mt-4
                                               flex
                                               flex-wrap
                                               gap-2"
                                    >

                                        @if (
                                            auth()->user()
                                                ->hasPermission(
                                                    'students.view'
                                                )
                                        )

                                            <a
                                                href="{{ route(
                                                    'admin.schedule.class-students',
                                                    $offering
                                                ) }}"
                                                class="inline-flex
                                                       flex-1
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       border
                                                       border-blue-300
                                                       px-3 py-2.5
                                                       text-xs
                                                       font-semibold
                                                       text-blue-700
                                                       hover:bg-blue-50"
                                            >
                                                Student List
                                            </a>

                                        @endif


                                        @if (
                                            auth()->user()
                                                ->hasPermission(
                                                    'enrolments.print'
                                                )
                                        )

                                            <a
                                                href="{{ route(
                                                    'admin.schedule.class-students.print',
                                                    $offering
                                                ) }}"
                                                target="_blank"
                                                class="inline-flex
                                                       flex-1
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       bg-blue-600
                                                       px-3 py-2.5
                                                       text-xs
                                                       font-semibold
                                                       text-white
                                                       hover:bg-blue-700"
                                            >
                                                Print List
                                            </a>

                                        @endif

                                    </div>

                                </div>

                            @empty

                                <div
                                    class="col-span-full
                                           rounded-xl
                                           border
                                           border-slate-200
                                           bg-white
                                           p-8
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >
                                    No classes at this time.
                                </div>

                            @endforelse

                        </div>

                    </section>

                @endif

            </div>



            {{-- =================================================
                RIGHT SIDE - STUDENT PREVIEW
            ================================================== --}}
            <section
                class="overflow-hidden
                       rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       shadow-sm"
            >

                @if ($selectedOffering)

                    <div
                        class="border-b
                               border-slate-100
                               p-6"
                    >

                        <div
                            class="flex flex-col
                                   gap-4
                                   sm:flex-row
                                   sm:items-center
                                   sm:justify-between"
                        >

                            <div>

                                <p
                                    class="text-xs
                                           font-semibold
                                           uppercase
                                           tracking-wide
                                           text-slate-400"
                                >
                                    Selected Class
                                </p>

                                <h2
                                    class="mt-1
                                           text-2xl
                                           font-bold
                                           text-slate-900"
                                >
                                    {{
                                        $selectedOffering
                                            ->section
                                            ?->section_name
                                        ?? 'Class'
                                    }}
                                </h2>

                                <p
                                    class="mt-2
                                           text-sm
                                           text-slate-500"
                                >
                                    {{
                                        \Carbon\Carbon::parse(
                                            $selectedOffering
                                                ->start_time
                                        )->format(
                                            'g:i A'
                                        )
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::parse(
                                            $selectedOffering
                                                ->end_time
                                        )->format(
                                            'g:i A'
                                        )
                                    }}

                                    ·

                                    {{
                                        $selectedOffering
                                            ->enrolments
                                            ->count()
                                    }}
                                    students
                                </p>

                            </div>


                            <a
                                href="{{ route(
                                    'admin.schedule.class-students',
                                    $selectedOffering
                                ) }}"
                                class="inline-flex
                                       items-center
                                       justify-center
                                       rounded-xl
                                       border
                                       border-blue-300
                                       px-4 py-2.5
                                       text-sm
                                       font-semibold
                                       text-blue-700
                                       hover:bg-blue-50"
                            >
                                Full Student List
                            </a>

                        </div>

                    </div>


                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="bg-slate-50">

                                <tr>

                                    <th
                                        class="px-5 py-3
                                               text-left
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Student
                                    </th>

                                    <th
                                        class="px-5 py-3
                                               text-left
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Guardian
                                    </th>

                                    <th
                                        class="px-5 py-3
                                               text-left
                                               text-xs
                                               font-bold
                                               uppercase
                                               text-slate-500"
                                    >
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody
                                class="divide-y
                                       divide-slate-100"
                            >

                                @forelse ($previewEnrolments as $enrolment)

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

                                    @endphp


                                    <tr>

                                        <td
                                            class="px-5 py-4
                                                   text-sm
                                                   font-semibold
                                                   text-slate-800"
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


                                        <td
                                            class="px-5 py-4
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
                                            class="px-5 py-4"
                                        >

                                            <span
                                                class="rounded-full
                                                       bg-green-100
                                                       px-3 py-1
                                                       text-xs
                                                       font-semibold
                                                       text-green-700"
                                            >
                                                {{
                                                    $student
                                                        ?->studentStatus
                                                        ?->status_name
                                                    ?? 'Active'
                                                }}
                                            </span>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="3"
                                            class="px-5 py-12
                                                   text-center
                                                   text-sm
                                                   text-slate-500"
                                        >
                                            No students allocated
                                            to this class.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    @if (
                        $selectedOffering
                            ->enrolments
                            ->count()
                        >
                        $previewEnrolments
                            ->count()
                    )

                        <div
                            class="border-t
                                   border-slate-100
                                   px-6 py-4"
                        >
                            <a
                                href="{{ route(
                                    'admin.schedule.class-students',
                                    $selectedOffering
                                ) }}"
                                class="text-sm
                                       font-semibold
                                       text-blue-600
                                       hover:text-blue-800"
                            >
                                +
                                {{
                                    $selectedOffering
                                        ->enrolments
                                        ->count()
                                    -
                                    $previewEnrolments
                                        ->count()
                                }}
                                more students
                            </a>
                        </div>

                    @endif


                @else

                    <div
                        class="flex min-h-[400px]
                               items-center
                               justify-center
                               p-8 text-center"
                    >

                        <div>

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
                                Select a class time
                                to view students.
                            </p>

                        </div>

                    </div>

                @endif

            </section>

        </div>

    @endif

</div>

@endsection
