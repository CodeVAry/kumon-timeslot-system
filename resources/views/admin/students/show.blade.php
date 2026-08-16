@extends('layouts.admin')

@section('title', 'Student Profile')

@section('page-title', 'Student Profile')

@php
    /*
     * Student initials.
     */
    $initials = strtoupper(mb_substr($student->first_name, 0, 1) . mb_substr($student->last_name, 0, 1));

    /*
     * Student status colour.
     */
    $statusColour = $student->studentStatus?->color_code ?? '#16a34a';

    $guardianCount = $student->guardians->count();
@endphp


@section('content')

    <div class="space-y-6">

        {{-- =====================================================
        SUCCESS MESSAGE
    ====================================================== --}}
        @if (session('success'))
            <div
                class="rounded-xl border
                   border-green-200
                   bg-green-50 px-5 py-4
                   text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif


        {{-- =====================================================
        BACK BUTTON
    ====================================================== --}}
        <div>

            <a href="{{ route('admin.students.index') }}"
                class="inline-flex items-center
                   gap-2 text-sm
                   font-semibold
                   text-blue-600
                   hover:text-blue-800">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0
                               7.5-7.5M3 12h18" />
                </svg>

                Back to Students

            </a>

        </div>


        {{-- =====================================================
        PROFILE HEADER
    ====================================================== --}}
        <section
            class="rounded-2xl
               border border-slate-200
               bg-white p-7
               shadow-sm">

            <div class="flex flex-col gap-6
                   lg:flex-row
                   lg:items-center">

                {{-- Avatar --}}
                <div
                    class="flex h-28 w-28
                       shrink-0 items-center
                       justify-center
                       rounded-full
                       bg-gradient-to-br
                       from-cyan-400
                       to-sky-500
                       text-3xl font-bold
                       text-white shadow-sm">
                    {{ $initials }}
                </div>


                {{-- Student identity --}}
                <div>

                    <h1
                        class="text-3xl
                           font-bold
                           tracking-tight
                           text-slate-900">
                        {{ $student->first_name }}
                        {{ $student->last_name }}
                    </h1>


                    <p class="mt-1 text-base
                           text-slate-500">
                        Student ID:
                        {{ $student->external_id }}
                    </p>


                    @if ($student->studentStatus)
                        <div class="mt-3">

                            <span
                                class="inline-flex
                                   items-center gap-2
                                   rounded-full
                                   px-3 py-1.5
                                   text-sm font-semibold"
                                style="
                                color:
                                {{ $statusColour }};

                                background-color:
                                {{ $statusColour }}15;
                            ">

                                <span class="h-2 w-2
                                       rounded-full"
                                    style="
                                    background-color:
                                    {{ $statusColour }};
                                "></span>

                                {{ $student->studentStatus->status_name }}

                            </span>

                        </div>
                    @endif

                </div>

            </div>

        </section>


        {{-- =====================================================
        INFORMATION CARDS
    ====================================================== --}}
        <div class="grid gap-5
               lg:grid-cols-3">


            {{-- =================================================
            STUDENT INFORMATION
        ================================================== --}}
            <section
                class="rounded-2xl
                   border border-slate-200
                   bg-white p-6
                   shadow-sm">

                <div class="flex items-center
                       justify-between gap-4">

                    <div class="flex items-center
                           gap-3">

                        <div
                            class="flex h-9 w-9
                               items-center
                               justify-center
                               rounded-full
                               bg-blue-100
                               text-blue-600">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75
                                           3.75 0 1 1-7.5 0
                                           3.75 3.75 0 0 1
                                           7.5 0ZM4.501
                                           20.118a7.5
                                           7.5 0 0 1
                                           14.998 0" />
                            </svg>

                        </div>


                        <h2
                            class="text-lg
                               font-bold
                               text-slate-900">
                            Student Information
                        </h2>

                    </div>


                    @if (auth()->user()->hasPermission('students.edit'))
                        <a href="{{ route('admin.students.edit', [
                            'student' => $student,
                            'section' => 'information',
                        ]) }}"
                            class="inline-flex
                               items-center gap-1.5
                               text-sm font-semibold
                               text-blue-600
                               hover:text-blue-800">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487
                                           1.687-1.688a1.875
                                           1.875 0 1 1
                                           2.652 2.652L10.582
                                           16.07a4.5 4.5
                                           0 0 1-1.897
                                           1.13L6 18l.8-2.685" />
                            </svg>

                            Edit

                        </a>
                    @endif

                </div>


                <dl class="mt-6 space-y-5">

                    <div class="grid
                           grid-cols-[120px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Date of Birth
                        </dt>

                        <dd
                            class="text-sm
                               font-medium
                               text-slate-800">
                            {{ $student->date_of_birth?->format('d M Y') ?? '—' }}
                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[120px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Phone
                        </dt>

                        <dd
                            class="text-sm
                               font-medium
                               text-slate-800">
                            {{ $student->phone ?: '—' }}
                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[120px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Email
                        </dt>

                        <dd
                            class="break-all
                               text-sm
                               font-medium
                               text-blue-600">
                            {{ $student->email ?: '—' }}
                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[120px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Address
                        </dt>

                        <dd
                            class="text-sm
                               font-medium
                               leading-relaxed
                               text-slate-800">
                            {{ $student->address ?: '—' }}
                        </dd>

                    </div>

                </dl>

            </section>



            {{-- =================================================
            PARENT / GUARDIAN
        ================================================== --}}
            <section
                class="rounded-2xl
                   border border-slate-200
                   bg-white p-6
                   shadow-sm">

                <div class="flex items-center
                       justify-between gap-4">

                    <div class="flex items-center
                           gap-3">

                        <div
                            class="flex h-9 w-9
                               items-center
                               justify-center
                               rounded-full
                               bg-indigo-100
                               text-indigo-600">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6.75a3
                                           3 0 1 1-6 0
                                           3 3 0 0 1
                                           6 0Zm6 12a6
                                           6 0 0 0-12 0" />
                            </svg>

                        </div>


                        <h2
                            class="text-lg
                               font-bold
                               text-slate-900">
                            Parent / Guardian
                        </h2>

                    </div>


                    @if ($primaryGuardian && auth()->user()->hasPermission('students.edit'))
                        <a href="{{ route('admin.students.edit', [
                            'student' => $student,
                            'section' => 'guardian',
                        ]) }}"
                            class="inline-flex
                               items-center gap-1.5
                               text-sm font-semibold
                               text-blue-600
                               hover:text-blue-800">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487
                                           1.687-1.688a1.875
                                           1.875 0 1 1
                                           2.652 2.652L10.582
                                           16.07" />
                            </svg>

                            Edit

                        </a>
                    @endif

                </div>


                @if ($primaryGuardian)

                    <dl class="mt-6 space-y-5">

                        <div
                            class="grid
                               grid-cols-[110px_1fr]
                               gap-4">

                            <dt class="text-sm
                                   text-slate-500">
                                Name
                            </dt>

                            <dd
                                class="text-sm
                                   font-medium
                                   text-slate-800">
                                {{ $primaryGuardian->first_name }}
                                {{ $primaryGuardian->last_name }}
                            </dd>

                        </div>


                        <div
                            class="grid
                               grid-cols-[110px_1fr]
                               gap-4">

                            <dt class="text-sm
                                   text-slate-500">
                                Relationship
                            </dt>

                            <dd
                                class="text-sm
                                   font-medium
                                   text-slate-800">
                                {{ $primaryGuardian->pivot->relationship ?: '—' }}
                            </dd>

                        </div>


                        <div
                            class="grid
                               grid-cols-[110px_1fr]
                               gap-4">

                            <dt class="text-sm
                                   text-slate-500">
                                Phone
                            </dt>

                            <dd
                                class="text-sm
                                   font-medium
                                   text-slate-800">
                                {{ $primaryGuardian->phone ?: '—' }}
                            </dd>

                        </div>


                        <div
                            class="grid
                               grid-cols-[110px_1fr]
                               gap-4">

                            <dt class="text-sm
                                   text-slate-500">
                                Email
                            </dt>

                            <dd
                                class="break-all
                                   text-sm
                                   font-medium
                                   text-blue-600">
                                {{ $primaryGuardian->email ?: '—' }}
                            </dd>

                        </div>

                    </dl>


                    @if ($guardianCount > 1)
                        <div
                            class="mt-5
                               rounded-xl
                               bg-indigo-50
                               px-4 py-3">

                            <p
                                class="text-xs
                                   font-semibold
                                   text-indigo-700">
                                + {{ $guardianCount - 1 }}

                                {{ $guardianCount - 1 === 1 ? 'additional guardian' : 'additional guardians' }}
                            </p>

                        </div>
                    @endif
                @else
                    <div class="py-10 text-center">

                        <p
                            class="text-sm
                               font-semibold
                               text-slate-600">
                            No guardian recorded
                        </p>

                    </div>

                @endif

            </section>



            {{-- =================================================
            STUDENT STATUS
        ================================================== --}}
            <section
                class="rounded-2xl
                   border border-slate-200
                   bg-white p-6
                   shadow-sm">

                <div class="flex items-center
                       justify-between gap-4">

                    <div class="flex items-center
                           gap-3">

                        <div
                            class="flex h-9 w-9
                               items-center
                               justify-center
                               rounded-full
                               bg-green-100
                               text-green-600">
                            ✓
                        </div>


                        <h2
                            class="text-lg
                               font-bold
                               text-slate-900">
                            Student Status
                        </h2>

                    </div>


                    @if (auth()->user()->hasPermission('students.edit'))
                        <a href="{{ route('admin.students.edit', [
                            'student' => $student,
                            'section' => 'status',
                        ]) }}"
                            class="inline-flex
                               items-center gap-1.5
                               text-sm font-semibold
                               text-blue-600
                               hover:text-blue-800">
                            ✎ Edit
                        </a>
                    @endif

                </div>


                <dl class="mt-6 space-y-5">

                    <div class="grid
                           grid-cols-[110px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Status
                        </dt>

                        <dd>

                            @if ($student->studentStatus)
                                <span
                                    class="inline-flex
                                       rounded-full
                                       px-3 py-1
                                       text-xs
                                       font-semibold"
                                    style="
                                    color:
                                    {{ $statusColour }};

                                    background-color:
                                    {{ $statusColour }}15;
                                ">
                                    {{ $student->studentStatus->status_name }}
                                </span>
                            @else
                                <span class="text-slate-400">
                                    —
                                </span>
                            @endif

                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[110px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Active
                        </dt>

                        <dd
                            class="text-sm
                               font-medium
                               text-slate-800">
                            {{ $student->is_active ? 'Yes' : 'No' }}
                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[110px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Join Date
                        </dt>

                        <dd
                            class="text-sm
                               font-medium
                               text-slate-800">
                            {{ $student->join_date?->format('d M Y') ?? '—' }}
                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[110px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Leave Alone
                        </dt>

                        <dd
                            class="text-sm
                               font-medium
                               text-slate-800">
                            {{ $student->can_leave_alone ? 'Yes' : 'No' }}
                        </dd>

                    </div>


                    <div class="grid
                           grid-cols-[110px_1fr]
                           gap-4">

                        <dt class="text-sm
                               text-slate-500">
                            Wishlist
                        </dt>

                        <dd
                            class="text-sm
                               font-semibold
                               text-purple-700">
                            {{ $wishlistEnrolments->count() }}
                        </dd>

                    </div>

                </dl>

            </section>

        </div>



        {{-- =====================================================
        SCHEDULE + SUMMARY PANEL
    ====================================================== --}}
        <div class="grid gap-6
               xl:grid-cols-[2fr_0.8fr]">


            {{-- =================================================
            CURRENT SCHEDULE
        ================================================== --}}
            <section
                class="overflow-hidden
                   rounded-2xl
                   border border-slate-200
                   bg-white shadow-sm">

                {{-- Schedule header --}}
                <div
                    class="flex flex-col gap-4
                       border-b
                       border-slate-100
                       px-6 py-5
                       sm:flex-row
                       sm:items-center
                       sm:justify-between">

                    <div>

                        <h2
                            class="text-xl
                               font-bold
                               text-slate-900">
                            Current Schedule
                        </h2>

                        <p
                            class="mt-1
                               text-sm
                               text-slate-500">
                            {{ $confirmedEnrolments->count() }}

                            {{ $confirmedEnrolments->count() === 1 ? 'active class' : 'active classes' }}
                        </p>

                    </div>


                    {{-- Schedule action buttons --}}
                    <div class="flex flex-wrap items-center gap-3">

                        {{-- =================================================
        EDIT SCHEDULE
    ================================================== --}}
                        @if ($confirmedEnrolments->count() > 0)

                            @if (auth()->user()->hasPermission('enrolments.edit'))
                                <a href="{{ route('admin.student-enrolments.edit-list', $student) }}"
                                    class="inline-flex h-10
                       items-center justify-center
                       gap-2 rounded-xl
                       border border-blue-200
                       bg-blue-50 px-5
                       text-sm font-semibold
                       text-blue-700
                       hover:bg-blue-100">
                                    ✎ Edit Schedule
                                </a>
                            @endif
                        @else
                            <button type="button" disabled title="No current class schedule to edit."
                                class="inline-flex h-10
                   cursor-not-allowed
                   items-center
                   justify-center gap-2
                   rounded-xl
                   border border-slate-200
                   bg-slate-100 px-5
                   text-sm font-semibold
                   text-slate-400">
                                ✎ Edit Schedule
                            </button>

                        @endif



                        {{-- =================================================
        ADD CLASS
    ================================================== --}}
                        @if (auth()->user()->hasPermission('enrolments.create'))
                            <a href="{{ route('admin.student-enrolments.create', $student) }}"
                                class="inline-flex h-10
                   items-center justify-center
                   gap-2 rounded-xl
                   bg-blue-600 px-5
                   text-sm font-semibold
                   text-white
                   shadow-sm
                   hover:bg-blue-700">
                                + Add Class
                            </a>
                        @endif



                        {{-- =================================================
        REMOVE CLASS
    ================================================== --}}
                        @if ($confirmedEnrolments->count() > 0)

                            @if (auth()->user()->hasPermission('enrolments.delete'))
                                <a href="{{ route('admin.student-enrolments.remove', $student) }}"
                                    class="inline-flex h-10
                       items-center
                       justify-center gap-2
                       rounded-xl
                       border border-red-200
                       bg-white px-5
                       text-sm font-semibold
                       text-red-600
                       hover:bg-red-50">
                                    × Remove Class
                                </a>
                            @endif
                        @else
                            <button type="button" disabled title="No current class to remove."
                                class="inline-flex h-10
                   cursor-not-allowed
                   items-center
                   justify-center gap-2
                   rounded-xl
                   border border-slate-200
                   bg-slate-100 px-5
                   text-sm font-semibold
                   text-slate-400">
                                × Remove Class
                            </button>

                        @endif

                    </div>

                </div>


                {{-- Schedule table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-slate-50">

                            <tr>

                                <th
                                    class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500">
                                    Day
                                </th>

                                <th
                                    class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500">
                                    Time
                                </th>

                                <th
                                    class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500">
                                    Section
                                </th>

                                <th
                                    class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500">
                                    Duration
                                </th>

                                <th
                                    class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y
                               divide-slate-100">

                            @forelse ($confirmedEnrolments
                                    as $enrolment)
                                @php
                                    $offering = $enrolment->sectionOffering;
                                @endphp


                                <tr class="transition
                                       hover:bg-slate-50">

                                    {{-- Day --}}
                                    <td
                                        class="px-6 py-5
                                           text-sm
                                           font-semibold
                                           text-slate-800">
                                        {{ $offering?->day?->day_name ?? '—' }}
                                    </td>


                                    {{-- Time --}}
                                    <td
                                        class="whitespace-nowrap
                                           px-6 py-5
                                           text-sm
                                           text-slate-700">

                                        @if ($offering)
                                            {{ \Carbon\Carbon::parse($offering->start_time)->format('g:i A') }}

                                            –

                                            {{ \Carbon\Carbon::parse($offering->end_time)->format('g:i A') }}
                                        @else
                                            —
                                        @endif

                                    </td>


                                    {{-- Section --}}
                                    <td
                                        class="px-6 py-5
                                           text-sm
                                           text-slate-700">
                                        {{ $offering?->section?->section_name ?? '—' }}
                                    </td>


                                    {{-- Duration --}}
                                    <td
                                        class="whitespace-nowrap
                                           px-6 py-5
                                           text-sm
                                           text-slate-700">

                                        @if ($offering)
                                            {{ $offering->duration_minutes }}
                                            min
                                        @else
                                            —
                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-6 py-5">

                                        <span
                                            class="inline-flex
                                               rounded-full
                                               bg-green-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-green-700">
                                            Active
                                        </span>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="px-6 py-14
                                           text-center
                                           text-sm
                                           text-slate-500">
                                        No active class enrolments.
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>



            {{-- =================================================
            RIGHT SUMMARY PANELS
        ================================================== --}}
            <div class="space-y-4">


                {{-- Attendance Summary --}}
                <section
                    class="rounded-2xl
                       border
                       border-blue-100
                       bg-blue-50
                       p-5">

                    <div class="flex items-center
                           justify-between">

                        <p
                            class="text-sm
                               font-bold
                               text-blue-800">
                            Attendance Summary
                        </p>

                        <span class="text-xs
                               text-slate-400">
                            Coming later
                        </span>

                    </div>


                    <div
                        class="mt-5 grid
                           grid-cols-2
                           divide-x
                           divide-blue-200">

                        <div class="text-center">

                            <p
                                class="text-2xl
                                   font-bold
                                   text-blue-700">
                                —
                            </p>

                            <p
                                class="mt-1
                                   text-xs
                                   text-slate-500">
                                Attendance Rate
                            </p>

                        </div>


                        <div class="text-center">

                            <p
                                class="text-2xl
                                   font-bold
                                   text-blue-700">
                                —
                            </p>

                            <p
                                class="mt-1
                                   text-xs
                                   text-slate-500">
                                Sessions Attended
                            </p>

                        </div>

                    </div>

                </section>



                {{-- Absence Summary --}}
                <section
                    class="rounded-2xl
                       border
                       border-red-100
                       bg-red-50
                       p-5">

                    <div class="flex items-center
                           justify-between">

                        <p
                            class="text-sm
                               font-bold
                               text-red-700">
                            Absence Summary
                        </p>

                        <span class="text-xs
                               text-slate-400">
                            Coming later
                        </span>

                    </div>


                    <div class="mt-5 text-center">

                        <p
                            class="text-2xl
                               font-bold
                               text-red-600">
                            —
                        </p>

                        <p
                            class="mt-1
                               text-xs
                               text-slate-500">
                            Total Absences
                        </p>

                    </div>

                </section>



                {{-- Wishlist --}}
                <section
                    class="rounded-2xl
                       border
                       border-purple-100
                       bg-purple-50
                       p-5">

                    <p class="text-sm
                           font-bold
                           text-purple-800">
                        Wishlist
                    </p>


                    <div
                        class="mt-4 flex
                           items-center
                           justify-between">

                        <div>

                            <p
                                class="text-3xl
                                   font-bold
                                   text-purple-700">
                                {{ $wishlistEnrolments->count() }}
                            </p>

                            <p
                                class="mt-1
                                   text-xs
                                   text-slate-500">
                                Requested class places
                            </p>

                        </div>


                        <div
                            class="flex h-11 w-11
                               items-center
                               justify-center
                               rounded-xl
                               bg-white
                               text-purple-600">
                            ★
                        </div>

                    </div>

                </section>

            </div>

        </div>



        {{-- =====================================================
        STUDENT NOTES
    ====================================================== --}}
        <section
            class="rounded-2xl
               border border-slate-200
               bg-white p-6
               shadow-sm">

            <div class="flex items-center
                   justify-between gap-4">

                <h2 class="text-lg
                       font-bold
                       text-slate-900">
                    Student Notes
                </h2>


                @if (auth()->user()->hasPermission('students.edit'))
                    <a href="{{ route('admin.students.edit', [
                        'student' => $student,
                        'section' => 'notes',
                    ]) }}"
                        class="inline-flex
                           items-center gap-1.5
                           text-sm
                           font-semibold
                           text-blue-600
                           hover:text-blue-800">
                        ✎ Edit
                    </a>
                @endif

            </div>


            @if ($student->notes)
                <p
                    class="mt-4
                       text-sm
                       leading-relaxed
                       text-slate-700">
                    {{ $student->notes }}
                </p>
            @else
                <p class="mt-4
                       text-sm
                       text-slate-400">
                    No notes have been recorded
                    for this student.
                </p>
            @endif

        </section>

    </div>

@endsection
