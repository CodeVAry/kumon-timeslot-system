@extends('layouts.admin')

@section('title', 'Edit Schedule')

@section('page-title', 'Edit Schedule')


@php

    /*
    |--------------------------------------------------------------------------
    | Current Offering
    |--------------------------------------------------------------------------
    */

    $currentOffering = $enrolment->sectionOffering;

    $currentOfferingId =
        $enrolment->section_offering_id;


    /*
    |--------------------------------------------------------------------------
    | Selected Offering
    |--------------------------------------------------------------------------
    |
    | If validation fails, old() keeps the staff member's selection.
    |
    */

    $selectedOfferingId = old(
        'section_offering_id',
        $currentOfferingId
    );


    /*
    |--------------------------------------------------------------------------
    | Primary Guardian
    |--------------------------------------------------------------------------
    */

    $primaryGuardian = null;

    foreach ($student->guardians as $guardian) {

        if ($guardian->pivot->is_primary) {

            $primaryGuardian = $guardian;

            break;
        }
    }


    /*
     * If no guardian is marked primary,
     * use the first linked guardian.
     */
    if (!$primaryGuardian) {

        $primaryGuardian =
            $student->guardians->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Offerings For JavaScript
    |--------------------------------------------------------------------------
    |
    | We prepare a simple PHP array first.
    | This avoids the Blade @json parsing problem.
    |
    */

    $offeringsData = [];

    foreach ($offerings as $offering) {

        $offeringsData[] = [

            'id' =>
                $offering->id,

            'section_id' =>
                $offering->section_id,

            'section_name' =>
                $offering->section
                    ? $offering->section->section_name
                    : null,

            'day_id' =>
                $offering->day_id,

            'day_name' =>
                $offering->day
                    ? $offering->day->day_name
                    : null,

            'start_time' =>
                $offering->start_time,

            'end_time' =>
                $offering->end_time,

            'duration_minutes' =>
                $offering->duration_minutes,

            'max_seats' =>
                $offering->max_seats,

            'allocated_seats' =>
                $offering->allocated_seats,

        ];
    }

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        BACK
    ========================================================== --}}
    <div>

        <a
            href="{{ route(
                'admin.student-enrolments.edit-list',
                $student
            ) }}"
            class="inline-flex items-center gap-2
                   text-sm font-semibold
                   text-blue-600
                   hover:text-blue-800"
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
                    d="M10.5 19.5 3 12m0 0
                       7.5-7.5M3 12h18"
                />
            </svg>

            Back to Current Classes

        </a>

    </div>



    {{-- =========================================================
        CURRENT CLASS PANEL
    ========================================================== --}}
    <section
        class="rounded-2xl
               border border-cyan-100
               bg-cyan-50
               px-6 py-6"
    >

        <p
            class="text-sm
                   font-bold
                   text-cyan-700"
        >
            Current Class
        </p>


        @if ($currentOffering)

            <h2
                class="mt-4
                       text-2xl
                       font-bold
                       text-slate-900"
            >

                {{
                    $currentOffering->section
                        ? $currentOffering
                            ->section
                            ->section_name
                        : '—'
                }}

                ·

                {{
                    $currentOffering->day
                        ? $currentOffering
                            ->day
                            ->day_name
                        : '—'
                }}

                ·

                {{
                    \Carbon\Carbon::parse(
                        $currentOffering->start_time
                    )->format('g:i A')
                }}

                –

                {{
                    \Carbon\Carbon::parse(
                        $currentOffering->end_time
                    )->format('g:i A')
                }}

                ({{ $currentOffering->duration_minutes }} min)

            </h2>


        @else

            <h2
                class="mt-4
                       text-xl
                       font-bold
                       text-slate-900"
            >
                Class information unavailable
            </h2>

        @endif


        <div
            class="mt-3 flex
                   flex-wrap items-center
                   gap-x-2 gap-y-1
                   text-sm
                   text-slate-500"
        >

            <span>
                Student:
            </span>

            <span
                class="font-medium
                       text-slate-700"
            >
                {{ $student->first_name }}
                {{ $student->last_name }}
            </span>


            @if ($primaryGuardian)

                <span>
                    ·
                </span>

                <span>
                    Guardian:
                </span>

                <span
                    class="font-medium
                           text-slate-700"
                >
                    {{ $primaryGuardian->first_name }}
                    {{ $primaryGuardian->last_name }}
                </span>

            @endif

        </div>

    </section>



    {{-- =========================================================
        ERRORS
    ========================================================== --}}
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
                Please check the selected schedule.
            </p>


            <ul
                class="mt-2
                       list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600"
            >

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
        EDIT FORM
    ========================================================== --}}
    <form
        method="POST"
        action="{{ route(
            'admin.student-enrolments.update',
            [
                'student' => $student,
                'enrolment' => $enrolment,
            ]
        ) }}"
        id="scheduleForm"
    >

        @csrf
        @method('PATCH')


        {{--
            Only this value is submitted
            to the controller.
        --}}
        <input
            type="hidden"
            name="section_offering_id"
            id="section_offering_id"
            value="{{ $selectedOfferingId }}"
        >


        <section
            class="rounded-2xl
                   border border-slate-200
                   bg-white
                   shadow-sm"
        >


            {{-- =================================================
                FORM TITLE
            ================================================== --}}
            <div
                class="border-b
                       border-slate-100
                       px-7 py-5"
            >

                <h2
                    class="text-lg
                           font-bold
                           text-slate-900"
                >
                    Change Schedule
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Select the class first,
                    then choose an available
                    day and time.
                </p>

            </div>



            {{-- =================================================
                DROPDOWNS
            ================================================== --}}
            <div
                class="grid gap-6
                       p-7
                       lg:grid-cols-3"
            >


                {{-- =============================================
                    CLASS / SECTION
                ============================================== --}}
                <div>

                    <label
                        for="section"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-800"
                    >
                        Class / Section
                    </label>


                    <select
                        id="section"
                        class="w-full
                               rounded-xl
                               border-slate-300
                               bg-white
                               px-4 py-3
                               text-sm
                               text-slate-800
                               shadow-sm
                               focus:border-cyan-500
                               focus:ring-cyan-500"
                    >

                        <option value="">
                            Select Class / Section
                        </option>

                    </select>


                    <p
                        class="mt-2
                               text-xs
                               text-slate-400"
                    >
                        Choose the class the
                        student will attend.
                    </p>

                </div>



                {{-- =============================================
                    DAY
                ============================================== --}}
                <div>

                    <label
                        for="day"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-800"
                    >
                        Day
                    </label>


                    <select
                        id="day"
                        disabled
                        class="w-full
                               rounded-xl
                               border-slate-300
                               bg-white
                               px-4 py-3
                               text-sm
                               text-slate-800
                               shadow-sm
                               disabled:cursor-not-allowed
                               disabled:bg-slate-100
                               disabled:text-slate-400
                               focus:border-cyan-500
                               focus:ring-cyan-500"
                    >

                        <option value="">
                            Select Day
                        </option>

                    </select>


                    <p
                        class="mt-2
                               text-xs
                               text-slate-400"
                    >
                        Only days available
                        for the selected class
                        are displayed.
                    </p>

                </div>



                {{-- =============================================
                    TIME
                ============================================== --}}
                <div>

                    <label
                        for="time"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-800"
                    >
                        Time
                    </label>


                    <select
                        id="time"
                        disabled
                        class="w-full
                               rounded-xl
                               border-slate-300
                               bg-white
                               px-4 py-3
                               text-sm
                               text-slate-800
                               shadow-sm
                               disabled:cursor-not-allowed
                               disabled:bg-slate-100
                               disabled:text-slate-400
                               focus:border-cyan-500
                               focus:ring-cyan-500"
                    >

                        <option value="">
                            Select Time
                        </option>

                    </select>


                    <p
                        class="mt-2
                               text-xs
                               text-slate-400"
                    >
                        Full times are disabled.
                    </p>

                </div>

            </div>



            {{-- =================================================
                SELECTED SCHEDULE
            ================================================== --}}
            <div
                id="selectedClassBox"
                class="mx-7 mb-7 hidden
                       rounded-xl
                       border
                       border-cyan-100
                       bg-cyan-50
                       px-5 py-4"
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
                                   text-cyan-700"
                        >
                            Selected Schedule
                        </p>


                        <p
                            id="selectedClassName"
                            class="mt-1
                                   font-semibold
                                   text-slate-900"
                        >
                        </p>

                    </div>


                    <div>

                        <span
                            id="seatStatus"
                            class="inline-flex
                                   rounded-full
                                   px-3 py-1.5
                                   text-xs
                                   font-semibold"
                        >
                        </span>

                    </div>

                </div>

            </div>



            {{-- =================================================
                BUTTONS
            ================================================== --}}
            <div
                class="flex flex-col
                       justify-end gap-3
                       border-t
                       border-slate-100
                       bg-slate-50/50
                       px-7 py-6
                       sm:flex-row"
            >

                <a
                    href="{{ route(
                        'admin.students.show',
                        $student
                    ) }}"
                    class="inline-flex
                           min-w-[150px]
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           px-6 py-3
                           text-sm
                           font-semibold
                           text-slate-700
                           transition
                           hover:bg-slate-100"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    id="saveButton"
                    disabled
                    class="inline-flex
                           min-w-[180px]
                           items-center
                           justify-center
                           rounded-xl
                           bg-cyan-500
                           px-6 py-3
                           text-sm
                           font-semibold
                           text-white
                           shadow-sm
                           transition
                           hover:bg-cyan-600
                           disabled:cursor-not-allowed
                           disabled:bg-slate-300
                           disabled:text-slate-500
                           disabled:shadow-none"
                >
                    Save Schedule
                </button>

            </div>

        </section>

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
        | DATA FROM LARAVEL
        |--------------------------------------------------------------------------
        */

        const offerings =
            @json($offeringsData);


        /*
         * Original enrolment.
         *
         * This is used to identify
         * the real current class.
         */
        const currentOfferingId =
            Number(
                @json($currentOfferingId)
            );


        /*
         * Selected value.
         *
         * Normally this is the current
         * offering, but after validation
         * failure it can contain old().
         */
        const initialSelectedOfferingId =
            Number(
                @json($selectedOfferingId)
            );



        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const sectionSelect =
            document.getElementById(
                'section'
            );


        const daySelect =
            document.getElementById(
                'day'
            );


        const timeSelect =
            document.getElementById(
                'time'
            );


        const offeringInput =
            document.getElementById(
                'section_offering_id'
            );


        const selectedClassBox =
            document.getElementById(
                'selectedClassBox'
            );


        const selectedClassName =
            document.getElementById(
                'selectedClassName'
            );


        const seatStatus =
            document.getElementById(
                'seatStatus'
            );


        const saveButton =
            document.getElementById(
                'saveButton'
            );


        const scheduleForm =
            document.getElementById(
                'scheduleForm'
            );



        /*
        |--------------------------------------------------------------------------
        | FORMAT TIME
        |--------------------------------------------------------------------------
        */

        function formatTime(time) {

            if (!time) {
                return '';
            }


            const parts =
                String(time)
                    .split(':');


            let hour =
                Number(parts[0]);


            const minute =
                parts[1];


            const suffix =
                hour >= 12
                    ? 'PM'
                    : 'AM';


            hour =
                hour % 12;


            if (hour === 0) {

                hour = 12;
            }


            return (
                hour
                +
                ':'
                +
                minute
                +
                ' '
                +
                suffix
            );
        }



        /*
        |--------------------------------------------------------------------------
        | RESET SELECT
        |--------------------------------------------------------------------------
        */

        function resetSelect(
            select,
            placeholder
        ) {

            select.innerHTML =
                '';


            const option =
                document.createElement(
                    'option'
                );


            option.value =
                '';


            option.textContent =
                placeholder;


            select.appendChild(
                option
            );
        }



        /*
        |--------------------------------------------------------------------------
        | CLEAR FINAL SELECTION
        |--------------------------------------------------------------------------
        */

        function clearSelectedSchedule() {

            offeringInput.value =
                '';


            selectedClassBox
                .classList
                .add('hidden');


            selectedClassName
                .textContent =
                '';


            seatStatus
                .textContent =
                '';


            saveButton.disabled =
                true;
        }



        /*
        |--------------------------------------------------------------------------
        | LOAD UNIQUE CLASSES / SECTIONS
        |--------------------------------------------------------------------------
        |
        | This is the FIRST dropdown.
        |
        */

        function loadSections() {

            resetSelect(
                sectionSelect,
                'Select Class / Section'
            );


            const sections =
                [];


            offerings.forEach(
                function (offering) {

                    const alreadyExists =
                        sections.some(
                            function (section) {

                                return (
                                    Number(
                                        section.id
                                    )
                                    ===
                                    Number(
                                        offering.section_id
                                    )
                                );
                            }
                        );


                    if (!alreadyExists) {

                        sections.push({

                            id:
                                offering.section_id,

                            name:
                                offering.section_name,

                        });
                    }
                }
            );


            /*
             * Sort section names
             * alphabetically.
             */
            sections.sort(
                function (a, b) {

                    return String(
                        a.name
                    ).localeCompare(
                        String(
                            b.name
                        )
                    );
                }
            );


            sections.forEach(
                function (section) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        section.id;


                    option.textContent =
                        section.name;


                    sectionSelect.appendChild(
                        option
                    );
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | LOAD DAYS FOR SELECTED CLASS
        |--------------------------------------------------------------------------
        |
        | Class / Section
        |       ↓
        |      Day
        |
        */

        function loadDays(
            selectedSectionId,
            selectedDayId = null
        ) {

            resetSelect(
                daySelect,
                'Select Day'
            );


            resetSelect(
                timeSelect,
                'Select Time'
            );


            clearSelectedSchedule();


            daySelect.disabled =
                true;


            timeSelect.disabled =
                true;


            if (!selectedSectionId) {

                return;
            }


            const sectionOfferings =
                offerings.filter(
                    function (offering) {

                        return (
                            Number(
                                offering.section_id
                            )
                            ===
                            Number(
                                selectedSectionId
                            )
                        );
                    }
                );


            const days =
                [];


            sectionOfferings.forEach(
                function (offering) {

                    const alreadyExists =
                        days.some(
                            function (day) {

                                return (
                                    Number(
                                        day.id
                                    )
                                    ===
                                    Number(
                                        offering.day_id
                                    )
                                );
                            }
                        );


                    if (!alreadyExists) {

                        days.push({

                            id:
                                offering.day_id,

                            name:
                                offering.day_name,

                        });
                    }
                }
            );


            days.forEach(
                function (day) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        day.id;


                    option.textContent =
                        day.name;


                    daySelect.appendChild(
                        option
                    );
                }
            );


            if (days.length > 0) {

                daySelect.disabled =
                    false;
            }


            if (selectedDayId) {

                daySelect.value =
                    String(
                        selectedDayId
                    );
            }
        }



        /*
        |--------------------------------------------------------------------------
        | LOAD TIMES
        |--------------------------------------------------------------------------
        |
        | Class / Section
        |       ↓
        |      Day
        |       ↓
        |      Time
        |
        */

        function loadTimes(
            selectedSectionId,
            selectedDayId,
            selectedOfferingId = null
        ) {

            resetSelect(
                timeSelect,
                'Select Time'
            );


            clearSelectedSchedule();


            timeSelect.disabled =
                true;


            if (
                !selectedSectionId ||
                !selectedDayId
            ) {

                return;
            }


            const matchingOfferings =
                offerings.filter(
                    function (offering) {

                        return (
                            Number(
                                offering.section_id
                            )
                            ===
                            Number(
                                selectedSectionId
                            )
                            &&
                            Number(
                                offering.day_id
                            )
                            ===
                            Number(
                                selectedDayId
                            )
                        );
                    }
                );


            /*
             * Sort by start time.
             */
            matchingOfferings.sort(
                function (a, b) {

                    return String(
                        a.start_time
                    ).localeCompare(
                        String(
                            b.start_time
                        )
                    );
                }
            );


            matchingOfferings.forEach(
                function (offering) {

                    const allocatedSeats =
                        Number(
                            offering.allocated_seats
                        );


                    const maximumSeats =
                        Number(
                            offering.max_seats
                        );


                    const availableSeats =
                        Math.max(
                            0,
                            maximumSeats
                            -
                            allocatedSeats
                        );


                    const isCurrent =
                        Number(
                            offering.id
                        )
                        ===
                        Number(
                            currentOfferingId
                        );


                    /*
                     * Current class must remain
                     * selectable even when full.
                     */
                    const isFull =
                        !isCurrent
                        &&
                        availableSeats <= 0;


                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        offering.id;


                    let optionText =
                        formatTime(
                            offering.start_time
                        )
                        +
                        ' – '
                        +
                        formatTime(
                            offering.end_time
                        );


                    /*
                     * Add duration.
                     */
                    optionText +=
                        ' ('
                        +
                        offering.duration_minutes
                        +
                        ' min)';


                    /*
                     * Availability.
                     */
                    if (isCurrent) {

                        optionText +=
                            ' — Current';

                    }
                    else if (isFull) {

                        optionText +=
                            ' — Full';

                    }
                    else {

                        optionText +=
                            ' — '
                            +
                            availableSeats
                            +
                            (
                                availableSeats === 1
                                    ? ' seat available'
                                    : ' seats available'
                            );
                    }


                    option.textContent =
                        optionText;


                    /*
                     * Full class cannot
                     * be selected.
                     */
                    option.disabled =
                        isFull;


                    timeSelect.appendChild(
                        option
                    );
                }
            );


            if (
                matchingOfferings.length > 0
            ) {

                timeSelect.disabled =
                    false;
            }


            if (selectedOfferingId) {

                timeSelect.value =
                    String(
                        selectedOfferingId
                    );


                /*
                 * Check whether selected
                 * option was actually loaded.
                 */
                if (timeSelect.value) {

                    updateSelectedOffering();
                }
            }
        }



        /*
        |--------------------------------------------------------------------------
        | FINAL SELECTED OFFERING
        |--------------------------------------------------------------------------
        */

        function updateSelectedOffering() {

            const selectedOfferingId =
                Number(
                    timeSelect.value
                );


            if (!selectedOfferingId) {

                clearSelectedSchedule();

                return;
            }


            const offering =
                offerings.find(
                    function (item) {

                        return (
                            Number(
                                item.id
                            )
                            ===
                            Number(
                                selectedOfferingId
                            )
                        );
                    }
                );


            if (!offering) {

                clearSelectedSchedule();

                return;
            }


            const allocatedSeats =
                Number(
                    offering.allocated_seats
                );


            const maximumSeats =
                Number(
                    offering.max_seats
                );


            const availableSeats =
                Math.max(
                    0,
                    maximumSeats
                    -
                    allocatedSeats
                );


            const isCurrent =
                Number(
                    offering.id
                )
                ===
                Number(
                    currentOfferingId
                );


            /*
             * Store actual offering ID.
             */
            offeringInput.value =
                offering.id;


            /*
             * Show selected schedule.
             */
            selectedClassName.textContent =
                offering.section_name
                +
                ' · '
                +
                offering.day_name
                +
                ' · '
                +
                formatTime(
                    offering.start_time
                )
                +
                ' – '
                +
                formatTime(
                    offering.end_time
                )
                +
                ' · '
                +
                offering.duration_minutes
                +
                ' min';


            /*
             * Reset badge classes.
             */
            seatStatus.className =
                'inline-flex rounded-full '
                +
                'px-3 py-1.5 '
                +
                'text-xs font-semibold';


            /*
             * Current class.
             */
            if (isCurrent) {

                seatStatus.textContent =
                    'Current Class';


                seatStatus.classList.add(
                    'bg-blue-100',
                    'text-blue-700'
                );


                saveButton.disabled =
                    false;
            }


            /*
             * Available class.
             */
            else if (
                availableSeats > 0
            ) {

                seatStatus.textContent =
                    availableSeats
                    +
                    (
                        availableSeats === 1
                            ? ' seat available'
                            : ' seats available'
                    );


                seatStatus.classList.add(
                    'bg-green-100',
                    'text-green-700'
                );


                saveButton.disabled =
                    false;
            }


            /*
             * Full class.
             */
            else {

                seatStatus.textContent =
                    'Class Full';


                seatStatus.classList.add(
                    'bg-red-100',
                    'text-red-700'
                );


                saveButton.disabled =
                    true;
            }


            selectedClassBox
                .classList
                .remove('hidden');
        }



        /*
        |--------------------------------------------------------------------------
        | CLASS / SECTION CHANGED
        |--------------------------------------------------------------------------
        */

        sectionSelect.addEventListener(
            'change',
            function () {

                loadDays(
                    this.value
                );
            }
        );



        /*
        |--------------------------------------------------------------------------
        | DAY CHANGED
        |--------------------------------------------------------------------------
        */

        daySelect.addEventListener(
            'change',
            function () {

                loadTimes(
                    sectionSelect.value,
                    this.value
                );
            }
        );



        /*
        |--------------------------------------------------------------------------
        | TIME CHANGED
        |--------------------------------------------------------------------------
        */

        timeSelect.addEventListener(
            'change',
            function () {

                updateSelectedOffering();
            }
        );



        /*
        |--------------------------------------------------------------------------
        | INITIAL PAGE LOAD
        |--------------------------------------------------------------------------
        */

        /*
         * First load all classes.
         */
        loadSections();


        /*
         * Find selected offering.
         *
         * Usually current offering.
         * If validation failed,
         * this can be old selection.
         */
        let initialOffering =
            offerings.find(
                function (offering) {

                    return (
                        Number(
                            offering.id
                        )
                        ===
                        Number(
                            initialSelectedOfferingId
                        )
                    );
                }
            );


        /*
         * If old selection is not found,
         * fall back to current offering.
         */
        if (!initialOffering) {

            initialOffering =
                offerings.find(
                    function (offering) {

                        return (
                            Number(
                                offering.id
                            )
                            ===
                            Number(
                                currentOfferingId
                            )
                        );
                    }
                );
        }


        /*
         * Load:
         *
         * Class
         * ↓
         * Day
         * ↓
         * Time
         */
        if (initialOffering) {

            /*
             * CLASS
             */
            sectionSelect.value =
                String(
                    initialOffering.section_id
                );


            /*
             * DAY
             */
            loadDays(
                initialOffering.section_id,
                initialOffering.day_id
            );


            /*
             * TIME
             */
            loadTimes(
                initialOffering.section_id,
                initialOffering.day_id,
                initialOffering.id
            );
        }



        /*
        |--------------------------------------------------------------------------
        | FORM VALIDATION
        |--------------------------------------------------------------------------
        */

        scheduleForm.addEventListener(
            'submit',
            function (event) {

                /*
                 * Class required.
                 */
                if (
                    !sectionSelect.value
                ) {

                    event.preventDefault();


                    alert(
                        'Please select a class or section.'
                    );


                    sectionSelect.focus();

                    return;
                }


                /*
                 * Day required.
                 */
                if (
                    !daySelect.value
                ) {

                    event.preventDefault();


                    alert(
                        'Please select a day.'
                    );


                    daySelect.focus();

                    return;
                }


                /*
                 * Time required.
                 */
                if (
                    !timeSelect.value
                    ||
                    !offeringInput.value
                ) {

                    event.preventDefault();


                    alert(
                        'Please select a time.'
                    );


                    timeSelect.focus();

                    return;
                }
            }
        );

    }
);

</script>

@endpush
