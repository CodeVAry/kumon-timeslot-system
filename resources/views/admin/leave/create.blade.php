@extends('layouts.admin')

@section('title', 'Add New Leave')

@section('page-title', 'Add New Leave')


@php

    $breadcrumbs = [
        [
            'label' => 'Leave Management',
            'url' => route('admin.leave.index'),
        ],
        [
            'label' => 'Add New Leave',
            'url' => null,
        ],
    ];

@endphp


@section('content')

    <div class="space-y-6">


        {{-- Back --}}
        <div>

            <a href="{{ route('admin.leave.index') }}"
                class="inline-flex items-center gap-2
                   text-sm font-semibold
                   text-blue-600
                   hover:text-blue-800">
                ← Back to Leave Management
            </a>

        </div>



        {{-- Header --}}
        <div>

            <h1 class="text-3xl font-bold
                   text-slate-900">
                Add New Leave
            </h1>

            <p class="mt-2 text-sm
                   text-slate-500">
                Record a planned leave period
                for a student.
            </p>

        </div>



        {{-- Errors --}}
        @if ($errors->any())

            <div
                class="rounded-xl
                   border border-red-200
                   bg-red-50
                   px-5 py-4">

                <p class="font-semibold
                       text-red-700">
                    Please correct the following:
                </p>

                <ul
                    class="mt-2 list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>

            </div>

        @endif



        {{-- =========================================================
    STUDENT SEARCH
========================================================== --}}

        <section
            class="rounded-2xl
           border border-slate-200
           bg-white
           p-6
           shadow-sm">

            <form method="GET" action="{{ route('admin.leave.create') }}">

                <label for="search"
                    class="mb-2 block
                   text-sm
                   font-semibold
                   text-slate-700">
                    Search Student
                </label>


                <div class="flex flex-col
                   gap-3
                   md:flex-row">

                    <div class="relative flex-1">

                        <input type="text" name="search" id="search" value="{{ $search }}"
                            placeholder="Enter student name or Student ID..." autocomplete="off"
                            class="h-11 w-full
                           rounded-xl
                           border-slate-300
                           pl-11
                           pr-4
                           text-sm
                           focus:border-blue-500
                           focus:ring-blue-500">


                        {{-- Search Icon --}}
                        <div
                            class="pointer-events-none
                           absolute
                           inset-y-0 left-0
                           flex items-center
                           pl-4
                           text-slate-400">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" class="h-5 w-5">
                                <circle cx="11" cy="11" r="8" />

                                <path d="m21 21-4.3-4.3" />
                            </svg>

                        </div>

                    </div>


                    <button type="submit"
                        class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-600
                       px-7
                       text-sm
                       font-semibold
                       text-white
                       transition
                       hover:bg-blue-700">
                        Search
                    </button>

                </div>


                <p class="mt-2
                   text-xs
                   text-slate-400">
                    Search by student name or manually entered Student ID.
                </p>

            </form>



            {{-- =====================================================
        SEARCH RESULTS
    ====================================================== --}}

            @if ($search !== '' && !$selectedStudent)

                <div
                    class="mt-5
                   overflow-hidden
                   rounded-xl
                   border
                   border-slate-200">

                    @forelse ($students as $student)
                        <div
                            class="flex flex-col
                           gap-3
                           border-b
                           border-slate-100
                           px-5 py-4
                           last:border-0
                           sm:flex-row
                           sm:items-center
                           sm:justify-between
                           hover:bg-blue-50/40">

                            <div>

                                <p class="font-semibold
                                   text-slate-900">
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                </p>


                                <p
                                    class="mt-1
                                   text-xs
                                   text-slate-500">
                                    Student ID:
                                    {{ $student->external_id ?? '—' }}
                                </p>

                            </div>


                            <a href="{{ route('admin.leave.create', [
                                'student_id' => $student->id,

                                'search' => $search,
                            ]) }}"
                                class="inline-flex
                               h-10
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-blue-300
                               bg-white
                               px-5
                               text-sm
                               font-semibold
                               text-blue-700
                               transition
                               hover:bg-blue-50">
                                Select Student
                            </a>

                        </div>


                    @empty

                        <div class="px-6 py-10
                           text-center">

                            <p class="font-semibold
                               text-slate-700">
                                No students found
                            </p>


                            <p
                                class="mt-1
                               text-sm
                               text-slate-500">
                                Try another name or Student ID.
                            </p>

                        </div>
                    @endforelse

                </div>

            @endif

        </section>



        @if ($selectedStudent)

            <div class="grid gap-6
                   xl:grid-cols-[0.8fr_1.2fr]">


                {{-- Student Information --}}
                <section
                    class="rounded-2xl
                       border border-slate-200
                       bg-white
                       p-6
                       shadow-sm">

                    <p
                        class="text-xs font-semibold
                           uppercase tracking-wide
                           text-blue-600">
                        Student Information
                    </p>


                    <h2
                        class="mt-2 text-2xl
                           font-bold
                           text-slate-900">
                        {{ $selectedStudent->first_name }}
                        {{ $selectedStudent->last_name }}
                    </h2>


                    <p class="mt-1 text-sm
                           text-slate-500">
                        ID:
                        {{ $selectedStudent->external_id ?? '—' }}
                    </p>


                    <div
                        class="mt-5
                           border-t
                           border-slate-100
                           pt-5">

                        <p class="text-sm font-semibold
                               text-slate-700">
                            Active Enrolled Classes
                        </p>


                        <div class="mt-3 space-y-3">

                            @forelse ($selectedStudent->enrolments as $enrolment)
                                @php

                                    $offering = $enrolment->sectionOffering;

                                @endphp

                                <div
                                    class="rounded-xl
                                       border border-slate-200
                                       bg-slate-50
                                       px-4 py-3">

                                    <p class="font-semibold
                                           text-slate-800">
                                        {{ $offering?->section?->section_name ?? 'Class' }}
                                    </p>


                                    <p
                                        class="mt-1
                                           text-xs
                                           text-slate-500">
                                        {{ $offering?->day?->day_name ?? '—' }}

                                        @if ($offering)
                                            ·

                                            {{ \Carbon\Carbon::parse($offering->start_time)->format('g:i A') }}

                                            –

                                            {{ \Carbon\Carbon::parse($offering->end_time)->format('g:i A') }}
                                        @endif
                                    </p>

                                </div>

                            @empty

                                <p class="text-sm
                                       text-slate-500">
                                    No active confirmed classes.
                                </p>
                            @endforelse

                        </div>


                        <div
                            class="mt-5
                               rounded-xl
                               border border-blue-100
                               bg-blue-50
                               p-4">
                            <p class="text-sm
                                   text-blue-800">
                                This leave will automatically apply
                                to all active enrolled classes
                                during the selected leave period.
                            </p>
                        </div>

                    </div>

                </section>



                {{-- Leave Form --}}
                <section
                    class="rounded-2xl
                       border border-slate-200
                       bg-white
                       p-6
                       shadow-sm">

                    <form method="POST" action="{{ route('admin.leave.store') }}">

                        @csrf


                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">


                        <div class="grid gap-5
                               md:grid-cols-2">


                            {{-- Start Date --}}
                            <div>

                                <label for="start_date"
                                    class="mb-2 block
                                       text-sm font-semibold
                                       text-slate-700">
                                    Start Date
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                    required
                                    class="w-full
                                       rounded-xl
                                       border-slate-300">

                            </div>



                            {{-- Expected Return --}}
                            <div>

                                <label for="expected_return_date"
                                    class="mb-2 block
                                       text-sm font-semibold
                                       text-slate-700">
                                    Expected Return Date
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="date" name="expected_return_date" id="expected_return_date"
                                    value="{{ old('expected_return_date') }}" required
                                    class="w-full
                                       rounded-xl
                                       border-slate-300">

                            </div>



                            {{-- Homework --}}
                            <div>

                                <label for="homework_requirement"
                                    class="mb-2 block
                                       text-sm font-semibold
                                       text-slate-700">
                                    Homework Required
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="homework_requirement" id="homework_requirement" required
                                    class="w-full
                                       rounded-xl
                                       border-slate-300">

                                    <option value="">
                                        Select homework requirement
                                    </option>

                                    <option value="none_required" @selected(old('homework_requirement') === 'none_required')>
                                        None required
                                    </option>

                                    <option value="same_as_normal" @selected(old('homework_requirement') === 'same_as_normal')>
                                        Same as normal
                                    </option>

                                    <option value="increase" @selected(old('homework_requirement') === 'increase')>
                                        Increase
                                    </option>

                                    <option value="decrease" @selected(old('homework_requirement') === 'decrease')>
                                        Decrease
                                    </option>

                                </select>

                            </div>



                            {{-- Reason --}}
                            <div>

                                <label for="reason"
                                    class="mb-2 block
                                       text-sm font-semibold
                                       text-slate-700">
                                    Reason
                                </label>

                                <input type="text" name="reason" id="reason" value="{{ old('reason') }}"
                                    maxlength="255" placeholder="Example: Family holiday"
                                    class="w-full
                                       rounded-xl
                                       border-slate-300">

                            </div>

                        </div>



                        {{-- Notes --}}
                        <div class="mt-5">

                            <label for="notes"
                                class="mb-2 block
                                   text-sm font-semibold
                                   text-slate-700">
                                Notes
                            </label>

                            <textarea name="notes" id="notes" rows="5" maxlength="2000" placeholder="Optional notes..."
                                class="w-full
                                   rounded-xl
                                   border-slate-300">{{ old('notes') }}</textarea>

                        </div>



                        <div
                            class="mt-6 flex
                               justify-end
                               gap-3">

                            <a href="{{ route('admin.leave.index') }}"
                                class="inline-flex h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-slate-300
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-slate-600">
                                Cancel
                            </a>


                            <button type="submit"
                                class="inline-flex h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-600
                                   px-7
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-blue-700">
                                Save Leave
                            </button>

                        </div>

                    </form>

                </section>

            </div>
        @else
            <div
                class="rounded-2xl
                   border border-dashed
                   border-slate-300
                   bg-white
                   p-12
                   text-center">

                <p class="font-semibold
                       text-slate-700">
                    Select a student first
                </p>

                <p class="mt-1
                       text-sm
                       text-slate-500">
                    The student’s active enrolled classes
                    will appear here.
                </p>

            </div>

        @endif

    </div>

@endsection
