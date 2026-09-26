@extends('layouts.admin')

@section('title', 'Student Import Preview')

@section('page-title', 'Student Import Preview')

@section('content')

<div class="space-y-6 pb-28">

    {{-- Header --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

        <div>

            <h1 class="text-3xl font-bold text-slate-900">

                Student Import Preview

            </h1>

            <p class="mt-2 text-sm text-slate-500">

                Existing students and allocations are detected automatically.

                Only new or changed data will be imported.

            </p>

        </div>

        <a

            href="{{ route('admin.student-import.index') }}"

            class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50"

        >

            ← Back

        </a>

    </div>

    {{-- Messages --}}

    @if (session('success'))

        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold text-green-700">

            {{ session('success') }}

        </div>

    @endif

    @if (session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">

            {{ session('error') }}

        </div>

    @endif

    @if ($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">

            <p class="text-sm font-semibold text-red-700">

                Please check the following:

            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- Summary --}}

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">

        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 p-5">

            <p class="text-sm font-semibold text-slate-600">

                Timeslot Students

            </p>

            <p class="mt-2 text-3xl font-bold text-cyan-700">

                {{ $currentStudentCount }}

            </p>

            <p class="mt-1 text-xs text-slate-500">

                Recognised students in the workbook

            </p>

        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

            <p class="text-sm font-semibold text-slate-600">

                Already In System

            </p>

            <p class="mt-2 text-3xl font-bold text-blue-700">

                {{ $existingStudents->count() }}

            </p>

            <p class="mt-1 text-xs text-slate-500">

                Existing students

            </p>

        </div>

        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">

            <p class="text-sm font-semibold text-slate-600">

                New Students

            </p>

            <p class="mt-2 text-3xl font-bold text-violet-700">

                {{ $studentsNotAdded->count() }}

            </p>

            <p class="mt-1 text-xs text-slate-500">

                Students not added yet

            </p>

        </div>

        <div class="rounded-2xl border border-green-200 bg-green-50 p-5">

            <p class="text-sm font-semibold text-slate-600">

                To Import

            </p>

            <p class="mt-2 text-3xl font-bold text-green-700">

                {{ $readyRows->count() }}

            </p>

            <p class="mt-1 text-xs text-slate-500">

                New/reactivated allocations

            </p>

        </div>

        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

            <p class="text-sm font-semibold text-slate-600">

                Problems

            </p>

            <p class="mt-2 text-3xl font-bold text-red-700">

                {{ $problemRows->count() }}

            </p>

            <p class="mt-1 text-xs text-slate-500">

                Rows requiring review

            </p>

        </div>

    </div>

    {{-- Nothing New --}}

    @if (

        $readyRows->isEmpty()

        && $problemRows->isEmpty()

    )

        <div class="rounded-2xl border border-blue-200 bg-blue-50 px-6 py-5">

            <p class="font-bold text-blue-800">

                This data has already been imported.

            </p>

            <p class="mt-1 text-sm text-blue-700">

                No new students or class allocations were found.

                Nothing needs to be imported again.

            </p>

        </div>

    @endif

    {{-- New Students --}}

    @if ($studentsNotAdded->isNotEmpty())

        <section class="overflow-hidden rounded-2xl border border-violet-200 bg-white shadow-sm">

            <div class="border-b border-violet-100 bg-violet-50 px-6 py-4">

                <h2 class="font-bold text-violet-800">

                    New Students Not Added Yet

                </h2>

                <p class="mt-1 text-sm text-violet-600">

                    These students are in the timeslot workbook but do not currently exist in the students table.

                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student ID

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                DOB

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Status

                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($studentsNotAdded as $row)

                            <tr>

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    {{ $row['profile']['external_id'] ?? '—' }}

                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-800">

                                    {{ $row['profile']['first_name'] ?? '' }}

                                    {{ $row['profile']['last_name'] ?? '' }}

                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    @if (!empty($row['profile']['date_of_birth']))

                                        {{

                                            \Carbon\Carbon::parse(

                                                $row['profile']['date_of_birth']

                                            )->format('d M Y')

                                        }}

                                    @else

                                        —

                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    <span class="inline-flex rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">

                                        New Student

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

    @endif

    {{-- Existing Students / Already Imported --}}

    @if ($existingStudents->isNotEmpty())

        <section class="overflow-hidden rounded-2xl border border-blue-200 bg-white shadow-sm">

            <div class="border-b border-blue-100 bg-blue-50 px-6 py-4">

                <h2 class="font-bold text-blue-800">

                    Students Already In The System

                </h2>

                <p class="mt-1 text-sm text-blue-600">

                    These students will not be created again.

                    Existing allocations are also skipped.

                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student ID

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Import Result

                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($existingStudents as $row)

                            @php

                                $state = $row['import_state'] ?? '';

                            @endphp

                            <tr>

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    {{ $row['profile']['external_id'] ?? '—' }}

                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-800">

                                    {{ $row['profile']['first_name'] ?? '' }}

                                    {{ $row['profile']['last_name'] ?? '' }}

                                </td>

                                <td class="px-5 py-4">

                                    @if ($state === 'already_imported')

                                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">

                                            Already Imported

                                        </span>

                                    @elseif ($state === 'reactivate_allocation')

                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">

                                            Allocation Will Be Reactivated

                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">

                                            New Allocation

                                        </span>

                                    @endif

                                    <p class="mt-1 text-xs text-slate-500">

                                        {{ $row['message'] ?? '' }}

                                    </p>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

    @endif

    {{-- Problems --}}

    @if ($problemRows->isNotEmpty())

        <section class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm">

            <div class="border-b border-red-100 bg-red-50 px-6 py-4">

                <h2 class="font-bold text-red-800">

                    Rows Requiring Attention

                </h2>

                <p class="mt-1 text-sm text-red-600">

                    Resolve unmatched students or invalid class rows before importing.

                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Class

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Day / Time

                            </th>

                            <th class="min-w-[360px] px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Problem / Action

                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($problemRows as $row)

                            @php

                                $status = $row['status'] ?? '';

                            @endphp

                            <tr class="align-top hover:bg-slate-50/70">

                                <td class="px-5 py-4">

                                    <p class="font-semibold text-slate-800">

                                        {{ $row['student_name'] ?? 'Unknown Student' }}

                                    </p>

                                    @if (!empty($row['raw_student']))

                                        <p class="mt-1 text-xs text-slate-400">

                                            Source:

                                            {{ $row['raw_student'] }}

                                        </p>

                                    @endif

                                    @if (!empty($row['sheet']))

                                        <p class="mt-1 text-xs text-slate-400">

                                            Sheet:

                                            {{ $row['sheet'] }}

                                            @if (!empty($row['row_number']))

                                                · Row:

                                                {{ $row['row_number'] }}

                                            @endif

                                        </p>

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    {{ $row['section_name'] ?? '—' }}

                                    @if (!empty($row['sub_section_name']))

                                        -

                                        {{ $row['sub_section_name'] }}

                                    @endif

                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">

                                    <p>{{ $row['day'] ?? '—' }}</p>

                                    @if (!empty($row['time']))

                                        <p class="mt-1 font-semibold">

                                            {{

                                                \Carbon\Carbon::parse(

                                                    $row['time']

                                                )->format('g:i A')

                                            }}

                                        </p>

                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    @if ($status === 'student_not_found')

                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">

                                            Student Not Found

                                        </span>

                                        <p class="mt-2 text-sm font-medium text-red-700">

                                            {{

                                                $row['message']

                                                ?? 'Student not found.'

                                            }}

                                        </p>

                                        @include(

                                            'admin.students.import.alias-form',

                                            [

                                                'row' => $row,
                                                'matchIndex' => $loop->index,

                                            ]

                                        )

                                    @elseif ($status === 'duplicate')

                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">

                                            Duplicate In Workbook

                                        </span>

                                        <p class="mt-2 text-sm text-amber-700">

                                            {{

                                                $row['message']

                                                ?? 'Duplicate allocation in uploaded workbook.'

                                            }}

                                        </p>

                                    @elseif ($status === 'class_not_found')

                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">

                                            Class Not Found

                                        </span>

                                        <p class="mt-2 text-sm text-red-700">

                                            {{

                                                $row['message']

                                                ?? 'Class offering could not be matched.'

                                            }}

                                        </p>

                                    @elseif ($status === 'student_ambiguous')

                                        <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">

                                            Multiple Matches

                                        </span>

                                        <p class="mt-2 text-sm text-orange-700">

                                            {{

                                                $row['message']

                                                ?? 'More than one profile matches this student.'

                                            }}

                                        </p>

                                        @include(
                                            'admin.students.import.alias-form',
                                            ['row' => $row, 'matchIndex' => $loop->index]
                                        )

                                    @else

                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">

                                            Problem

                                        </span>

                                        <p class="mt-2 text-sm text-red-700">

                                            {{

                                                $row['message']

                                                ?? 'This row cannot be imported.'

                                            }}

                                        </p>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

    @endif

    {{-- Rows That Will Actually Be Imported --}}

    @if ($readyRows->isNotEmpty())

        <section class="overflow-hidden rounded-2xl border border-green-200 bg-white shadow-sm">

            <div class="border-b border-green-100 bg-green-50 px-6 py-4">

                <h2 class="font-bold text-green-800">

                    Data To Import

                </h2>

                <p class="mt-1 text-sm text-green-600">

                    Only these rows will be processed when you click Confirm Import.

                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student ID

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Student

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Class

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Day / Time

                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">

                                Action

                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($readyRows as $row)

                            @php

                                $state = $row['import_state'] ?? '';

                            @endphp

                            <tr class="hover:bg-green-50/30">

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    {{ $row['profile']['external_id'] ?? '—' }}

                                </td>

                                <td class="px-5 py-4">

                                    <p class="font-semibold text-slate-800">

                                        {{ $row['profile']['first_name'] ?? '' }}

                                        {{ $row['profile']['last_name'] ?? '' }}

                                    </p>

                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    {{ $row['section_name'] ?? '—' }}

                                    @if (!empty($row['sub_section_name']))

                                        -

                                        {{ $row['sub_section_name'] }}

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">

                                    {{ $row['day'] ?? '—' }}

                                    @if (!empty($row['time']))

                                        ·

                                        {{

                                            \Carbon\Carbon::parse(

                                                $row['time']

                                            )->format('g:i A')

                                        }}

                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    @if ($state === 'new_student')

                                        <span class="inline-flex rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">

                                            Add Student + Allocation

                                        </span>

                                    @elseif ($state === 'reactivate_allocation')

                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">

                                            Reactivate Allocation

                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">

                                            Add Allocation Only

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

    @endif

    {{-- Safety Information --}}

    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

        <p class="font-semibold text-blue-800">

            Import safety

        </p>

        <p class="mt-2 text-sm leading-6 text-blue-700">

            Students are matched by Student ID.

            Existing students are not created again.

            Existing active allocations are skipped.

            Only new allocations, new students, or inactive allocations that need reactivation are processed.

        </p>

    </div>

    {{-- One floating action for the full preview; controls above belong to this form. --}}
    @php
        $matchableCount = $problemRows->whereIn('status', [
            'student_not_found', 'student_ambiguous',
        ])->count();
    @endphp
    <form id="import-confirm-form"
          method="POST"
          action="{{ route('admin.student-import.confirm') }}"
          data-ready-count="{{ $readyRows->count() }}"
          class="fixed bottom-4 right-4 z-50 flex max-w-[calc(100vw-2rem)] flex-wrap items-center justify-end gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
        @csrf
        <span class="text-sm text-slate-600">
            {{ $readyRows->count() }} ready ·
            <span id="selected-match-count">0</span> name match(es) selected
        </span>
        <a href="{{ route('admin.student-import.index') }}"
           class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Cancel</a>
        @if ($readyRows->isNotEmpty() || $matchableCount > 0)
            <button id="confirm-import-button" type="submit"
                    class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                Confirm Import
            </button>
        @else
            <button type="button" disabled class="rounded-lg bg-slate-300 px-5 py-2.5 text-sm font-bold text-white">
                Nothing New To Import
            </button>
        @endif
    </form>

    <script>
        (function () {
            const form = document.getElementById('import-confirm-form');
            const count = document.getElementById('selected-match-count');
            const button = document.getElementById('confirm-import-button');
            const rows = document.querySelectorAll('[data-match-row]');

            function updateSelection() {
                let selected = 0;
                rows.forEach(function (row) {
                    const check = row.querySelector('[data-match-check]');
                    const select = row.querySelector('[data-match-select]');
                    select.disabled = !check.checked;
                    select.required = check.checked;
                    if (check.checked) selected++;
                });
                count.textContent = selected;
                if (button) button.disabled = selected === 0 && Number(form.dataset.readyCount) === 0;
            }

            rows.forEach(function (row) {
                const check = row.querySelector('[data-match-check]');
                check.addEventListener('change', updateSelection);
            });

            updateSelection();
            form.addEventListener('submit', function (event) {
                if (!window.confirm('Import ready rows and save the selected student name matches?')) {
                    event.preventDefault();
                } else if (button) {
                    button.disabled = true;
                }
            });
        })();
    </script>

</div>

@endsection
