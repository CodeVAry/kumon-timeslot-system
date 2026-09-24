@extends('layouts.admin')

@section('title', 'Edit Schedule')

@section('page-title', 'Edit Schedule')


@php

    $currentOffering =
        $enrolment
            ->sectionOffering;


    $currentOfferingId =
        (int)
        $enrolment
            ->section_offering_id;


    $currentSubSectionId =
        $enrolment
            ->sub_section_id
            ? (int) $enrolment->sub_section_id
            : null;


    $selectedOfferingId =
        (int)
        old(
            'section_offering_id',
            $currentOfferingId
        );


    $selectedSubSectionId =
        old(
            'sub_section_id',
            $currentSubSectionId
        );


    $offeringsData =
        $offerings
            ->map(
                function ($offering) {

                    return [
                        'id' =>
                            (int) $offering->id,

                        'section_id' =>
                            (int) $offering->section_id,

                        'section_name' =>
                            $offering
                                ->section
                                ?->section_name,

                        'day_id' =>
                            (int) $offering->day_id,

                        'day_name' =>
                            $offering
                                ->day
                                ?->day_name,

                        'start_time' =>
                            $offering->start_time,

                        'end_time' =>
                            $offering->end_time,

                        'max_seats' =>
                            (int) $offering->max_seats,

                        'allocated_seats' =>
                            (int) $offering->allocated_seats,

                        'sub_sections' =>
                            $offering
                                ->subSections
                                ->map(
                                    function ($subSection) {

                                        return [
                                            'id' =>
                                                (int) $subSection->id,

                                            'name' =>
                                                $subSection
                                                    ->sub_section_name,
                                        ];
                                    }
                                )
                                ->values()
                                ->all(),
                    ];
                }
            )
            ->values()
            ->all();

@endphp


@section('content')

<div class="space-y-6">

    <div>

        <a
            href="{{ route(
                'admin.student-enrolments.edit-list',
                $student
            ) }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800"
        >
            ← Back to Current Classes
        </a>

    </div>


    <section class="rounded-2xl border border-cyan-100 bg-cyan-50 px-6 py-6">

        <p class="text-sm font-bold text-cyan-700">
            Current Class
        </p>


        @if ($currentOffering)

            <h2 class="mt-3 text-2xl font-bold text-slate-900">

                {{ $currentOffering->section?->section_name ?? '—' }}

                @if ($enrolment->subSection)

                    — {{ $enrolment->subSection->sub_section_name }}

                @endif

                · {{ $currentOffering->day?->day_name ?? '—' }}

                ·
                {{ \Carbon\Carbon::parse($currentOffering->start_time)->format('g:i A') }}
                –
                {{ \Carbon\Carbon::parse($currentOffering->end_time)->format('g:i A') }}

            </h2>

        @endif


        <p class="mt-3 text-sm text-slate-600">
            Student:
            <strong>
                {{ $student->first_name }} {{ $student->last_name }}
            </strong>
        </p>

    </section>


    @if ($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">

            <p class="font-semibold text-red-700">
                Please check the selected schedule.
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


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


        <input
            type="hidden"
            name="section_offering_id"
            id="section_offering_id"
            value="{{ $selectedOfferingId }}"
        >


        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-7 py-5">

                <h2 class="text-lg font-bold text-slate-900">
                    Change Schedule
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Choose class, day and time. If that offering has sub-sections,
                    choose the correct sub-section as well.
                </p>

                <p class="mt-2 text-sm font-medium text-cyan-700">
                    Interactive students must be assigned to English or Math.
                    Both subjects share one maximum capacity of 5 students.
                </p>

            </div>


            <div class="grid gap-6 p-7 md:grid-cols-2 xl:grid-cols-4">

                <div>

                    <label
                        for="section"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Class / Section
                    </label>

                    <select
                        id="section"
                        class="w-full rounded-xl border-2 border-slate-400 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-cyan-600 focus:ring-cyan-500"
                    >
                        <option value="">
                            Select Class / Section
                        </option>
                    </select>

                </div>


                <div>

                    <label
                        for="day"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Day
                    </label>

                    <select
                        id="day"
                        disabled
                        class="w-full rounded-xl border-2 border-slate-400 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 focus:border-cyan-600 focus:ring-cyan-500"
                    >
                        <option value="">
                            Select Day
                        </option>
                    </select>

                </div>


                <div>

                    <label
                        for="time"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Time
                    </label>

                    <select
                        id="time"
                        disabled
                        class="w-full rounded-xl border-2 border-slate-400 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 focus:border-cyan-600 focus:ring-cyan-500"
                    >
                        <option value="">
                            Select Time
                        </option>
                    </select>

                </div>


                <div>

                    <label
                        for="sub_section_id"
                        class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                        Subject / Sub-section
                    </label>

                    <select
                        id="sub_section_id"
                        name="sub_section_id"
                        disabled
                        class="w-full rounded-xl border-2 border-slate-400 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 focus:border-cyan-600 focus:ring-cyan-500"
                    >
                        <option value="">
                            Not required
                        </option>
                    </select>

                </div>

            </div>


            <div
                id="selectedClassBox"
                class="mx-7 mb-7 hidden rounded-xl border border-cyan-200 bg-cyan-50 px-5 py-4"
            >

                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">
                    Selected Schedule
                </p>

                <p
                    id="selectedClassName"
                    class="mt-1 font-semibold text-slate-900"
                ></p>

                <span
                    id="seatStatus"
                    class="mt-3 inline-flex rounded-full px-3 py-1.5 text-xs font-semibold"
                ></span>

            </div>


            <div class="flex flex-col justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-7 py-6 sm:flex-row">

                <a
                    href="{{ route('admin.students.show', $student) }}"
                    class="inline-flex min-w-[150px] items-center justify-center rounded-xl border-2 border-slate-400 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    id="saveButton"
                    disabled
                    class="inline-flex min-w-[180px] items-center justify-center rounded-xl bg-cyan-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-600 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 disabled:shadow-none"
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

        const offerings =
            @json($offeringsData);


        const initialOfferingId =
            Number(
                @json($selectedOfferingId)
            );


        const initialSubSectionId =
            @json($selectedSubSectionId);


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


        const subSectionSelect =
            document.getElementById(
                'sub_section_id'
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


        function formatTime(time)
        {
            if (!time) {

                return '';
            }


            const parts =
                String(time)
                    .split(':');


            let hour =
                Number(
                    parts[0]
                );


            const minute =
                parts[1];


            const suffix =
                hour >= 12
                    ? 'PM'
                    : 'AM';


            hour =
                hour % 12;


            if (
                hour
                ===
                0
            ) {

                hour =
                    12;
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


        function uniqueBy(
            items,
            key
        ) {

            const map =
                new Map();


            items.forEach(
                function (item) {

                    map.set(
                        item[key],
                        item
                    );
                }
            );


            return Array.from(
                map.values()
            );
        }


        function populateSections()
        {
            const sections =
                uniqueBy(
                    offerings,
                    'section_id'
                )
                    .sort(
                        function (a, b) {

                            return String(
                                a.section_name
                            ).localeCompare(
                                String(
                                    b.section_name
                                )
                            );
                        }
                    );


            sections.forEach(
                function (item) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        item.section_id;


                    option.textContent =
                        item.section_name;


                    sectionSelect.appendChild(
                        option
                    );
                }
            );
        }


        function populateDays(
            sectionId,
            selectedDayId = null
        )
        {
            resetSelect(
                daySelect,
                'Select Day'
            );


            resetSelect(
                timeSelect,
                'Select Time'
            );


            resetSelect(
                subSectionSelect,
                'Not required'
            );


            daySelect.disabled =
                !sectionId;


            timeSelect.disabled =
                true;


            subSectionSelect.disabled =
                true;


            offeringInput.value =
                '';


            saveButton.disabled =
                true;


            selectedClassBox.classList.add(
                'hidden'
            );


            if (!sectionId) {

                return;
            }


            const matching =
                offerings.filter(
                    function (item) {

                        return (
                            Number(
                                item.section_id
                            )
                            ===
                            Number(
                                sectionId
                            )
                        );
                    }
                );


            const days =
                uniqueBy(
                    matching,
                    'day_id'
                );


            days.forEach(
                function (item) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        item.day_id;


                    option.textContent =
                        item.day_name;


                    daySelect.appendChild(
                        option
                    );
                }
            );


            if (selectedDayId) {

                daySelect.value =
                    String(
                        selectedDayId
                    );
            }
        }


        function populateTimes(
            sectionId,
            dayId,
            selectedOfferingId = null
        )
        {
            resetSelect(
                timeSelect,
                'Select Time'
            );


            resetSelect(
                subSectionSelect,
                'Not required'
            );


            timeSelect.disabled =
                !dayId;


            subSectionSelect.disabled =
                true;


            offeringInput.value =
                '';


            saveButton.disabled =
                true;


            selectedClassBox.classList.add(
                'hidden'
            );


            if (
                !sectionId
                ||
                !dayId
            ) {

                return;
            }


            const matching =
                offerings.filter(
                    function (item) {

                        return (
                            Number(
                                item.section_id
                            )
                            ===
                            Number(
                                sectionId
                            )
                            &&
                            Number(
                                item.day_id
                            )
                            ===
                            Number(
                                dayId
                            )
                        );
                    }
                );


            matching.forEach(
                function (item) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        item.id;


                    option.textContent =
                        formatTime(
                            item.start_time
                        )
                        +
                        ' – '
                        +
                        formatTime(
                            item.end_time
                        );


                    const isCurrent =
                        Number(
                            item.id
                        )
                        ===
                        Number(
                            initialOfferingId
                        );


                    const full =
                        Number(
                            item.allocated_seats
                        )
                        >=
                        Number(
                            item.max_seats
                        )
                        &&
                        !isCurrent;


                    if (full) {

                        option.disabled =
                            true;


                        option.textContent +=
                            ' (Full)';
                    }


                    timeSelect.appendChild(
                        option
                    );
                }
            );


            if (selectedOfferingId) {

                timeSelect.value =
                    String(
                        selectedOfferingId
                    );
            }
        }


        function applyOffering(
            offeringId,
            preferredSubSectionId = null
        )
        {
            const offering =
                offerings.find(
                    function (item) {

                        return (
                            Number(
                                item.id
                            )
                            ===
                            Number(
                                offeringId
                            )
                        );
                    }
                );


            if (!offering) {

                offeringInput.value =
                    '';


                saveButton.disabled =
                    true;


                selectedClassBox.classList.add(
                    'hidden'
                );


                resetSelect(
                    subSectionSelect,
                    'Not required'
                );


                subSectionSelect.disabled =
                    true;


                return;
            }


            offeringInput.value =
                offering.id;


            resetSelect(
                subSectionSelect,
                (
                    offering.sub_sections.length
                    >
                    0
                )
                    ? 'Select Sub-section'
                    : 'Not required'
            );


            if (
                offering.sub_sections.length
                >
                0
            ) {

                subSectionSelect.disabled =
                    false;


                offering.sub_sections.forEach(
                    function (subSection) {

                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value =
                            subSection.id;


                        option.textContent =
                            subSection.name;


                        subSectionSelect.appendChild(
                            option
                        );
                    }
                );


                if (preferredSubSectionId) {

                    subSectionSelect.value =
                        String(
                            preferredSubSectionId
                        );
                }

            } else {

                subSectionSelect.disabled =
                    true;
            }


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
                );


            const isCurrent =
                Number(
                    offering.id
                )
                ===
                Number(
                    initialOfferingId
                );


            const available =
                Math.max(
                    0,
                    Number(
                        offering.max_seats
                    )
                    -
                    Number(
                        offering.allocated_seats
                    )
                    +
                    (
                        isCurrent
                            ? 1
                            : 0
                    )
                );


            seatStatus.textContent =
                available
                +
                ' seat'
                +
                (
                    available
                    ===
                    1
                        ? ''
                        : 's'
                )
                +
                ' available';


            seatStatus.className =
                'mt-3 inline-flex rounded-full px-3 py-1.5 text-xs font-semibold '
                +
                (
                    available
                    >
                    0
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                );


            selectedClassBox.classList.remove(
                'hidden'
            );


            updateSaveState();
        }


        function updateSaveState()
        {
            const offering =
                offerings.find(
                    function (item) {

                        return (
                            Number(
                                item.id
                            )
                            ===
                            Number(
                                offeringInput.value
                            )
                        );
                    }
                );


            if (!offering) {

                saveButton.disabled =
                    true;


                return;
            }


            if (
                offering.sub_sections.length
                >
                0
            ) {

                saveButton.disabled =
                    !subSectionSelect.value;

            } else {

                saveButton.disabled =
                    false;
            }
        }


        sectionSelect.addEventListener(
            'change',
            function () {

                populateDays(
                    sectionSelect.value
                );
            }
        );


        daySelect.addEventListener(
            'change',
            function () {

                populateTimes(
                    sectionSelect.value,
                    daySelect.value
                );
            }
        );


        timeSelect.addEventListener(
            'change',
            function () {

                applyOffering(
                    timeSelect.value
                );
            }
        );


        subSectionSelect.addEventListener(
            'change',
            updateSaveState
        );


        populateSections();


        const initialOffering =
            offerings.find(
                function (item) {

                    return (
                        Number(
                            item.id
                        )
                        ===
                        Number(
                            initialOfferingId
                        )
                    );
                }
            );


        if (initialOffering) {

            sectionSelect.value =
                String(
                    initialOffering.section_id
                );


            populateDays(
                initialOffering.section_id,
                initialOffering.day_id
            );


            populateTimes(
                initialOffering.section_id,
                initialOffering.day_id,
                initialOffering.id
            );


            applyOffering(
                initialOffering.id,
                initialSubSectionId
            );
        }
    }
);

</script>

@endpush
