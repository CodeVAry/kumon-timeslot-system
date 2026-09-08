@extends('layouts.admin')

@section('title', 'Create Section Offering')

@section('page-title', 'Create Section Offering')

@php
    $breadcrumbs = [
        [
            'label' => 'Section Offerings',
            'url' => route(
                'admin.section-offerings.index'
            ),
        ],
        [
            'label' => 'Create',
            'url' => null,
        ],
    ];
@endphp


@section('content')

<div class="mx-auto max-w-3xl">

    <div
        class="rounded-xl
               border border-gray-200
               bg-white
               shadow-sm"
    >

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
                Create Section Offering
            </h2>

            <p
                class="mt-1
                       text-sm
                       text-gray-500"
            >
                Select multiple days to create the same
                offering on each selected day.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route(
                'admin.section-offerings.store'
            ) }}"
        >

            @csrf


            <div class="space-y-6 p-6">


                {{-- Section --}}
                <div>

                    <label
                        for="section_id"
                        class="mb-2 block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Section
                        <span class="text-red-500">*</span>
                    </label>


                    <select
                        name="section_id"
                        id="section_id"
                        required
                        class="w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option value="">
                            Select Section
                        </option>


                        @foreach ($sections as $section)

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


                    @error('section_id')

                        <p
                            class="mt-1
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                {{-- Sub-sections --}}
                <div
                    id="sub-section-container"
                    class="hidden"
                >

                    <label
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Sub-sections
                        <span class="text-red-500">*</span>
                    </label>


                    <div
                        id="sub-section-options"
                        class="grid
                               gap-3
                               sm:grid-cols-3"
                    >
                    </div>


                    <p
                        class="mt-2
                               text-xs
                               text-gray-500"
                    >
                        Selected sub-sections share the same
                        maximum capacity for this offering.
                    </p>


                    @error('sub_section_ids')

                        <p
                            class="mt-2
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror


                    @error('sub_section_ids.*')

                        <p
                            class="mt-2
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                {{-- Days --}}
                <div>

                    <label
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Days
                        <span class="text-red-500">*</span>
                    </label>


                    <div
                        class="grid
                               gap-3
                               sm:grid-cols-2"
                    >

                        @foreach ($days as $day)

                            <label
                                class="flex
                                       items-center
                                       gap-3
                                       rounded-lg
                                       border
                                       border-gray-200
                                       p-3
                                       hover:bg-gray-50"
                            >

                                <input
                                    type="checkbox"
                                    name="day_ids[]"
                                    value="{{ $day->id }}"
                                    class="rounded
                                           border-gray-300
                                           text-blue-600
                                           focus:ring-blue-500"
                                    @checked(
                                        in_array(
                                            $day->id,
                                            old(
                                                'day_ids',
                                                []
                                            )
                                        )
                                    )
                                >


                                <span
                                    class="text-sm
                                           font-medium
                                           text-gray-700"
                                >
                                    {{ $day->day_name }}
                                </span>

                            </label>

                        @endforeach

                    </div>


                    @error('day_ids')

                        <p
                            class="mt-2
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                {{-- Start Time --}}
                <div>

                    <label
                        for="start_time"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Start Time
                        <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="time"
                        name="start_time"
                        id="start_time"
                        value="{{ old('start_time') }}"
                        required
                        class="w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >


                    @error('start_time')

                        <p
                            class="mt-1
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                {{-- Duration --}}
                <div>

                    <label
                        for="duration_minutes"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Duration in Minutes
                        <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="number"
                        name="duration_minutes"
                        id="duration_minutes"
                        value="{{ old(
                            'duration_minutes',
                            45
                        ) }}"
                        min="5"
                        max="480"
                        step="5"
                        required
                        class="w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >


                    @error('duration_minutes')

                        <p
                            class="mt-1
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                {{-- End Time --}}
                <div>

                    <label
                        for="end_time"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Calculated End Time
                    </label>


                    <input
                        type="time"
                        id="end_time"
                        readonly
                        class="w-full
                               rounded-lg
                               border-gray-300
                               bg-gray-100
                               text-gray-700"
                    >

                </div>



                {{-- Maximum Seats --}}
                <div>

                    <label
                        for="max_seats"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-gray-700"
                    >
                        Maximum Seats
                        <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="number"
                        name="max_seats"
                        id="max_seats"
                        value="{{ old('max_seats') }}"
                        min="1"
                        max="1000"
                        required
                        class="w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >


                    <p
                        id="capacity-help"
                        class="mt-2
                               text-xs
                               text-gray-500"
                    >
                        Maximum students for this class offering.
                    </p>


                    @error('max_seats')

                        <p
                            class="mt-1
                                   text-sm
                                   text-red-600"
                        >
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                {{-- Active --}}
                <div>

                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
                    >


                    <label
                        class="inline-flex
                               items-center
                               gap-3"
                    >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="rounded
                                   border-gray-300
                                   text-blue-600
                                   focus:ring-blue-500"
                            @checked(
                                old(
                                    'is_active',
                                    true
                                )
                            )
                        >


                        <span
                            class="text-sm
                                   font-semibold
                                   text-gray-700"
                        >
                            Active Offering
                        </span>

                    </label>

                </div>

            </div>


            <div
                class="flex
                       justify-end
                       gap-3
                       border-t
                       border-gray-200
                       bg-gray-50
                       p-6"
            >

                <a
                    href="{{ route(
                        'admin.section-offerings.index'
                    ) }}"
                    class="rounded-lg
                           border
                           border-gray-300
                           bg-white
                           px-4 py-2.5
                           text-sm
                           font-semibold
                           text-gray-700
                           hover:bg-gray-100"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="rounded-lg
                           bg-blue-600
                           px-4 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Create Offerings
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Sub-sections
    |--------------------------------------------------------------------------
    */

    const sections = @json($sectionsForJs);

    const oldSubSectionIds =
        @json(old('sub_section_ids', []));

    let selectedSubSections =
        oldSubSectionIds.map(
            id => parseInt(id)
        );


    const sectionSelect =
        document.getElementById(
            'section_id'
        );

    const subSectionContainer =
        document.getElementById(
            'sub-section-container'
        );

    const subSectionOptions =
        document.getElementById(
            'sub-section-options'
        );

    const capacityHelp =
        document.getElementById(
            'capacity-help'
        );


    function loadSubSections()
    {
        const sectionId =
            parseInt(
                sectionSelect.value
            );


        const section =
            sections.find(
                item =>
                    parseInt(item.id)
                    === sectionId
            );


        subSectionOptions.innerHTML =
            '';


        if (
            !section ||
            section.sub_sections.length === 0
        ) {
            subSectionContainer
                .classList
                .add('hidden');

            capacityHelp.textContent =
                'Maximum students for this class offering.';

            return;
        }


        subSectionContainer
            .classList
            .remove('hidden');


        section.sub_sections.forEach(
            function (subSection) {

                const checked =
                    selectedSubSections
                        .includes(
                            parseInt(
                                subSection.id
                            )
                        );


                const label =
                    document.createElement(
                        'label'
                    );


                label.className =
                    'flex items-center gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer hover:bg-blue-50';


                label.innerHTML = `
                    <input
                        type="checkbox"
                        name="sub_section_ids[]"
                        value="${subSection.id}"
                        ${checked ? 'checked' : ''}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >

                    <span class="text-sm font-semibold text-gray-700">
                        ${subSection.name}
                    </span>
                `;


                subSectionOptions
                    .appendChild(label);
            }
        );


        capacityHelp.textContent =
            'Maximum seats are shared across all selected sub-sections.';
    }


    sectionSelect.addEventListener(
        'change',
        function () {
            selectedSubSections = [];

            loadSubSections();
        }
    );


    loadSubSections();


    /*
    |--------------------------------------------------------------------------
    | End Time
    |--------------------------------------------------------------------------
    */

    const startTimeInput =
        document.getElementById(
            'start_time'
        );

    const durationInput =
        document.getElementById(
            'duration_minutes'
        );

    const endTimeInput =
        document.getElementById(
            'end_time'
        );


    function calculateEndTime()
    {
        const startTime =
            startTimeInput.value;

        const duration =
            parseInt(
                durationInput.value
            );


        if (
            !startTime ||
            !duration
        ) {
            endTimeInput.value = '';

            return;
        }


        const parts =
            startTime.split(':');


        const totalMinutes =
            (
                parseInt(parts[0]) * 60
            )
            +
            parseInt(parts[1])
            +
            duration;


        if (totalMinutes >= 1440) {
            endTimeInput.value = '';

            return;
        }


        const endHours =
            Math.floor(
                totalMinutes / 60
            );

        const endMinutes =
            totalMinutes % 60;


        endTimeInput.value =
            String(endHours)
                .padStart(2, '0')
            +
            ':'
            +
            String(endMinutes)
                .padStart(2, '0');
    }


    startTimeInput.addEventListener(
        'input',
        calculateEndTime
    );

    durationInput.addEventListener(
        'input',
        calculateEndTime
    );


    calculateEndTime();
});
</script>

@endpush
