@extends('layouts.admin')

@section('title', 'Edit Section Offering')

@section('page-title', 'Edit Section Offering')

@php
    $breadcrumbs = [
        [
            'label' => 'Section Offerings',
            'url' => route(
                'admin.section-offerings.index'
            ),
        ],
        [
            'label' => 'Edit',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    <div class="mx-auto max-w-3xl">

        <div class="rounded-xl border border-gray-200
                    bg-white shadow-sm">

            <div class="border-b border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800">
                    Edit Section Offering
                </h2>
            </div>

            <form
                method="POST"
                action="{{ route(
                    'admin.section-offerings.update',
                    $sectionOffering
                ) }}"
            >
                @csrf
                @method('PUT')

                <div class="space-y-6 p-6">

                    <div>
                        <label
                            for="section_id"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Section
                        </label>

                        <select
                            name="section_id"
                            id="section_id"
                            required
                            class="w-full rounded-lg
                                   border-gray-300"
                        >
                            @foreach ($sections as $section)
                                <option
                                    value="{{ $section->id }}"
                                    @selected(
                                        old(
                                            'section_id',
                                            $sectionOffering->section_id
                                        ) == $section->id
                                    )
                                >
                                    {{ $section->section_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('section_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="day_id"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Day
                        </label>

                        <select
                            name="day_id"
                            id="day_id"
                            required
                            class="w-full rounded-lg
                                   border-gray-300"
                        >
                            @foreach ($days as $day)
                                <option
                                    value="{{ $day->id }}"
                                    @selected(
                                        old(
                                            'day_id',
                                            $sectionOffering->day_id
                                        ) == $day->id
                                    )
                                >
                                    {{ $day->day_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('day_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="start_time"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Start Time
                        </label>

                        <input
                            type="time"
                            name="start_time"
                            id="start_time"
                            value="{{ old(
                                'start_time',
                                substr(
                                    $sectionOffering->start_time,
                                    0,
                                    5
                                )
                            ) }}"
                            required
                            class="w-full rounded-lg
                                   border-gray-300"
                        >

                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="duration_minutes"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Duration in Minutes
                        </label>

                        <input
                            type="number"
                            name="duration_minutes"
                            id="duration_minutes"
                            value="{{ old(
                                'duration_minutes',
                                $sectionOffering->duration_minutes
                            ) }}"
                            min="5"
                            max="480"
                            step="5"
                            required
                            class="w-full rounded-lg
                                   border-gray-300"
                        >

                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="end_time"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Calculated End Time
                        </label>

                        <input
                            type="time"
                            id="end_time"
                            value="{{ substr(
                                $sectionOffering->end_time,
                                0,
                                5
                            ) }}"
                            readonly
                            class="w-full rounded-lg
                                   border-gray-300 bg-gray-100"
                        >
                    </div>

                    <div>
                        <label
                            for="max_seats"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Maximum Seats
                        </label>

                        <input
                            type="number"
                            name="max_seats"
                            id="max_seats"
                            value="{{ old(
                                'max_seats',
                                $sectionOffering->max_seats
                            ) }}"
                            min="1"
                            max="1000"
                            required
                            class="w-full rounded-lg
                                   border-gray-300"
                        >

                        @error('max_seats')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <label class="inline-flex items-center gap-3">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="rounded border-gray-300
                                       text-blue-600"
                                @checked(
                                    old(
                                        'is_active',
                                        $sectionOffering->is_active
                                    )
                                )
                            >

                            <span class="text-sm font-semibold
                                         text-gray-700">
                                Active Offering
                            </span>

                        </label>
                    </div>

                </div>

                <div class="flex justify-end gap-3 border-t
                            border-gray-200 bg-gray-50 p-6">

                    <a
                        href="{{ route(
                            'admin.section-offerings.index'
                        ) }}"
                        class="rounded-lg border border-gray-300
                               bg-white px-4 py-2.5 text-sm
                               font-semibold text-gray-700"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600
                               px-4 py-2.5 text-sm
                               font-semibold text-white
                               hover:bg-blue-700"
                    >
                        Update Offering
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startTimeInput =
            document.getElementById('start_time');

        const durationInput =
            document.getElementById('duration_minutes');

        const endTimeInput =
            document.getElementById('end_time');

        function calculateEndTime() {
            const startTime = startTimeInput.value;
            const duration = parseInt(durationInput.value);

            if (!startTime || !duration) {
                endTimeInput.value = '';
                return;
            }

            const parts = startTime.split(':');

            const totalMinutes =
                (parseInt(parts[0]) * 60) +
                parseInt(parts[1]) +
                duration;

            if (totalMinutes >= 1440) {
                endTimeInput.value = '';
                return;
            }

            const endHours =
                Math.floor(totalMinutes / 60);

            const endMinutes =
                totalMinutes % 60;

            endTimeInput.value =
                String(endHours).padStart(2, '0') +
                ':' +
                String(endMinutes).padStart(2, '0');
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
