@extends('layouts.admin')

@section('title', 'Class Enrolment')

@section('page-title', 'Class Enrolment')


@php

    $oldConfirmed =
        array_map(
            'intval',
            old(
                'confirmed_ids',
                []
            )
        );


    $oldWishlist =
        array_map(
            'intval',
            old(
                'wishlist_ids',
                []
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Filter Options
    |--------------------------------------------------------------------------
    */

    $availableSections =
        $sectionOfferings
            ->pluck(
                'section'
            )
            ->filter()
            ->unique(
                'id'
            )
            ->sortBy(
                'section_name'
            );


    $availableDays =
        $sectionOfferings
            ->pluck(
                'day'
            )
            ->filter()
            ->unique(
                'id'
            )
            ->sortBy(
                'sort_order'
            );


    $availableTimes =
        $sectionOfferings
            ->map(
                function ($offering) {

                    return [
                        'raw' =>
                            $offering
                                ->start_time,

                        'label' =>
                            \Carbon\Carbon::createFromFormat(
                                'H:i:s',
                                $offering
                                    ->start_time
                            )->format(
                                'g:i A'
                            ),
                    ];
                }
            )
            ->unique(
                'raw'
            )
            ->sortBy(
                'raw'
            )
            ->values();

@endphp


@section('content')

<div class="mx-auto max-w-7xl">


    {{-- =========================================================
        STEPS
    ========================================================== --}}

    <div
        class="mb-6
               flex
               flex-wrap
               items-center
               gap-3"
    >

        <a
            href="{{ route(
                'admin.student-registration.student'
            ) }}"
            class="font-semibold
                   text-blue-600"
        >
            ✓ Student Details
        </a>


        <span class="text-gray-400">
            →
        </span>


        <a
            href="{{ route(
                'admin.student-registration.guardians'
            ) }}"
            class="font-semibold
                   text-blue-600"
        >
            ✓ Guardian Details
        </a>


        <span class="text-gray-400">
            →
        </span>


        <span
            class="font-semibold
                   text-blue-700"
        >
            3. Class Enrolment
        </span>

    </div>



    {{-- =========================================================
        ERRORS
    ========================================================== --}}

    @error('classes')

        <div
            class="mb-5
                   rounded-lg
                   border
                   border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm
                   font-medium
                   text-red-700"
        >
            {{ $message }}
        </div>

    @enderror


    @error('confirmed_ids')

        <div
            class="mb-5
                   rounded-lg
                   border
                   border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm
                   text-red-700"
        >
            {{ $message }}
        </div>

    @enderror


    @error('wishlist_ids')

        <div
            class="mb-5
                   rounded-lg
                   border
                   border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm
                   text-red-700"
        >
            {{ $message }}
        </div>

    @enderror



    {{-- =========================================================
        JAVASCRIPT VALIDATION MESSAGE
    ========================================================== --}}

    <div
        id="classSelectionError"
        class="mb-5 hidden
               rounded-lg
               border
               border-red-200
               bg-red-50
               px-4 py-3
               text-sm
               font-medium
               text-red-700"
    ></div>



    {{-- =========================================================
        STUDENT SUMMARY
    ========================================================== --}}

    <div
        class="mb-6
               rounded-xl
               border
               border-gray-200
               bg-white
               p-6
               shadow-sm"
    >

        <h2
            class="text-xl
                   font-bold
                   text-gray-800"
        >
            Student Registration Summary
        </h2>


        <div
            class="mt-4
                   grid
                   gap-4
                   md:grid-cols-3"
        >

            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-gray-500"
                >
                    Student
                </p>


                <p
                    class="mt-1
                           font-semibold
                           text-gray-800"
                >
                    {{ $studentData['first_name'] }}
                    {{ $studentData['last_name'] }}
                </p>

            </div>


            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-gray-500"
                >
                    Student ID
                </p>


                <p
                    class="mt-1
                           text-gray-800"
                >
                    {{ $studentData['external_id'] }}
                </p>

            </div>


            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-gray-500"
                >
                    Guardian Count
                </p>


                <p
                    class="mt-1
                           text-gray-800"
                >

                    {{
                        count(
                            $guardianData[
                                'existing'
                            ] ?? []
                        )
                        +
                        count(
                            $guardianData[
                                'new'
                            ] ?? []
                        )
                    }}

                </p>

            </div>

        </div>

    </div>



    <form
        method="POST"
        action="{{ route(
            'admin.student-registration.complete'
        ) }}"
        id="classEnrolmentForm"
    >

        @csrf


        <div
            class="rounded-xl
                   border
                   border-gray-200
                   bg-white
                   shadow-sm"
        >


            {{-- =================================================
                HEADING
            ================================================== --}}

            <div
                class="border-b
                       border-gray-200
                       p-6"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-gray-800"
                >
                    Select Classes
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >
                    Confirmed classes use one seat.
                    Wishlist selections do not use any seats.
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-blue-700"
                >
                    Only one class time can be selected
                    for each class.
                </p>

            </div>



            {{-- =================================================
                FILTERS
            ================================================== --}}

            <div
                class="border-b
                       border-gray-200
                       bg-gray-50/70
                       p-6"
            >

                <div
                    class="flex
                           flex-col
                           gap-4
                           lg:flex-row
                           lg:items-end"
                >


                    {{-- =========================================
                        CLASS FILTER
                    ========================================== --}}

                    <div class="flex-1">

                        <label
                            for="classFilter"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   uppercase
                                   tracking-wide
                                   text-gray-500"
                        >
                            Class
                        </label>


                        <select
                            id="classFilter"
                            class="h-11
                                   w-full
                                   rounded-lg
                                   border-gray-300
                                   bg-white
                                   text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            <option value="">
                                All Classes
                            </option>


                            @foreach (
                                $availableSections
                                as $section
                            )

                                <option
                                    value="{{ $section->id }}"
                                >
                                    {{ $section->section_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>



                    {{-- =========================================
                        DAY FILTER
                    ========================================== --}}

                    <div class="flex-1">

                        <label
                            for="dayFilter"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   uppercase
                                   tracking-wide
                                   text-gray-500"
                        >
                            Day
                        </label>


                        <select
                            id="dayFilter"
                            class="h-11
                                   w-full
                                   rounded-lg
                                   border-gray-300
                                   bg-white
                                   text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            <option value="">
                                All Days
                            </option>


                            @foreach (
                                $availableDays
                                as $day
                            )

                                <option
                                    value="{{ $day->id }}"
                                >
                                    {{ $day->day_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>



                    {{-- =========================================
                        TIME FILTER
                    ========================================== --}}

                    <div class="flex-1">

                        <label
                            for="timeFilter"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   uppercase
                                   tracking-wide
                                   text-gray-500"
                        >
                            Time
                        </label>


                        <select
                            id="timeFilter"
                            class="h-11
                                   w-full
                                   rounded-lg
                                   border-gray-300
                                   bg-white
                                   text-sm
                                   shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            <option value="">
                                All Times
                            </option>


                            @foreach (
                                $availableTimes
                                as $time
                            )

                                <option
                                    value="{{ $time['raw'] }}"
                                >
                                    {{ $time['label'] }}
                                </option>

                            @endforeach

                        </select>

                    </div>



                    {{-- =========================================
                        CLEAR
                    ========================================== --}}

                    <div>

                        <button
                            type="button"
                            id="clearFilters"
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-lg
                                   border
                                   border-gray-300
                                   bg-white
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-gray-700
                                   hover:bg-gray-100"
                        >
                            Clear Filters
                        </button>

                    </div>

                </div>



                {{-- =============================================
                    FILTER RESULT MESSAGE
                ============================================== --}}

                <div
                    class="mt-4
                           flex
                           items-center
                           justify-between
                           gap-4"
                >

                    <p
                        id="filterResultCount"
                        class="text-xs
                               text-gray-500"
                    >
                        Showing all available classes
                    </p>


                    <p
                        class="text-xs
                               font-medium
                               text-blue-600"
                    >
                        You can select one offering
                        from each class.
                    </p>

                </div>

            </div>



            {{-- =================================================
                TABLE
            ================================================== --}}

            <div class="overflow-x-auto">

                <table
                    class="min-w-full
                           divide-y
                           divide-gray-200"
                >

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-5 py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Day
                            </th>


                            <th
                                class="px-5 py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Class
                            </th>


                            <th
                                class="px-5 py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Time
                            </th>


                            <th
                                class="px-5 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Maximum
                            </th>


                            <th
                                class="px-5 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Allocated
                            </th>


                            <th
                                class="px-5 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Available
                            </th>


                            <th
                                class="px-5 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Enrol
                            </th>


                            <th
                                class="px-5 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-gray-500"
                            >
                                Wishlist
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        id="classOfferingTable"
                        class="divide-y
                               divide-gray-200"
                    >

                        @forelse (
                            $sectionOfferings
                            as $offering
                        )

                            @php

                                $isFull =
                                    $offering
                                        ->available_seats
                                    <=
                                    0;

                            @endphp


                            <tr
                                class="class-offering-row
                                {{
                                    $isFull
                                        ? 'bg-red-50'
                                        : 'hover:bg-gray-50'
                                }}"
                                data-section-id="{{
                                    $offering
                                        ->section_id
                                }}"
                                data-section-name="{{
                                    $offering
                                        ->section
                                        ->section_name
                                }}"
                                data-day-id="{{
                                    $offering
                                        ->day_id
                                }}"
                                data-time="{{
                                    $offering
                                        ->start_time
                                }}"
                            >


                                {{-- Day --}}
                                <td
                                    class="whitespace-nowrap
                                           px-5 py-4
                                           text-sm
                                           font-semibold
                                           text-gray-800"
                                >
                                    {{
                                        $offering
                                            ->day
                                            ->day_name
                                    }}
                                </td>



                                {{-- Class --}}
                                <td
                                    class="whitespace-nowrap
                                           px-5 py-4
                                           text-sm
                                           font-medium
                                           text-gray-700"
                                >
                                    {{
                                        $offering
                                            ->section
                                            ->section_name
                                    }}
                                </td>



                                {{-- Time --}}
                                <td
                                    class="whitespace-nowrap
                                           px-5 py-4
                                           text-sm
                                           text-gray-700"
                                >

                                    {{
                                        \Carbon\Carbon::createFromFormat(
                                            'H:i:s',
                                            $offering
                                                ->start_time
                                        )->format(
                                            'g:i A'
                                        )
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::createFromFormat(
                                            'H:i:s',
                                            $offering
                                                ->end_time
                                        )->format(
                                            'g:i A'
                                        )
                                    }}

                                </td>



                                {{-- Maximum --}}
                                <td
                                    class="px-5 py-4
                                           text-center
                                           text-sm
                                           text-gray-700"
                                >
                                    {{
                                        $offering
                                            ->max_seats
                                    }}
                                </td>



                                {{-- Allocated --}}
                                <td
                                    class="px-5 py-4
                                           text-center
                                           text-sm
                                           text-gray-700"
                                >
                                    {{
                                        $offering
                                            ->allocated_seats
                                    }}
                                </td>



                                {{-- Available --}}
                                <td
                                    class="px-5 py-4
                                           text-center"
                                >

                                    @if ($isFull)

                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-red-100
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-red-700"
                                        >
                                            Full
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-green-100
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-green-700"
                                        >
                                            {{
                                                $offering
                                                    ->available_seats
                                            }}
                                        </span>

                                    @endif

                                </td>



                                {{-- Enrol --}}
                                <td
                                    class="px-5 py-4
                                           text-center"
                                >

                                    <input
                                        type="checkbox"
                                        name="confirmed_ids[]"
                                        value="{{ $offering->id }}"
                                        class="class-selection-checkbox
                                               confirmed-checkbox
                                               rounded
                                               border-gray-300
                                               text-blue-600"
                                        data-offering="{{
                                            $offering->id
                                        }}"
                                        data-section="{{
                                            $offering->section_id
                                        }}"
                                        data-section-name="{{
                                            $offering
                                                ->section
                                                ->section_name
                                        }}"
                                        @checked(
                                            in_array(
                                                $offering->id,
                                                $oldConfirmed
                                            )
                                        )
                                        @disabled(
                                            $isFull
                                        )
                                    >


                                    @if ($isFull)

                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-red-600"
                                        >
                                            No seats
                                        </p>

                                    @endif

                                </td>



                                {{-- Wishlist --}}
                                <td
                                    class="px-5 py-4
                                           text-center"
                                >

                                    <input
                                        type="checkbox"
                                        name="wishlist_ids[]"
                                        value="{{ $offering->id }}"
                                        class="class-selection-checkbox
                                               wishlist-checkbox
                                               rounded
                                               border-gray-300
                                               text-purple-600"
                                        data-offering="{{
                                            $offering->id
                                        }}"
                                        data-section="{{
                                            $offering->section_id
                                        }}"
                                        data-section-name="{{
                                            $offering
                                                ->section
                                                ->section_name
                                        }}"
                                        @checked(
                                            in_array(
                                                $offering->id,
                                                $oldWishlist
                                            )
                                        )
                                    >


                                    @if (
                                        $offering
                                            ->wishlist_count
                                        >
                                        0
                                    )

                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-gray-500"
                                        >

                                            {{
                                                $offering
                                                    ->wishlist_count
                                            }}

                                            waiting

                                        </p>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="px-6 py-12
                                           text-center
                                           text-sm
                                           text-gray-500"
                                >
                                    No active classes are available.
                                </td>

                            </tr>

                        @endforelse



                        {{-- No Filter Results --}}

                        <tr
                            id="noFilterResults"
                            class="hidden"
                        >

                            <td
                                colspan="8"
                                class="px-6 py-12
                                       text-center
                                       text-sm
                                       text-gray-500"
                            >

                                No classes match the selected filters.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>



            {{-- =================================================
                ACTIONS
            ================================================== --}}

            <div
                class="flex
                       flex-col
                       gap-3
                       border-t
                       border-gray-200
                       bg-gray-50
                       p-6
                       sm:flex-row
                       sm:justify-end"
            >

                <a
                    href="{{ route(
                        'admin.student-registration.guardians'
                    ) }}"
                    class="rounded-lg
                           border
                           border-gray-300
                           bg-white
                           px-4 py-2.5
                           text-center
                           text-sm
                           font-semibold
                           text-gray-700
                           hover:bg-gray-100"
                >
                    ← Back to Guardians
                </a>


                <button
                    type="submit"
                    class="rounded-lg
                           bg-blue-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Complete Registration
                </button>

            </div>

        </div>

    </form>

</div>

@endsection



@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | Elements
        |--------------------------------------------------------------------------
        */

        const confirmedCheckboxes =
            document.querySelectorAll(
                '.confirmed-checkbox'
            );


        const wishlistCheckboxes =
            document.querySelectorAll(
                '.wishlist-checkbox'
            );


        const allSelectionCheckboxes =
            document.querySelectorAll(
                '.class-selection-checkbox'
            );


        const rows =
            document.querySelectorAll(
                '.class-offering-row'
            );


        const classFilter =
            document.getElementById(
                'classFilter'
            );


        const dayFilter =
            document.getElementById(
                'dayFilter'
            );


        const timeFilter =
            document.getElementById(
                'timeFilter'
            );


        const clearFilters =
            document.getElementById(
                'clearFilters'
            );


        const noFilterResults =
            document.getElementById(
                'noFilterResults'
            );


        const resultCount =
            document.getElementById(
                'filterResultCount'
            );


        const selectionError =
            document.getElementById(
                'classSelectionError'
            );


        const form =
            document.getElementById(
                'classEnrolmentForm'
            );



        /*
        |--------------------------------------------------------------------------
        | Show Selection Error
        |--------------------------------------------------------------------------
        */

        function showSelectionError(
            message
        ) {

            if (!selectionError) {
                return;
            }


            selectionError.textContent =
                message;


            selectionError.classList.remove(
                'hidden'
            );


            selectionError.scrollIntoView({
                behavior:
                    'smooth',

                block:
                    'center',
            });


            setTimeout(
                function () {

                    selectionError
                        .classList
                        .add(
                            'hidden'
                        );
                },
                4500
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Find Another Selected Offering For Same Class
        |--------------------------------------------------------------------------
        */

        function findSelectedForSection(
            sectionId,
            currentCheckbox
        ) {

            return Array.from(
                allSelectionCheckboxes
            )
                .find(
                    function (checkbox) {

                        return (
                            checkbox
                                !==
                                currentCheckbox
                            &&
                            checkbox
                                .checked
                            &&
                            checkbox
                                .dataset
                                .section
                            ===
                            sectionId
                        );
                    }
                );
        }



        /*
        |--------------------------------------------------------------------------
        | Enforce Only One Offering Per Class
        |--------------------------------------------------------------------------
        */

        function enforceOneOfferingPerClass(
            checkbox
        ) {

            if (!checkbox.checked) {
                return true;
            }


            const sectionId =
                checkbox
                    .dataset
                    .section;


            const sectionName =
                checkbox
                    .dataset
                    .sectionName;


            const existingSelection =
                findSelectedForSection(
                    sectionId,
                    checkbox
                );


            if (existingSelection) {

                checkbox.checked =
                    false;


                showSelectionError(
                    'Only one class time can be selected for '
                    +
                    sectionName
                    +
                    '. Unselect the existing '
                    +
                    sectionName
                    +
                    ' selection first.'
                );


                return false;
            }


            return true;
        }



        /*
        |--------------------------------------------------------------------------
        | Confirmed Selection
        |--------------------------------------------------------------------------
        */

        confirmedCheckboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        if (!checkbox.checked) {
                            return;
                        }


                        /*
                         * First enforce one class offering.
                         */
                        if (
                            !enforceOneOfferingPerClass(
                                checkbox
                            )
                        ) {

                            return;
                        }


                        /*
                         * Same offering cannot also
                         * be Wishlist.
                         */
                        const offeringId =
                            checkbox
                                .dataset
                                .offering;


                        const wishlistCheckbox =
                            document.querySelector(
                                '.wishlist-checkbox'
                                +
                                '[data-offering="'
                                +
                                offeringId
                                +
                                '"]'
                            );


                        if (wishlistCheckbox) {

                            wishlistCheckbox.checked =
                                false;
                        }
                    }
                );
            }
        );



        /*
        |--------------------------------------------------------------------------
        | Wishlist Selection
        |--------------------------------------------------------------------------
        */

        wishlistCheckboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        if (!checkbox.checked) {
                            return;
                        }


                        /*
                         * First enforce one offering
                         * for this class.
                         */
                        if (
                            !enforceOneOfferingPerClass(
                                checkbox
                            )
                        ) {

                            return;
                        }


                        /*
                         * Same offering cannot also
                         * be confirmed.
                         */
                        const offeringId =
                            checkbox
                                .dataset
                                .offering;


                        const confirmedCheckbox =
                            document.querySelector(
                                '.confirmed-checkbox'
                                +
                                '[data-offering="'
                                +
                                offeringId
                                +
                                '"]'
                            );


                        if (confirmedCheckbox) {

                            confirmedCheckbox.checked =
                                false;
                        }
                    }
                );
            }
        );



        /*
        |--------------------------------------------------------------------------
        | Apply Filters
        |--------------------------------------------------------------------------
        */

        function applyFilters() {

            const selectedClass =
                classFilter
                    ? classFilter.value
                    : '';


            const selectedDay =
                dayFilter
                    ? dayFilter.value
                    : '';


            const selectedTime =
                timeFilter
                    ? timeFilter.value
                    : '';


            let visibleCount = 0;


            rows.forEach(
                function (row) {

                    const matchesClass =
                        !selectedClass
                        ||
                        row
                            .dataset
                            .sectionId
                        ===
                        selectedClass;


                    const matchesDay =
                        !selectedDay
                        ||
                        row
                            .dataset
                            .dayId
                        ===
                        selectedDay;


                    const matchesTime =
                        !selectedTime
                        ||
                        row
                            .dataset
                            .time
                        ===
                        selectedTime;


                    const show =
                        matchesClass
                        &&
                        matchesDay
                        &&
                        matchesTime;


                    if (show) {

                        row.classList.remove(
                            'hidden'
                        );


                        visibleCount++;

                    } else {

                        row.classList.add(
                            'hidden'
                        );
                    }
                }
            );


            /*
             * No results message.
             */
            if (noFilterResults) {

                if (
                    visibleCount
                    ===
                    0
                ) {

                    noFilterResults
                        .classList
                        .remove(
                            'hidden'
                        );

                } else {

                    noFilterResults
                        .classList
                        .add(
                            'hidden'
                        );
                }
            }


            /*
             * Result count.
             */
            if (resultCount) {

                if (
                    !selectedClass
                    &&
                    !selectedDay
                    &&
                    !selectedTime
                ) {

                    resultCount.textContent =
                        'Showing all '
                        +
                        visibleCount
                        +
                        ' available classes';

                } else {

                    resultCount.textContent =
                        'Showing '
                        +
                        visibleCount
                        +
                        ' matching '
                        +
                        (
                            visibleCount === 1
                                ? 'class'
                                : 'classes'
                        );
                }
            }
        }



        /*
        |--------------------------------------------------------------------------
        | Filter Events
        |--------------------------------------------------------------------------
        */

        if (classFilter) {

            classFilter.addEventListener(
                'change',
                applyFilters
            );
        }


        if (dayFilter) {

            dayFilter.addEventListener(
                'change',
                applyFilters
            );
        }


        if (timeFilter) {

            timeFilter.addEventListener(
                'change',
                applyFilters
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Clear Filters
        |--------------------------------------------------------------------------
        */

        if (clearFilters) {

            clearFilters.addEventListener(
                'click',
                function () {

                    if (classFilter) {
                        classFilter.value = '';
                    }


                    if (dayFilter) {
                        dayFilter.value = '';
                    }


                    if (timeFilter) {
                        timeFilter.value = '';
                    }


                    applyFilters();
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Final Browser-Side Validation
        |--------------------------------------------------------------------------
        |
        | Backend also performs this check.
        |--------------------------------------------------------------------------
        */

        if (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    const selectedBySection =
                        {};


                    let invalidClassName =
                        null;


                    allSelectionCheckboxes
                        .forEach(
                            function (checkbox) {

                                if (!checkbox.checked) {
                                    return;
                                }


                                const sectionId =
                                    checkbox
                                        .dataset
                                        .section;


                                const sectionName =
                                    checkbox
                                        .dataset
                                        .sectionName;


                                if (
                                    selectedBySection[
                                        sectionId
                                    ]
                                ) {

                                    invalidClassName =
                                        sectionName;


                                    return;
                                }


                                selectedBySection[
                                    sectionId
                                ] =
                                    true;
                            }
                        );


                    if (invalidClassName) {

                        event.preventDefault();


                        showSelectionError(
                            'Only one class time can be selected for '
                            +
                            invalidClassName
                            +
                            '.'
                        );
                    }
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Initial Filter
        |--------------------------------------------------------------------------
        */

        applyFilters();

    }
);

</script>

@endpush
