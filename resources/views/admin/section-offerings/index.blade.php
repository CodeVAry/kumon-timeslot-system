@extends('layouts.admin')

@section('title', 'Class Offering')

@section('page-title', 'Class Offering')


@php

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs
    |--------------------------------------------------------------------------
    */

    $breadcrumbs = [
        [
            'label' => 'Class Setup',
            'url' => null,
        ],
        [
            'label' => 'Class Offering',
            'url' => null,
        ],
    ];



    $selectedSubject = request('subject');

    if (!$selectedSubject) {
        $selectedSubject =
            $viewType === 'interactive'
                ? 'interactive'
                : 'english';
    }

    if (
        !in_array(
            $selectedSubject,
            [
                'english',
                'math',
                'interactive',
            ]
        )
    ) {
        $selectedSubject = 'english';
    }


    /*
    |--------------------------------------------------------------------------
    | Status Helper
    |--------------------------------------------------------------------------
    |
    | 100% filled = Full
    | 80%+ filled = Nearly full
    | Otherwise = Available
    |
    */

    $getStatus = function ($allocated, $maximum) {

        $allocated = (int) $allocated;
        $maximum = (int) $maximum;

        if ($maximum <= 0) {
            return [
                'label' => 'Not configured',
                'class' =>
                    'bg-slate-100 text-slate-600',
                'bar' =>
                    'bg-slate-300',
            ];
        }

        $filledPercentage =
            ($allocated / $maximum) * 100;

        if ($allocated >= $maximum) {
            return [
                'label' => 'Full',
                'class' =>
                    'bg-red-100 text-red-700',
                'bar' =>
                    'bg-red-500',
            ];
        }

        if ($filledPercentage >= 80) {
            return [
                'label' => 'Nearly Full',
                'class' =>
                    'bg-amber-100 text-amber-700',
                'bar' =>
                    'bg-amber-500',
            ];
        }

        return [
            'label' => 'Available',
            'class' =>
                'bg-green-100 text-green-700',
            'bar' =>
                'bg-green-500',
        ];
    };

@endphp


@section('content')


<style>

    @media print {

        .no-print {
            display: none !important;
        }

        .print-container {
            border: none !important;
            box-shadow: none !important;
        }

    }

</style>


<div class="space-y-7">


    {{-- =========================================================
        SUCCESS / ERROR MESSAGES
    ========================================================== --}}

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



    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div
        class="flex flex-col
               gap-5
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
                Class Offering
            </h1>


        </div>


        <div
            class="no-print
                   flex flex-wrap
                   items-center
                   gap-3"
        >

            {{-- Add Offering --}}
            @if (
                auth()->user()
                    ->hasPermission(
                        'section_offerings.create'
                    )
            )

                <a
                    href="{{ route(
                        'admin.section-offerings.create'
                    ) }}"
                    class="inline-flex
                           h-12
                           items-center
                           justify-center
                           gap-2
                           rounded-xl
                           border
                           border-blue-300
                           bg-white
                           px-6
                           text-sm
                           font-semibold
                           text-blue-700
                           shadow-sm
                           transition
                           hover:bg-blue-50"
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
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15"
                        />
                    </svg>

                    Add Class Offering

                </a>

            @endif


            {{-- Print --}}
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex
                       h-12
                       items-center
                       justify-center
                       gap-2
                       rounded-xl
                       bg-blue-600
                       px-7
                       text-sm
                       font-semibold
                       text-white
                       shadow-sm
                       transition
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
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6.72 13.829h10.56
                           M6 18h12v-6H6v6Zm1.5-9V4.5h9V9"
                    />
                </svg>

                Print daily list

            </button>

        </div>

    </div>



    {{-- =========================================================
        DAY TABS
    ========================================================== --}}

    <div
        class="no-print
               flex flex-wrap
               items-center
               gap-3"
    >

        @foreach ($days as $day)

            <a
                href="{{ route(
                    'admin.section-offerings.index',
                    [
                        'day_id' =>
                            $day->id,

                        'view' =>
                            $selectedSubject === 'interactive'
                                ? 'interactive'
                                : 'regular',

                        'subject' =>
                            $selectedSubject,
                    ]
                ) }}"
                class="inline-flex
                       min-w-32
                       items-center
                       justify-center
                       gap-2
                       rounded-xl
                       border
                       px-6 py-3
                       text-sm
                       font-semibold
                       transition
                    {{
                        (int) $selectedDayId
                        ===
                        (int) $day->id

                            ? 'border-blue-600 bg-blue-600 text-white shadow-sm'

                            : 'border-slate-200 bg-white text-blue-700 hover:border-blue-300 hover:bg-blue-50'
                    }}"
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
                </svg>

                {{ $day->day_name }}

            </a>

        @endforeach

    </div>



    {{-- =========================================================
        SUBJECT TABS
    ========================================================== --}}

    <div
        class="no-print
               grid max-w-3xl
               grid-cols-1
               overflow-hidden
               rounded-xl
               border
               border-slate-200
               bg-white
               sm:grid-cols-3"
    >


        {{-- English --}}
        <a
            href="{{ route(
                'admin.section-offerings.index',
                [
                    'day_id' =>
                        $selectedDayId,

                    'view' =>
                        'regular',

                    'subject' =>
                        'english',
                ]
            ) }}"
            class="inline-flex
                   h-16
                   items-center
                   justify-center
                   gap-3
                   border-b
                   border-slate-200
                   text-base
                   font-semibold
                   transition
                   sm:border-b-0
                   sm:border-r
                {{
                    $selectedSubject === 'english'

                        ? 'bg-blue-600 text-white'

                        : 'bg-white text-slate-700 hover:bg-blue-50 hover:text-blue-700'
                }}"
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
                    d="M2.25 6.75
                       c2.25-1.5 4.5-1.5 6.75 0
                       v12
                       c-2.25-1.5-4.5-1.5-6.75 0
                       v-12Zm19.5 0
                       c-2.25-1.5-4.5-1.5-6.75 0
                       v12
                       c2.25-1.5 4.5-1.5 6.75 0
                       v-12ZM9 6.75v12
                       M15 6.75v12"
                />
            </svg>

            English

        </a>



        {{-- Math --}}
        <a
            href="{{ route(
                'admin.section-offerings.index',
                [
                    'day_id' =>
                        $selectedDayId,

                    'view' =>
                        'regular',

                    'subject' =>
                        'math',
                ]
            ) }}"
            class="inline-flex
                   h-16
                   items-center
                   justify-center
                   gap-3
                   border-b
                   border-slate-200
                   text-base
                   font-semibold
                   transition
                   sm:border-b-0
                   sm:border-r
                {{
                    $selectedSubject === 'math'

                        ? 'bg-blue-600 text-white'

                        : 'bg-white text-slate-700 hover:bg-blue-50 hover:text-blue-700'
                }}"
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
                    x="4"
                    y="3"
                    width="16"
                    height="18"
                    rx="2"
                />
                <path d="M8 7h8M8 11h2M14 11h2M8 15h2M14 15h2" />
            </svg>

            Math

        </a>



        {{-- Interactive --}}
        <a
            href="{{ route(
                'admin.section-offerings.index',
                [
                    'day_id' =>
                        $selectedDayId,

                    'view' =>
                        'interactive',

                    'subject' =>
                        'interactive',
                ]
            ) }}"
            class="inline-flex
                   h-16
                   items-center
                   justify-center
                   gap-3
                   text-base
                   font-semibold
                   transition
                {{
                    $selectedSubject === 'interactive'

                        ? 'bg-blue-600 text-white'

                        : 'bg-white text-slate-700 hover:bg-blue-50 hover:text-blue-700'
                }}"
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
                    y="4"
                    width="18"
                    height="13"
                    rx="2"
                />
                <path d="M8 21h8M12 17v4" />
            </svg>

            Interactive

        </a>

    </div>



    {{-- =========================================================
        SUBJECT INFORMATION
    ========================================================== --}}

    <div
        class="flex items-center
               gap-4
               rounded-2xl
               border
               border-blue-100
               bg-blue-50/50
               px-6 py-5"
    >

        <div
            class="flex h-10 w-10
                   shrink-0
                   items-center
                   justify-center
                   rounded-full
                   bg-blue-600
                   text-white"
        >
            i
        </div>


        <div
            class="text-sm
                   text-slate-600"
        >

            @if ($selectedSubject === 'english')

                <span
                    class="font-bold
                           text-slate-800"
                >
                    English
                </span>

                <span class="ml-1">
                    max
                    {{ $englishCapacity ?: '—' }}
                    seats per timeslot
                </span>


            @elseif ($selectedSubject === 'math')

                <span
                    class="font-bold
                           text-slate-800"
                >
                    Math
                </span>

                <span class="ml-1">

                    shared max
                    {{ $mathCapacity ?: '—' }}
                    students

                    @if ($mathSections->isNotEmpty())

                        across

                        {{
                            $mathSections
                                ->pluck('section_name')
                                ->implode(', ')
                        }}

                    @endif

                </span>


            @else

                <span
                    class="font-bold
                           text-slate-800"
                >
                    Interactive
                </span>

                <span class="ml-1">
                    capacity is managed separately
                    for each class offering
                </span>

            @endif

        </div>

    </div>



    {{-- =========================================================
        SELECTED DAY
    ========================================================== --}}

    <div>

        <p
            class="text-xs
                   font-semibold
                   uppercase
                   tracking-wide
                   text-slate-400"
        >
            Selected Day
        </p>


        <div
            class="mt-2
                   flex items-center
                   gap-3"
        >

            <div
                class="flex h-10 w-10
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-50
                       text-blue-600"
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
                </svg>

            </div>


            <p
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                {{
                    $selectedDay?->day_name
                    ?? 'No day selected'
                }}
            </p>

        </div>

    </div>



    {{-- =========================================================
        ENGLISH SCHEDULE
    ========================================================== --}}

    @if ($selectedSubject === 'english')

        <div
            class="print-container
                   overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="min-w-full">


                    {{-- Header --}}
                    <thead
                        class="border-b
                               border-slate-200
                               bg-slate-50/70"
                    >

                        <tr>

                            <th
                                class="px-7 py-5
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Time
                            </th>


                            <th
                                class="px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Enrolled / Capacity
                            </th>


                            <th
                                class="px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Availability
                            </th>


                            <th
                                class="px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Status
                            </th>


                            <th
                                class="no-print
                                       px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse ($regularRows as $row)

                            @php

                                $allocated =
                                    (int)
                                    $row['english_allocated'];

                                $maximum =
                                    (int)
                                    $row['english_maximum'];

                                $available =
                                    max(
                                        0,
                                        $maximum - $allocated
                                    );

                                $availabilityPercentage =
                                    $maximum > 0
                                        ? round(
                                            (
                                                $available /
                                                $maximum
                                            ) * 100
                                        )
                                        : 0;

                                $status =
                                    $getStatus(
                                        $allocated,
                                        $maximum
                                    );

                            @endphp


                            <tr
                                class="transition
                                       hover:bg-blue-50/30"
                            >


                                {{-- Time --}}
                                <td
                                    class="whitespace-nowrap
                                           px-7 py-7"
                                >

                                    <div
                                        class="flex
                                               items-center
                                               gap-4"
                                    >

                                        <div
                                            class="flex
                                                   h-10 w-10
                                                   items-center
                                                   justify-center
                                                   rounded-full
                                                   bg-blue-50
                                                   text-blue-600"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                class="h-5 w-5"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="9"
                                                />
                                                <path d="M12 7v5l3 2" />
                                            </svg>

                                        </div>


                                        <div>

                                            <p
                                                class="text-lg
                                                       font-bold
                                                       text-slate-900"
                                            >
                                                {{ $row['time'] }}
                                            </p>

                                            <p
                                                class="mt-1
                                                       text-xs
                                                       text-slate-400"
                                            >
                                                {{
                                                    $selectedDay
                                                        ?->day_name
                                                    ?? ''
                                                }}
                                            </p>

                                        </div>

                                    </div>

                                </td>



                                {{-- Capacity --}}
                                <td
                                    class="px-7 py-7
                                           text-center"
                                >

                                    <p
                                        class="text-lg
                                               font-bold
                                               text-slate-900"
                                    >
                                        {{ $allocated }}
                                        /
                                        {{ $maximum ?: '—' }}
                                    </p>

                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-400"
                                    >
                                        seats
                                    </p>

                                </td>



                                {{-- Availability --}}
                                <td
                                    class="px-7 py-7"
                                >

                                    <div
                                        class="mx-auto
                                               max-w-40"
                                    >

                                        <div
                                            class="h-2
                                                   overflow-hidden
                                                   rounded-full
                                                   bg-slate-100"
                                        >

                                            <div
                                                class="h-full
                                                       rounded-full
                                                       {{
                                                           $status['bar']
                                                       }}"
                                                style="
                                                    width:
                                                    {{
                                                        $availabilityPercentage
                                                    }}%;
                                                "
                                            ></div>

                                        </div>


                                        <p
                                            class="mt-2
                                                   text-center
                                                   text-sm
                                                   font-normal
                                                   text-slate-500"
                                        >
                                            {{
                                                $availabilityPercentage
                                            }}%
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-center
                                                   text-xs
                                                   text-slate-400"
                                        >
                                            {{ $available }}

                                            {{
                                                $available === 1
                                                    ? 'seat'
                                                    : 'seats'
                                            }}

                                            available
                                        </p>

                                    </div>

                                </td>



                                {{-- Status --}}
                                <td
                                    class="px-7 py-7
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               min-w-28
                                               items-center
                                               justify-center
                                               gap-2
                                               rounded-xl
                                               px-4 py-2
                                               text-xs
                                               font-semibold
                                               {{
                                                   $status['class']
                                               }}"
                                    >

                                        <span
                                            class="h-2 w-2
                                                   rounded-full
                                                   bg-current"
                                        ></span>

                                        {{ $status['label'] }}

                                    </span>

                                </td>



                                {{-- Action --}}
                                <td
                                    class="no-print
                                           px-7 py-7
                                           text-center"
                                >

                                    @if (
                                        $row['first_offering_id'] &&
                                        auth()->user()
                                            ->hasPermission(
                                                'section_offerings.edit'
                                            )
                                    )

                                        <a
                                            href="{{ route(
                                                'admin.section-offerings.edit',
                                                $row[
                                                    'first_offering_id'
                                                ]
                                            ) }}"
                                            class="inline-flex
                                                   h-11
                                                   items-center
                                                   justify-center
                                                   gap-2
                                                   rounded-xl
                                                   border
                                                   border-blue-300
                                                   bg-white
                                                   px-6
                                                   text-sm
                                                   font-semibold
                                                   text-blue-700
                                                   transition
                                                   hover:bg-blue-50"
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
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12
                                                       s3.75-6
                                                       9.75-6
                                                       9.75 6
                                                       9.75 6
                                                       -3.75 6
                                                       -9.75 6
                                                       -9.75-6
                                                       -9.75-6Z"
                                                />
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                />
                                            </svg>

                                            View

                                        </a>

                                    @else

                                        <span
                                            class="text-sm
                                                   text-slate-400"
                                        >
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-16
                                           text-center"
                                >

                                    <p
                                        class="font-semibold
                                               text-slate-700"
                                    >
                                        No English classes found
                                    </p>

                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500"
                                    >
                                        Add an English class offering
                                        for
                                        {{
                                            $selectedDay
                                                ?->day_name
                                            ?? 'this day'
                                        }}.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    @endif



    {{-- =========================================================
        MATH SCHEDULE
    ========================================================== --}}

    @if ($selectedSubject === 'math')

        <div
            class="print-container
                   overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead
                        class="border-b
                               border-slate-200
                               bg-slate-50/70"
                    >

                        <tr>

                            <th
                                class="px-6 py-5
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Time
                            </th>


                            @foreach ($mathSections as $mathSection)

                                <th
                                    class="px-5 py-5
                                           text-center
                                           text-xs
                                           font-bold
                                           uppercase
                                           tracking-wide
                                           text-slate-700"
                                >
                                    {{
                                        $mathSection
                                            ->section_name
                                    }}
                                </th>

                            @endforeach


                            <th
                                class="px-6 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Math Total / Capacity
                            </th>


                            <th
                                class="px-6 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Availability
                            </th>


                            <th
                                class="px-6 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Status
                            </th>


                            <th
                                class="no-print
                                       px-6 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse ($regularRows as $row)

                            @php

                                $mathAllocated =
                                    (int)
                                    $row['math_total'];

                                $mathMaximum =
                                    (int)
                                    $row['math_maximum'];

                                $mathAvailable =
                                    max(
                                        0,
                                        $mathMaximum -
                                        $mathAllocated
                                    );

                                $mathAvailabilityPercentage =
                                    $mathMaximum > 0
                                        ? round(
                                            (
                                                $mathAvailable /
                                                $mathMaximum
                                            ) * 100
                                        )
                                        : 0;

                                $mathStatus =
                                    $getStatus(
                                        $mathAllocated,
                                        $mathMaximum
                                    );

                            @endphp


                            <tr
                                class="transition
                                       hover:bg-blue-50/30"
                            >


                                {{-- Time --}}
                                <td
                                    class="whitespace-nowrap
                                           px-6 py-7"
                                >

                                    <div
                                        class="flex
                                               items-center
                                               gap-4"
                                    >

                                        <div
                                            class="flex
                                                   h-10 w-10
                                                   items-center
                                                   justify-center
                                                   rounded-full
                                                   bg-blue-50
                                                   text-blue-600"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                class="h-5 w-5"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="9"
                                                />
                                                <path d="M12 7v5l3 2" />
                                            </svg>
                                        </div>


                                        <p
                                            class="text-lg
                                                   font-bold
                                                   text-slate-900"
                                        >
                                            {{ $row['time'] }}
                                        </p>

                                    </div>

                                </td>



                                {{-- Individual Math sections --}}
                                @foreach ($mathSections as $mathSection)

                                    <td
                                        class="px-5 py-7
                                               text-center"
                                    >

                                        <p
                                            class="text-base
                                                   font-bold
                                                   text-slate-800"
                                        >
                                            {{
                                                $row[
                                                    'math_allocations'
                                                ][
                                                    $mathSection->id
                                                ]
                                                ?? 0
                                            }}
                                        </p>

                                    </td>

                                @endforeach



                                {{-- Math Capacity --}}
                                <td
                                    class="px-6 py-7
                                           text-center"
                                >

                                    <p
                                        class="text-lg
                                               font-bold
                                               text-slate-900"
                                    >
                                        {{ $mathAllocated }}
                                        /
                                        {{ $mathMaximum ?: '—' }}
                                    </p>

                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-400"
                                    >
                                        shared seats
                                    </p>

                                </td>



                                {{-- Availability --}}
                                <td
                                    class="px-6 py-7"
                                >

                                    <div
                                        class="mx-auto
                                               max-w-40"
                                    >

                                        <div
                                            class="h-2
                                                   overflow-hidden
                                                   rounded-full
                                                   bg-slate-100"
                                        >

                                            <div
                                                class="h-full
                                                       rounded-full
                                                       {{
                                                           $mathStatus['bar']
                                                       }}"
                                                style="
                                                    width:
                                                    {{
                                                        $mathAvailabilityPercentage
                                                    }}%;
                                                "
                                            ></div>

                                        </div>


                                        <p
                                            class="mt-2
                                                   text-center
                                                   text-sm
                                                   font-normal
                                                   text-slate-500"
                                        >
                                            {{
                                                $mathAvailabilityPercentage
                                            }}%
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-center
                                                   text-xs
                                                   text-slate-400"
                                        >
                                            {{ $mathAvailable }}

                                            {{
                                                $mathAvailable === 1
                                                    ? 'seat'
                                                    : 'seats'
                                            }}

                                            available
                                        </p>

                                    </div>

                                </td>



                                {{-- Status --}}
                                <td
                                    class="px-6 py-7
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               min-w-28
                                               items-center
                                               justify-center
                                               gap-2
                                               rounded-xl
                                               px-4 py-2
                                               text-xs
                                               font-semibold
                                               {{
                                                   $mathStatus['class']
                                               }}"
                                    >

                                        <span
                                            class="h-2 w-2
                                                   rounded-full
                                                   bg-current"
                                        ></span>

                                        {{
                                            $mathStatus[
                                                'label'
                                            ]
                                        }}

                                    </span>

                                </td>



                                {{-- Action --}}
                                <td
                                    class="no-print
                                           px-6 py-7
                                           text-center"
                                >

                                    @if (
                                        $row['first_offering_id'] &&
                                        auth()->user()
                                            ->hasPermission(
                                                'section_offerings.edit'
                                            )
                                    )

                                        <a
                                            href="{{ route(
                                                'admin.section-offerings.edit',
                                                $row[
                                                    'first_offering_id'
                                                ]
                                            ) }}"
                                            class="inline-flex
                                                   h-11
                                                   items-center
                                                   justify-center
                                                   gap-2
                                                   rounded-xl
                                                   border
                                                   border-blue-300
                                                   bg-white
                                                   px-6
                                                   text-sm
                                                   font-semibold
                                                   text-blue-700
                                                   transition
                                                   hover:bg-blue-50"
                                        >
                                            View
                                        </a>

                                    @else

                                        <span
                                            class="text-sm
                                                   text-slate-400"
                                        >
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="{{ 5 + $mathSections->count() }}"
                                    class="px-6 py-16
                                           text-center"
                                >

                                    <p
                                        class="font-semibold
                                               text-slate-700"
                                    >
                                        No Math classes found
                                    </p>

                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500"
                                    >
                                        Add Math class offerings
                                        for
                                        {{
                                            $selectedDay
                                                ?->day_name
                                            ?? 'this day'
                                        }}.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    @endif



    {{-- =========================================================
        INTERACTIVE SCHEDULE
    ========================================================== --}}

    @if ($selectedSubject === 'interactive')

        <div
            class="print-container
                   overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead
                        class="border-b
                               border-slate-200
                               bg-slate-50/70"
                    >

                        <tr>

                            <th
                                class="px-7 py-5
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Time
                            </th>


                            <th
                                class="px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Enrolled / Capacity
                            </th>


                            <th
                                class="px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Availability
                            </th>


                            <th
                                class="px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Status
                            </th>


                            <th
                                class="no-print
                                       px-7 py-5
                                       text-center
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-slate-700"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse ($interactiveRows as $row)

                            @php

                                $allocated =
                                    (int)
                                    $row['allocated'];

                                $maximum =
                                    (int)
                                    $row['maximum'];

                                $available =
                                    max(
                                        0,
                                        $maximum -
                                        $allocated
                                    );

                                $availabilityPercentage =
                                    $maximum > 0
                                        ? round(
                                            (
                                                $available /
                                                $maximum
                                            ) * 100
                                        )
                                        : 0;

                                $status =
                                    $getStatus(
                                        $allocated,
                                        $maximum
                                    );

                            @endphp


                            <tr
                                class="transition
                                       hover:bg-blue-50/30"
                            >


                                {{-- Time --}}
                                <td
                                    class="whitespace-nowrap
                                           px-7 py-7"
                                >

                                    <div
                                        class="flex
                                               items-center
                                               gap-4"
                                    >

                                        <div
                                            class="flex
                                                   h-10 w-10
                                                   items-center
                                                   justify-center
                                                   rounded-full
                                                   bg-blue-50
                                                   text-blue-600"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                class="h-5 w-5"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="9"
                                                />
                                                <path d="M12 7v5l3 2" />
                                            </svg>
                                        </div>


                                        <div>

                                            <p
                                                class="text-lg
                                                       font-bold
                                                       text-slate-900"
                                            >
                                                {{ $row['time'] }}
                                            </p>


                                            <p
                                                class="mt-1
                                                       text-xs
                                                       text-slate-400"
                                            >
                                                {{ $row['end_time'] }}
                                            </p>

                                        </div>

                                    </div>

                                </td>



                                {{-- Capacity --}}
                                <td
                                    class="px-7 py-7
                                           text-center"
                                >

                                    <p
                                        class="text-lg
                                               font-bold
                                               text-slate-900"
                                    >
                                        {{ $allocated }}
                                        /
                                        {{ $maximum ?: '—' }}
                                    </p>


                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-400"
                                    >
                                        seats
                                    </p>

                                </td>



                                {{-- Availability --}}
                                <td
                                    class="px-7 py-7"
                                >

                                    <div
                                        class="mx-auto
                                               max-w-40"
                                    >

                                        <div
                                            class="h-2
                                                   overflow-hidden
                                                   rounded-full
                                                   bg-slate-100"
                                        >

                                            <div
                                                class="h-full
                                                       rounded-full
                                                       {{
                                                           $status['bar']
                                                       }}"
                                                style="
                                                    width:
                                                    {{
                                                        $availabilityPercentage
                                                    }}%;
                                                "
                                            ></div>

                                        </div>


                                        <p
                                            class="mt-2
                                                   text-center
                                                   text-sm
                                                   font-normal
                                                   text-slate-500"
                                        >
                                            {{
                                                $availabilityPercentage
                                            }}%
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-center
                                                   text-xs
                                                   text-slate-400"
                                        >
                                            {{ $available }}

                                            {{
                                                $available === 1
                                                    ? 'seat'
                                                    : 'seats'
                                            }}

                                            available
                                        </p>

                                    </div>

                                </td>



                                {{-- Status --}}
                                <td
                                    class="px-7 py-7
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               min-w-28
                                               items-center
                                               justify-center
                                               gap-2
                                               rounded-xl
                                               px-4 py-2
                                               text-xs
                                               font-semibold
                                               {{
                                                   $status['class']
                                               }}"
                                    >

                                        <span
                                            class="h-2 w-2
                                                   rounded-full
                                                   bg-current"
                                        ></span>

                                        {{ $status['label'] }}

                                    </span>

                                </td>



                                {{-- Action --}}
                                <td
                                    class="no-print
                                           px-7 py-7
                                           text-center"
                                >

                                    @if (
                                        auth()->user()
                                            ->hasPermission(
                                                'section_offerings.edit'
                                            )
                                    )

                                        <a
                                            href="{{ route(
                                                'admin.section-offerings.edit',
                                                $row['id']
                                            ) }}"
                                            class="inline-flex
                                                   h-11
                                                   items-center
                                                   justify-center
                                                   gap-2
                                                   rounded-xl
                                                   border
                                                   border-blue-300
                                                   bg-white
                                                   px-6
                                                   text-sm
                                                   font-semibold
                                                   text-blue-700
                                                   transition
                                                   hover:bg-blue-50"
                                        >
                                            View
                                        </a>

                                    @else

                                        <span
                                            class="text-sm
                                                   text-slate-400"
                                        >
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-16
                                           text-center"
                                >

                                    <p
                                        class="font-semibold
                                               text-slate-700"
                                    >
                                        No Interactive classes found
                                    </p>

                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500"
                                    >
                                        Add Interactive offerings
                                        for
                                        {{
                                            $selectedDay
                                                ?->day_name
                                            ?? 'this day'
                                        }}.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    @endif



    {{-- =========================================================
        BOTTOM INFORMATION CARDS
    ========================================================== --}}

    <div
        class="no-print
               grid gap-4
               rounded-2xl
               border
               border-blue-100
               bg-white
               p-5
               shadow-sm
               md:grid-cols-3"
    >


        {{-- Capacity --}}
        <div
            class="flex
                   items-center
                   gap-4
                   px-4 py-2"
        >

            <div
                class="flex h-12 w-12
                       shrink-0
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-50
                       text-blue-600"
            >
                👥
            </div>


            <div>

                <p
                    class="text-sm
                           font-bold
                           text-slate-800"
                >
                    Capacity
                </p>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >

                    @if ($selectedSubject === 'english')

                        Max
                        {{ $englishCapacity ?: '—' }}
                        students per timeslot

                    @elseif ($selectedSubject === 'math')

                        Shared max
                        {{ $mathCapacity ?: '—' }}
                        students

                    @else

                        Managed per Interactive offering

                    @endif

                </p>

            </div>

        </div>



        {{-- Selected Subject --}}
        <div
            class="flex
                   items-center
                   gap-4
                   border-y
                   border-slate-100
                   px-4 py-5
                   md:border-x
                   md:border-y-0"
        >

            <div
                class="flex h-12 w-12
                       shrink-0
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-50
                       text-blue-600"
            >
                📘
            </div>


            <div>

                <p
                    class="text-sm
                           font-bold
                           text-slate-800"
                >
                    Selected Class
                </p>

                <p
                    class="mt-1
                           text-sm
                           capitalize
                           text-slate-500"
                >
                    {{ $selectedSubject }}
                </p>

            </div>

        </div>



        {{-- Day --}}
        <div
            class="flex
                   items-center
                   gap-4
                   px-4 py-2"
        >

            <div
                class="flex h-12 w-12
                       shrink-0
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-50
                       text-blue-600"
            >
                📅
            </div>


            <div>

                <p
                    class="text-sm
                           font-bold
                           text-slate-800"
                >
                    Selected Day
                </p>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    {{
                        $selectedDay?->day_name
                        ?? '—'
                    }}
                </p>

            </div>

        </div>

    </div>


</div>

@endsection
