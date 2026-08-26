@extends('layouts.admin')

@section('title', 'Students')

@section('page-title', 'Students')

@php
    $breadcrumbs = [
        [
            'label' => 'Student Setup',
            'url' => null,
        ],
        [
            'label' => 'Student List',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    <div class="space-y-6">

        {{-- =====================================================
            SUCCESS MESSAGE
        ====================================================== --}}

        @if (session('success'))
            <div
                class="rounded-xl
                       border
                       border-green-200
                       bg-green-50
                       px-5 py-4
                       text-sm
                       text-green-700">
                {{ session('success') }}
            </div>
        @endif


        {{-- =====================================================
            ERROR MESSAGE
        ====================================================== --}}

        @if (session('error'))
            <div
                class="rounded-xl
                       border
                       border-red-200
                       bg-red-50
                       px-5 py-4
                       text-sm
                       text-red-700">
                {{ session('error') }}
            </div>
        @endif



        {{-- =====================================================
            PAGE HEADING
        ====================================================== --}}

        <div
            class="flex
                   flex-col
                   gap-5
                   lg:flex-row
                   lg:items-center
                   lg:justify-between">

            <div>

                <h1
                    class="text-3xl
                           font-bold
                           tracking-tight
                           text-cyan-950">
                    Students
                </h1>


                <p class="mt-1
                           text-sm
                           text-slate-500">
                    Search, filter and manage student records
                    and linked guardians.
                </p>

            </div>


            <div
                class="flex
                       flex-wrap
                       items-center
                       gap-3">

                {{-- Guardian list button --}}

                @if (Route::has('admin.guardians.index'))
                    <a href="{{ route('admin.guardians.index') }}"
                        class="inline-flex
                               h-11
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-cyan-200
                               bg-white
                               px-6
                               text-sm
                               font-semibold
                               text-cyan-800
                               shadow-sm
                               hover:bg-cyan-50">
                        Guardians
                    </a>
                @endif


                {{-- Add student button --}}

                @if (auth()->user()->hasPermission('students.create'))
                    <a href="{{ route('admin.student-registration.student') }}"
                        class="inline-flex
                               h-11
                               items-center
                               justify-center
                               gap-2
                               rounded-xl
                               bg-cyan-500
                               px-6
                               text-sm
                               font-semibold
                               text-white
                               shadow-sm
                               hover:bg-cyan-600">

                        <span class="text-lg
                                   leading-none">
                            +
                        </span>

                        Add Student

                    </a>
                @endif

            </div>

        </div>



        {{-- =====================================================
            FILTERS
        ====================================================== --}}

        <div
            class="rounded-2xl
                   border
                   border-cyan-200
                   bg-white
                   p-5
                   shadow-sm">

            <form method="GET" action="{{ route('admin.students.index') }}">

                <div
                    class="grid
                           gap-5
                           md:grid-cols-2
                           xl:grid-cols-3">


                    {{-- Search --}}

                    <div>

                        <label for="search"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600">
                            Search by student name
                        </label>


                        <input id="search" type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by student name or ID"
                            class="h-12
                                   w-full
                                   rounded-xl
                                   border-slate-300
                                   text-sm
                                   shadow-sm
                                   focus:border-cyan-500
                                   focus:ring-cyan-500">

                    </div>



                    {{-- Student status --}}

                    <div>

                        <label for="student_status_id"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600">
                            Status
                        </label>


                        <select id="student_status_id" name="student_status_id"
                            class="h-12
                                   w-full
                                   rounded-xl
                                   border-slate-300
                                   text-sm
                                   shadow-sm
                                   focus:border-cyan-500
                                   focus:ring-cyan-500">

                            <option value="">
                                Any status
                            </option>


                            @foreach ($studentStatuses as $studentStatus)
                                <option value="{{ $studentStatus->id }}" @selected(request('student_status_id') == $studentStatus->id)>
                                    {{ $studentStatus->status_name }}
                                </option>
                            @endforeach

                        </select>

                    </div>



                    {{-- Guardian --}}

                    <div>

                        <label for="guardian"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600">
                            Guardian
                        </label>


                        <input id="guardian" type="text" name="guardian" value="{{ request('guardian') }}"
                            placeholder="Search guardian name, phone or email"
                            class="h-12
                                   w-full
                                   rounded-xl
                                   border-slate-300
                                   text-sm
                                   shadow-sm
                                   focus:border-cyan-500
                                   focus:ring-cyan-500">

                    </div>



                    {{-- Day --}}

                    <div>

                        <label for="day_id"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600">
                            Day
                        </label>


                        <select id="day_id" name="day_id"
                            class="h-12
                                   w-full
                                   rounded-xl
                                   border-slate-300
                                   text-sm
                                   shadow-sm
                                   focus:border-cyan-500
                                   focus:ring-cyan-500">

                            <option value="">
                                Any day
                            </option>


                            @foreach ($days as $day)
                                <option value="{{ $day->id }}" @selected(request('day_id') == $day->id)>
                                    {{ $day->day_name }}
                                </option>
                            @endforeach

                        </select>

                    </div>



                    {{-- Class time --}}

                    <div>

                        <label for="timeslot"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600">
                            Class Time
                        </label>


                        <select id="timeslot" name="timeslot"
                            class="h-12
                                   w-full
                                   rounded-xl
                                   border-slate-300
                                   text-sm
                                   shadow-sm
                                   focus:border-cyan-500
                                   focus:ring-cyan-500">

                            <option value="">
                                Any time
                            </option>


                            @foreach ($timeslots as $timeslot)
                                <option value="{{ $timeslot }}" @selected(request('timeslot') == $timeslot)>
                                    {{ \Carbon\Carbon::parse($timeslot)->format('g:i A') }}
                                </option>
                            @endforeach

                        </select>

                    </div>



                    {{-- Section --}}

                    <div>

                        <label for="section_id"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600">
                            Section
                        </label>


                        <select id="section_id" name="section_id"
                            class="h-12
                                   w-full
                                   rounded-xl
                                   border-slate-300
                                   text-sm
                                   shadow-sm
                                   focus:border-cyan-500
                                   focus:ring-cyan-500">

                            <option value="">
                                Any Section
                            </option>


                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>
                                    {{ $section->section_name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                </div>



                {{-- Filter buttons --}}

                <div
                    class="mt-5
                           flex
                           flex-col
                           gap-3
                           sm:flex-row
                           sm:justify-end">

                    <a href="{{ route('admin.students.index') }}"
                        class="inline-flex
                               h-11
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-cyan-200
                               bg-white
                               px-8
                               text-sm
                               font-semibold
                               text-cyan-800
                               hover:bg-cyan-50">
                        Clear
                    </a>


                    <button type="submit"
                        class="inline-flex
                               h-11
                               items-center
                               justify-center
                               rounded-xl
                               bg-cyan-500
                               px-8
                               text-sm
                               font-semibold
                               text-white
                               hover:bg-cyan-600">
                        Apply Filters
                    </button>

                </div>

            </form>

        </div>



        {{-- =====================================================
            RESULT INFORMATION
        ====================================================== --}}

        <div
            class="flex
                   flex-col
                   gap-2
                   sm:flex-row
                   sm:items-center
                   sm:justify-between">

            <p class="text-sm
                       text-slate-500">

                {{ $students->total() }}

                {{ $students->total() === 1 ? 'result' : 'results' }}

            </p>


            @if ($students->total() > 0)
                <p class="text-sm
                           text-slate-400">

                    Showing

                    {{ $students->firstItem() }}

                    to

                    {{ $students->lastItem() }}

                    of

                    {{ $students->total() }}

                </p>
            @endif

        </div>



        {{-- =====================================================
            STUDENT TABLE
        ====================================================== --}}

        <div
            class="overflow-hidden
                   rounded-2xl
                   border
                   border-cyan-200
                   bg-white
                   shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead
                        class="border-b
                               border-cyan-200
                               bg-cyan-50/60">

                        <tr>

                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Student
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Guardian
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Student ID
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                DOB
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Status
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Subject
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Day & Time
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Notes
                            </th>


                            <th
                                class="px-5 py-4
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-slate-500">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y
                               divide-cyan-100">

                        @forelse (
                                $students
                                as $student
                            )

                            @php

                                $primaryGuardian = $student->guardians->first(function ($guardian) {
                                    return (bool) $guardian->pivot->is_primary;
                                });

                                if (!$primaryGuardian) {
                                    $primaryGuardian = $student->guardians->first();
                                }

                                $confirmedEnrolments = $student->enrolments->filter(function ($enrolment) {
                                    return $enrolment->is_active && !$enrolment->is_wishlist;
                                });

                                $wishlistEnrolments = $student->enrolments->filter(function ($enrolment) {
                                    return $enrolment->is_active && $enrolment->is_wishlist;
                                });

                                $classNames = $confirmedEnrolments
                                    ->map(function ($enrolment) {
                                        return $enrolment->sectionOffering?->section?->section_name;
                                    })
                                    ->filter()
                                    ->unique()
                                    ->implode(', ');

                            @endphp


                            <tr class="transition
                                       hover:bg-cyan-50/50">


                                {{-- Student --}}

                                <td class="whitespace-nowrap
                                           px-5 py-5">

                                    <p class="font-semibold
                                               text-slate-800">
                                        {{ $student->first_name }}
                                        {{ $student->last_name }}
                                    </p>


                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-400">
                                        Record ID:
                                        {{ $student->id }}
                                    </p>

                                </td>



                                {{-- Guardian --}}

                                <td
                                    class="px-5 py-5
                                           text-sm
                                           text-slate-700">

                                    @if ($primaryGuardian)
                                        <p>
                                            {{ $primaryGuardian->first_name }}
                                            {{ $primaryGuardian->last_name }}
                                        </p>


                                        @if ($student->guardians->count() > 1)
                                            <p
                                                class="mt-1
                                                       text-xs
                                                       text-slate-400">
                                                +{{ $student->guardians->count() - 1 }}
                                                more
                                            </p>
                                        @endif
                                    @else
                                        <span class="text-slate-400">
                                            —
                                        </span>
                                    @endif

                                </td>



                                {{-- External student ID --}}

                                <td
                                    class="whitespace-nowrap
                                           px-5 py-5
                                           text-sm
                                           text-slate-700">
                                    {{ $student->external_id }}
                                </td>



                                {{-- Date of birth --}}

                                <td
                                    class="whitespace-nowrap
                                           px-5 py-5
                                           text-sm
                                           text-slate-700">

                                    {{ $student->date_of_birth?->format('d M Y') ?? '—' }}

                                </td>



                                {{-- Status --}}

                                <td class="whitespace-nowrap
                                           px-5 py-5">

                                    @if ($student->studentStatus)
                                        @php

                                            $statusColour = $student->studentStatus->color_code ?? '#64748b';

                                        @endphp


                                        <span
                                            class="inline-flex
                                                   items-center
                                                   gap-2
                                                   text-xs
                                                   font-semibold
                                                   uppercase"
                                            style="color: {{ $statusColour }};">

                                            <span
                                                class="h-2.5
                                                       w-2.5
                                                       rounded-full"
                                                style="
                                                    background-color:
                                                    {{ $statusColour }};
                                                "></span>


                                            {{ $student->studentStatus->status_name }}

                                        </span>
                                    @else
                                        <span
                                            class="text-sm
                                                   text-slate-400">
                                            —
                                        </span>
                                    @endif

                                </td>



                                {{-- Subject --}}

                                <td
                                    class="px-5 py-5
                                           text-sm
                                           text-slate-700">

                                    @if ($classNames)
                                        <span
                                            class="font-medium
                                                   text-slate-700">
                                            {{ $classNames }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">
                                            —
                                        </span>
                                    @endif

                                </td>



                                {{-- Day and time --}}

                                <td
                                    class="px-5 py-5
                                           text-sm
                                           text-slate-700">

                                    @forelse ($confirmedEnrolments
                                            as $enrolment)
                                        @php

                                            $offering = $enrolment->sectionOffering;

                                        @endphp


                                        @if ($offering)
                                            <div
                                                class="mb-2
                                                       whitespace-nowrap">

                                                {{ $offering->day?->day_name ?? '' }}

                                                ·

                                                {{ \Carbon\Carbon::parse($offering->start_time)->format('g:i A') }}

                                            </div>
                                        @endif


                                    @empty

                                        <span class="text-slate-400">
                                            —
                                        </span>
                                    @endforelse



                                    @if ($wishlistEnrolments->count() > 0)
                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-purple-100
                                                   px-2.5 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-purple-700">

                                            {{ $wishlistEnrolments->count() }}

                                            {{ $wishlistEnrolments->count() === 1 ? 'wishlist' : 'wishlists' }}

                                        </span>
                                    @endif

                                </td>



                                {{-- Notes --}}

                                <td
                                    class="max-w-xs
                                           px-5 py-5
                                           text-sm
                                           text-slate-600">

                                    @if ($student->notes)
                                        <p class="line-clamp-2">
                                            {{ $student->notes }}
                                        </p>
                                    @endif


                                    <span>
                                        Can leave:
                                        {{ $student->can_leave_alone ? 'Yes' : 'No' }}
                                    </span>

                                </td>



                                {{-- =====================================================
                                    ACTIONS
                                ====================================================== --}}

                                <td class="px-5 py-5">

                                    <div
                                        class="flex
                                               items-center
                                               justify-center
                                               gap-2">


                                        {{-- View --}}

                                        <a href="{{ route('admin.students.show', $student) }}"
                                            class="inline-flex
                                                   h-10
                                                   items-center
                                                   justify-center
                                                   rounded-xl
                                                   border
                                                   border-cyan-300
                                                   bg-cyan-50
                                                   px-5
                                                   text-sm
                                                   font-semibold
                                                   text-cyan-800
                                                   hover:bg-cyan-100">
                                            View
                                        </a>



                                        {{-- Delete --}}

                                        @if (auth()->user()->hasPermission('students.delete'))
                                            <form method="POST"
                                                action="{{ route('admin.students.destroy', $student) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Are you sure you want to delete this student? This action cannot be undone.'
                                                    );
                                                ">

                                                @csrf
                                                @method('DELETE')


                                                <button type="submit"
                                                    class="inline-flex
                                                           h-10
                                                           items-center
                                                           justify-center
                                                           rounded-xl
                                                           border
                                                           border-red-300
                                                           bg-red-50
                                                           px-5
                                                           text-sm
                                                           font-semibold
                                                           text-red-700
                                                           hover:bg-red-100">
                                                    Delete
                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="9"
                                    class="px-6 py-16
                                           text-center
                                           text-sm
                                           text-slate-500">
                                    No students matched the selected filters.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>



            {{-- =================================================
                PAGINATION
            ================================================== --}}

            @if ($students->hasPages())
                <div
                    class="border-t
                           border-cyan-100
                           bg-white
                           px-6 py-4">
                    {{ $students->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
