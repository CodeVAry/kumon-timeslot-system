@extends('layouts.admin')

@section('title', 'Class Enrolment')

@section('page-title', 'Class Enrolment')

@php
    $oldConfirmed = array_map(
        'intval',
        old('confirmed_ids', [])
    );

    $oldWishlist = array_map(
        'intval',
        old('wishlist_ids', [])
    );
@endphp

@section('content')

<div class="mx-auto max-w-7xl">

    <div class="mb-6 flex flex-wrap items-center gap-3">

        <a
            href="{{ route(
                'admin.student-registration.student'
            ) }}"
            class="font-semibold text-blue-600"
        >
            ✓ Student Details
        </a>

        <span class="text-gray-400">→</span>

        <a
            href="{{ route(
                'admin.student-registration.guardians'
            ) }}"
            class="font-semibold text-blue-600"
        >
            ✓ Guardian Details
        </a>

        <span class="text-gray-400">→</span>

        <span class="font-semibold text-blue-700">
            3. Class Enrolment
        </span>

    </div>

    @error('classes')
        <div class="mb-5 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    @error('confirmed_ids')
        <div class="mb-5 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    @error('wishlist_ids')
        <div class="mb-5 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    <div class="mb-6 rounded-xl border border-gray-200
                bg-white p-6 shadow-sm">

        <h2 class="text-xl font-bold text-gray-800">
            Student Registration Summary
        </h2>

        <div class="mt-4 grid gap-4 md:grid-cols-3">

            <div>
                <p class="text-xs font-semibold uppercase
                          text-gray-500">
                    Student
                </p>

                <p class="mt-1 font-semibold text-gray-800">
                    {{ $studentData['first_name'] }}
                    {{ $studentData['last_name'] }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase
                          text-gray-500">
                    Student ID
                </p>

                <p class="mt-1 text-gray-800">
                    {{ $studentData['external_id'] }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase
                          text-gray-500">
                    Guardian Count
                </p>

                <p class="mt-1 text-gray-800">
                    {{
                        count(
                            $guardianData['existing'] ?? []
                        )
                        +
                        count(
                            $guardianData['new'] ?? []
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
    >
        @csrf

        <div class="rounded-xl border border-gray-200
                    bg-white shadow-sm">

            <div class="border-b border-gray-200 p-6">

                <h2 class="text-xl font-bold text-gray-800">
                    Select Classes
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Confirmed classes use one seat.
                    Wishlist selections do not use any seats.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-5 py-3 text-left text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Day
                            </th>

                            <th class="px-5 py-3 text-left text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Class
                            </th>

                            <th class="px-5 py-3 text-left text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Time
                            </th>

                            <th class="px-5 py-3 text-center text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Maximum
                            </th>

                            <th class="px-5 py-3 text-center text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Allocated
                            </th>

                            <th class="px-5 py-3 text-center text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Available
                            </th>

                            <th class="px-5 py-3 text-center text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Enrol
                            </th>

                            <th class="px-5 py-3 text-center text-xs
                                       font-semibold uppercase
                                       text-gray-500">
                                Wishlist
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse (
                            $sectionOfferings as $offering
                        )

                            @php
                                $isFull =
                                    $offering->available_seats <= 0;
                            @endphp

                            <tr
                                class="{{ $isFull
                                    ? 'bg-red-50'
                                    : 'hover:bg-gray-50' }}"
                            >

                                <td class="whitespace-nowrap
                                           px-5 py-4 text-sm
                                           font-semibold text-gray-800">

                                    {{ $offering->day->day_name }}

                                </td>

                                <td class="whitespace-nowrap
                                           px-5 py-4 text-sm
                                           text-gray-700">

                                    {{ $offering->section->section_name }}

                                </td>

                                <td class="whitespace-nowrap
                                           px-5 py-4 text-sm
                                           text-gray-700">

                                    {{
                                        \Carbon\Carbon::createFromFormat(
                                            'H:i:s',
                                            $offering->start_time
                                        )->format('g:i A')
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::createFromFormat(
                                            'H:i:s',
                                            $offering->end_time
                                        )->format('g:i A')
                                    }}

                                </td>

                                <td class="px-5 py-4 text-center
                                           text-sm text-gray-700">

                                    {{ $offering->max_seats }}

                                </td>

                                <td class="px-5 py-4 text-center
                                           text-sm text-gray-700">

                                    {{ $offering->allocated_seats }}

                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if ($isFull)

                                        <span class="inline-flex
                                                     rounded-full
                                                     bg-red-100
                                                     px-3 py-1
                                                     text-xs
                                                     font-semibold
                                                     text-red-700">
                                            Full
                                        </span>

                                    @else

                                        <span class="inline-flex
                                                     rounded-full
                                                     bg-green-100
                                                     px-3 py-1
                                                     text-xs
                                                     font-semibold
                                                     text-green-700">

                                            {{ $offering->available_seats }}

                                        </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-center">

                                    <input
                                        type="checkbox"
                                        name="confirmed_ids[]"
                                        value="{{ $offering->id }}"
                                        class="confirmed-checkbox
                                               rounded border-gray-300
                                               text-blue-600"
                                        data-offering="{{ $offering->id }}"
                                        @checked(
                                            in_array(
                                                $offering->id,
                                                $oldConfirmed
                                            )
                                        )
                                        @disabled($isFull)
                                    >

                                    @if ($isFull)
                                        <p class="mt-1 text-xs
                                                  text-red-600">
                                            No seats
                                        </p>
                                    @endif

                                </td>

                                <td class="px-5 py-4 text-center">

                                    <input
                                        type="checkbox"
                                        name="wishlist_ids[]"
                                        value="{{ $offering->id }}"
                                        class="wishlist-checkbox
                                               rounded border-gray-300
                                               text-purple-600"
                                        data-offering="{{ $offering->id }}"
                                        @checked(
                                            in_array(
                                                $offering->id,
                                                $oldWishlist
                                            )
                                        )
                                    >

                                    @if ($offering->wishlist_count > 0)
                                        <p class="mt-1 text-xs
                                                  text-gray-500">
                                            {{
                                                $offering->wishlist_count
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
                                    class="px-6 py-12 text-center
                                           text-sm text-gray-500"
                                >
                                    No active classes are available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="flex flex-col gap-3 border-t
                        border-gray-200 bg-gray-50 p-6
                        sm:flex-row sm:justify-end">

                <a
                    href="{{ route(
                        'admin.student-registration.guardians'
                    ) }}"
                    class="rounded-lg border border-gray-300
                           bg-white px-4 py-2.5 text-center
                           text-sm font-semibold text-gray-700
                           hover:bg-gray-100"
                >
                    ← Back to Guardians
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600
                           px-5 py-2.5 text-sm
                           font-semibold text-white
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
document.addEventListener('DOMContentLoaded', function () {
    const confirmedCheckboxes =
        document.querySelectorAll(
            '.confirmed-checkbox'
        );

    const wishlistCheckboxes =
        document.querySelectorAll(
            '.wishlist-checkbox'
        );

    confirmedCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener(
            'change',
            function () {
                if (!checkbox.checked) {
                    return;
                }

                const offeringId =
                    checkbox.dataset.offering;

                const wishlistCheckbox =
                    document.querySelector(
                        '.wishlist-checkbox' +
                        '[data-offering="' +
                        offeringId +
                        '"]'
                    );

                if (wishlistCheckbox) {
                    wishlistCheckbox.checked = false;
                }
            }
        );
    });

    wishlistCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener(
            'change',
            function () {
                if (!checkbox.checked) {
                    return;
                }

                const offeringId =
                    checkbox.dataset.offering;

                const confirmedCheckbox =
                    document.querySelector(
                        '.confirmed-checkbox' +
                        '[data-offering="' +
                        offeringId +
                        '"]'
                    );

                if (confirmedCheckbox) {
                    confirmedCheckbox.checked = false;
                }
            }
        );
    });
});
</script>

@endpush
