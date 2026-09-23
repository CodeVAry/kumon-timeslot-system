@extends('layouts.admin')

@section('title', 'Add Class')

@section('page-title', 'Add Class')

@section('content')

<div class="space-y-6">

    <div>
        <a
            href="{{ route('admin.students.show', $student) }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800"
        >
            ← Back to Student Profile
        </a>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">
            Add Classes
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            {{ $student->first_name }} {{ $student->last_name }}
            · Student ID: {{ $student->external_id ?: 'Not added yet' }}
        </p>
    </section>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">
            <p class="font-semibold text-red-700">
                Please check the information below.
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
        action="{{ route('admin.student-enrolments.store', $student) }}"
    >
        @csrf

        @php
            $selectedOfferingIds =
                collect(old('section_offering_ids', []))
                    ->map(fn ($id) => (string) $id)
                    ->all();
        @endphp

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Available Classes
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Select one or more classes for this student.
                    </p>
                </div>

                <div
                    id="selectedClassCount"
                    class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700"
                >
                    0 selected
                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Select
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Section
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Day
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Time
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Seats
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Availability
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($offerings as $offering)

                            @php
                                $availableSeats =
                                    max(
                                        0,
                                        $offering->max_seats
                                        -
                                        $offering->allocated_seats
                                    );

                                $isFull =
                                    $availableSeats <= 0;

                                $isSelected =
                                    in_array(
                                        (string) $offering->id,
                                        $selectedOfferingIds,
                                        true
                                    );
                            @endphp

                            <tr class="transition hover:bg-blue-50/40 {{ $isSelected ? 'bg-blue-50/60' : '' }}">

                                <td class="px-6 py-5">
                                    <input
                                        type="checkbox"
                                        name="section_offering_ids[]"
                                        value="{{ $offering->id }}"
                                        @checked($isSelected)
                                        class="class-checkbox h-5 w-5 rounded border-2 border-slate-600 text-blue-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                    >
                                </td>

                                <td class="px-6 py-5 text-sm font-semibold text-slate-900">
                                    {{ $offering->section?->section_name ?? '—' }}
                                </td>

                                <td class="px-6 py-5 text-sm text-slate-700">
                                    {{ $offering->day?->day_name ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-5 text-sm text-slate-700">
                                    {{ \Carbon\Carbon::parse($offering->start_time)->format('g:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($offering->end_time)->format('g:i A') }}
                                </td>

                                <td class="px-6 py-5 text-sm text-slate-700">
                                    {{ $offering->allocated_seats }}
                                    /
                                    {{ $offering->max_seats }}
                                </td>

                                <td class="px-6 py-5">
                                    @if ($isFull)
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Full
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            {{ $availableSeats }} available
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center text-sm text-slate-500">
                                    No additional classes are currently available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

        @if ($offerings->count() > 0)

            <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-slate-900">
                    Enrolment Type
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    This choice applies to all selected classes.
                </p>

                <div class="mt-5 grid gap-4 md:grid-cols-2">

                    <label class="cursor-pointer rounded-xl border-2 border-slate-400 p-5 transition hover:border-blue-500 hover:bg-blue-50">

                        <div class="flex items-start gap-3">

                            <input
                                type="radio"
                                name="enrolment_type"
                                value="confirmed"
                                class="mt-1 h-5 w-5 border-2 border-slate-600 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                @checked(old('enrolment_type', 'confirmed') === 'confirmed')
                                required
                            >

                            <div>
                                <p class="font-semibold text-slate-900">
                                    Enrol Student
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    All selected classes receive confirmed seats.
                                </p>
                            </div>

                        </div>

                    </label>

                    <label class="cursor-pointer rounded-xl border-2 border-slate-400 p-5 transition hover:border-purple-500 hover:bg-purple-50">

                        <div class="flex items-start gap-3">

                            <input
                                type="radio"
                                name="enrolment_type"
                                value="wishlist"
                                class="mt-1 h-5 w-5 border-2 border-slate-600 text-purple-600 focus:ring-2 focus:ring-purple-500"
                                @checked(old('enrolment_type') === 'wishlist')
                                required
                            >

                            <div>
                                <p class="font-semibold text-purple-800">
                                    Add to Wishlist
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    All selected classes are added as wishlist requests.
                                </p>
                            </div>

                        </div>

                    </label>

                </div>

            </section>

            <div class="mt-6 flex justify-end gap-3">

                <a
                    href="{{ route('admin.students.show', $student) }}"
                    class="rounded-xl border border-slate-400 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Add Selected Classes
                </button>

            </div>

        @endif

    </form>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const checkboxes =
            document.querySelectorAll(
                '.class-checkbox'
            );

        const counter =
            document.getElementById(
                'selectedClassCount'
            );

        function updateSelectedCount()
        {
            if (!counter) {
                return;
            }

            const selectedCount =
                document.querySelectorAll(
                    '.class-checkbox:checked'
                ).length;

            counter.textContent =
                selectedCount + ' selected';
        }

        checkboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    updateSelectedCount
                );
            }
        );

        updateSelectedCount();
    }
);
</script>

@endpush
