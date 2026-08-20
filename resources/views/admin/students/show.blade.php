@extends('layouts.admin')

@section('title', 'Student Profile')

@section('page-title', 'Student Profile')


@php

    /*
     * Student initials.
     */
    $initials = strtoupper(
        mb_substr(
            $student->first_name,
            0,
            1
        )
        .
        mb_substr(
            $student->last_name,
            0,
            1
        )
    );


    /*
     * Student status colour.
     */
    $statusColour =
        $student->studentStatus?->color_code
        ?? '#16a34a';


    $guardianCount =
        $student->guardians->count();

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
                   text-green-700"
        >
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
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    {{-- =====================================================
        BACK BUTTON
    ====================================================== --}}

    <div>

        <a
            href="{{ route(
                'admin.students.index'
            ) }}"
            class="inline-flex
                   items-center
                   gap-2
                   text-sm
                   font-semibold
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

            Back to Students

        </a>

    </div>



    {{-- =====================================================
        PROFILE HEADER
    ====================================================== --}}

    <section
        class="rounded-2xl
               border
               border-slate-200
               bg-white
               p-7
               shadow-sm"
    >

        <div
            class="flex
                   flex-col
                   gap-6
                   lg:flex-row
                   lg:items-center"
        >

            {{-- Avatar --}}
            <div
                class="flex
                       h-28 w-28
                       shrink-0
                       items-center
                       justify-center
                       rounded-full
                       bg-gradient-to-br
                       from-cyan-400
                       to-sky-500
                       text-3xl
                       font-bold
                       text-white
                       shadow-sm"
            >
                {{ $initials }}
            </div>


            {{-- Student Identity --}}
            <div>

                <h1
                    class="text-3xl
                           font-bold
                           tracking-tight
                           text-slate-900"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </h1>


                <p
                    class="mt-1
                           text-base
                           text-slate-500"
                >
                    Student ID:
                    {{ $student->external_id }}
                </p>


                @if ($student->studentStatus)

                    <div class="mt-3">

                        <span
                            class="inline-flex
                                   items-center
                                   gap-2
                                   rounded-full
                                   px-3 py-1.5
                                   text-sm
                                   font-semibold"
                            style="
                                color:
                                {{ $statusColour }};

                                background-color:
                                {{ $statusColour }}15;
                            "
                        >

                            <span
                                class="h-2 w-2
                                       rounded-full"
                                style="
                                    background-color:
                                    {{ $statusColour }};
                                "
                            ></span>

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

    <div
        class="grid
               gap-5
               lg:grid-cols-3"
    >


        {{-- =================================================
            STUDENT INFORMATION
        ================================================== --}}

        <section
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       items-center
                       justify-between
                       gap-4"
            >

                <div
                    class="flex
                           items-center
                           gap-3"
                >

                    <div
                        class="flex
                               h-9 w-9
                               items-center
                               justify-center
                               rounded-full
                               bg-blue-100
                               text-blue-600"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5 w-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6a3.75
                                   3.75 0 1 1-7.5 0
                                   3.75 3.75 0 0 1
                                   7.5 0ZM4.501
                                   20.118a7.5
                                   7.5 0 0 1
                                   14.998 0"
                            />
                        </svg>

                    </div>


                    <h2
                        class="text-lg
                               font-bold
                               text-slate-900"
                    >
                        Student Information
                    </h2>

                </div>


                @if (
                    auth()
                        ->user()
                        ->hasPermission(
                            'students.edit'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.students.edit',
                            [
                                'student' =>
                                    $student,

                                'section' =>
                                    'information',
                            ]
                        ) }}"
                        class="inline-flex
                               items-center
                               gap-1.5
                               text-sm
                               font-semibold
                               text-blue-600
                               hover:text-blue-800"
                    >
                        ✎ Edit
                    </a>

                @endif

            </div>


            <dl class="mt-6 space-y-5">


                <div
                    class="grid
                           grid-cols-[120px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Date of Birth
                    </dt>

                    <dd
                        class="text-sm
                               font-medium
                               text-slate-800"
                    >
                        {{
                            $student
                                ->date_of_birth
                                ?->format('d M Y')
                            ?? '—'
                        }}
                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[120px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Phone
                    </dt>

                    <dd
                        class="text-sm
                               font-medium
                               text-slate-800"
                    >
                        {{ $student->phone ?: '—' }}
                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[120px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Email
                    </dt>

                    <dd
                        class="break-all
                               text-sm
                               font-medium
                               text-blue-600"
                    >
                        {{ $student->email ?: '—' }}
                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[120px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Address
                    </dt>

                    <dd
                        class="text-sm
                               font-medium
                               leading-relaxed
                               text-slate-800"
                    >
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
                   border
                   border-slate-200
                   bg-white
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       items-center
                       justify-between
                       gap-4"
            >

                <div
                    class="flex
                           items-center
                           gap-3"
                >

                    <div
                        class="flex
                               h-9 w-9
                               items-center
                               justify-center
                               rounded-full
                               bg-indigo-100
                               text-indigo-600"
                    >
                        👥
                    </div>


                    <h2
                        class="text-lg
                               font-bold
                               text-slate-900"
                    >
                        Parent / Guardian
                    </h2>

                </div>


                @if (
                    $primaryGuardian
                    &&
                    auth()
                        ->user()
                        ->hasPermission(
                            'students.edit'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.students.edit',
                            [
                                'student' =>
                                    $student,

                                'section' =>
                                    'guardian',
                            ]
                        ) }}"
                        class="text-sm
                               font-semibold
                               text-blue-600"
                    >
                        Edit
                    </a>

                @endif

            </div>


            @if ($primaryGuardian)

                <dl class="mt-6 space-y-5">


                    <div
                        class="grid
                               grid-cols-[110px_1fr]
                               gap-4"
                    >

                        <dt class="text-sm text-slate-500">
                            Name
                        </dt>

                        <dd
                            class="text-sm
                                   font-medium
                                   text-slate-800"
                        >
                            {{ $primaryGuardian->first_name }}
                            {{ $primaryGuardian->last_name }}
                        </dd>

                    </div>



                    <div
                        class="grid
                               grid-cols-[110px_1fr]
                               gap-4"
                    >

                        <dt class="text-sm text-slate-500">
                            Relationship
                        </dt>

                        <dd
                            class="text-sm
                                   font-medium
                                   text-slate-800"
                        >
                            {{
                                $primaryGuardian
                                    ->pivot
                                    ->relationship
                                ?: '—'
                            }}
                        </dd>

                    </div>



                    <div
                        class="grid
                               grid-cols-[110px_1fr]
                               gap-4"
                    >

                        <dt class="text-sm text-slate-500">
                            Phone
                        </dt>

                        <dd
                            class="text-sm
                                   font-medium
                                   text-slate-800"
                        >
                            {{ $primaryGuardian->phone ?: '—' }}
                        </dd>

                    </div>



                    <div
                        class="grid
                               grid-cols-[110px_1fr]
                               gap-4"
                    >

                        <dt class="text-sm text-slate-500">
                            Email
                        </dt>

                        <dd
                            class="break-all
                                   text-sm
                                   font-medium
                                   text-blue-600"
                        >
                            {{ $primaryGuardian->email ?: '—' }}
                        </dd>

                    </div>

                </dl>


                @if ($guardianCount > 1)

                    <div
                        class="mt-5
                               rounded-xl
                               bg-indigo-50
                               px-4 py-3"
                    >

                        <p
                            class="text-xs
                                   font-semibold
                                   text-indigo-700"
                        >
                            + {{ $guardianCount - 1 }}

                            {{
                                $guardianCount - 1 === 1
                                    ? 'additional guardian'
                                    : 'additional guardians'
                            }}
                        </p>

                    </div>

                @endif

            @else

                <div class="py-10 text-center">

                    <p
                        class="text-sm
                               font-semibold
                               text-slate-600"
                    >
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
                   border
                   border-slate-200
                   bg-white
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       items-center
                       justify-between
                       gap-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex
                               h-9 w-9
                               items-center
                               justify-center
                               rounded-full
                               bg-green-100
                               text-green-600"
                    >
                        ✓
                    </div>


                    <h2
                        class="text-lg
                               font-bold
                               text-slate-900"
                    >
                        Student Status
                    </h2>

                </div>


                @if (
                    auth()
                        ->user()
                        ->hasPermission(
                            'students.edit'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.students.edit',
                            [
                                'student' =>
                                    $student,

                                'section' =>
                                    'status',
                            ]
                        ) }}"
                        class="text-sm
                               font-semibold
                               text-blue-600"
                    >
                        ✎ Edit
                    </a>

                @endif

            </div>


            <dl class="mt-6 space-y-5">


                <div
                    class="grid
                           grid-cols-[110px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
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
                                "
                            >
                                {{ $student->studentStatus->status_name }}
                            </span>

                        @else

                            —

                        @endif

                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[110px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Active
                    </dt>

                    <dd
                        class="text-sm
                               font-medium
                               text-slate-800"
                    >
                        {{ $student->is_active ? 'Yes' : 'No' }}
                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[110px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Join Date
                    </dt>

                    <dd
                        class="text-sm
                               font-medium
                               text-slate-800"
                    >
                        {{
                            $student
                                ->join_date
                                ?->format('d M Y')
                            ?? '—'
                        }}
                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[110px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Leave Alone
                    </dt>

                    <dd
                        class="text-sm
                               font-medium
                               text-slate-800"
                    >
                        {{
                            $student->can_leave_alone
                                ? 'Yes'
                                : 'No'
                        }}
                    </dd>

                </div>



                <div
                    class="grid
                           grid-cols-[110px_1fr]
                           gap-4"
                >

                    <dt class="text-sm text-slate-500">
                        Wishlist
                    </dt>

                    <dd
                        class="text-sm
                               font-semibold
                               text-purple-700"
                    >
                        {{ $wishlistEnrolments->count() }}
                    </dd>

                </div>

            </dl>

        </section>

    </div>



    {{-- =====================================================
        SCHEDULE + SUMMARY
    ====================================================== --}}

    <div
        class="grid
               gap-6
               xl:grid-cols-[2fr_0.8fr]"
    >


        {{-- =================================================
            CURRENT SCHEDULE
        ================================================== --}}

        <section
            class="overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >

            {{-- Header --}}
            <div
                class="flex
                       flex-col
                       gap-4
                       border-b
                       border-slate-100
                       px-6 py-5
                       sm:flex-row
                       sm:items-center
                       sm:justify-between"
            >

                <div>

                    <h2
                        class="text-xl
                               font-bold
                               text-slate-900"
                    >
                        Current Schedule
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        {{ $confirmedEnrolments->count() }}

                        {{
                            $confirmedEnrolments->count() === 1
                                ? 'active class'
                                : 'active classes'
                        }}
                    </p>

                </div>


                <div
                    class="flex
                           flex-wrap
                           items-center
                           gap-3"
                >

                    {{-- Edit Schedule --}}
                    @if (
                        $confirmedEnrolments->count() > 0
                        &&
                        auth()
                            ->user()
                            ->hasPermission(
                                'enrolments.edit'
                            )
                    )

                        <a
                            href="{{ route(
                                'admin.student-enrolments.edit-list',
                                $student
                            ) }}"
                            class="inline-flex
                                   h-10
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-blue-200
                                   bg-blue-50
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-blue-700
                                   hover:bg-blue-100"
                        >
                            ✎ Edit Schedule
                        </a>

                    @endif



                    {{-- Add Class --}}
                    @if (
                        auth()
                            ->user()
                            ->hasPermission(
                                'enrolments.create'
                            )
                    )

                        <a
                            href="{{ route(
                                'admin.student-enrolments.create',
                                $student
                            ) }}"
                            class="inline-flex
                                   h-10
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-600
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-blue-700"
                        >
                            + Add Class
                        </a>

                    @endif



                    {{-- Remove Class --}}
                    @if (
                        $confirmedEnrolments->count() > 0
                        &&
                        auth()
                            ->user()
                            ->hasPermission(
                                'enrolments.delete'
                            )
                    )

                        <a
                            href="{{ route(
                                'admin.student-enrolments.remove',
                                $student
                            ) }}"
                            class="inline-flex
                                   h-10
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-red-200
                                   bg-white
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-red-600
                                   hover:bg-red-50"
                        >
                            × Remove Class
                        </a>

                    @endif

                </div>

            </div>



            {{-- Schedule Table --}}
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
                                       text-slate-500"
                            >
                                Day
                            </th>


                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Time
                            </th>


                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Section
                            </th>


                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Duration
                            </th>


                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Status
                            </th>


                            <th
                                class="px-6 py-4
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Wishlist
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse (
                            $confirmedEnrolments
                            as $enrolment
                        )

                            @php

                                $offering =
                                    $enrolment
                                        ->sectionOffering;


                                $activeWishlist =
                                    $enrolment
                                        ->wishlistRequests()
                                        ->where(
                                            'is_active',
                                            true
                                        )
                                        ->where(
                                            'is_wishlist',
                                            true
                                        )
                                        ->first();


                                $hasActiveWishlist =
                                    $activeWishlist
                                    !== null;

                            @endphp


                            <tr
                                class="transition
                                       hover:bg-slate-50"
                            >


                                {{-- Day --}}
                                <td
                                    class="px-6 py-5
                                           text-sm
                                           font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $offering
                                            ?->day
                                            ?->day_name
                                        ?? '—'
                                    }}
                                </td>



                                {{-- Time --}}
                                <td
                                    class="whitespace-nowrap
                                           px-6 py-5
                                           text-sm
                                           text-slate-700"
                                >

                                    @if ($offering)

                                        {{
                                            \Carbon\Carbon::parse(
                                                $offering->start_time
                                            )->format('g:i A')
                                        }}

                                        –

                                        {{
                                            \Carbon\Carbon::parse(
                                                $offering->end_time
                                            )->format('g:i A')
                                        }}

                                    @else

                                        —

                                    @endif

                                </td>



                                {{-- Section --}}
                                <td
                                    class="px-6 py-5
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        $offering
                                            ?->section
                                            ?->section_name
                                        ?? '—'
                                    }}
                                </td>



                                {{-- Duration --}}
                                <td
                                    class="px-6 py-5
                                           text-sm
                                           text-slate-700"
                                >

                                    @if ($offering)

                                        {{
                                            $offering
                                                ->duration_minutes
                                        }}
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
                                               text-green-700"
                                    >
                                        Active
                                    </span>

                                </td>



                                {{-- Wishlist Action --}}
                                <td
                                    class="px-6 py-5
                                           text-center"
                                >

                                    @if (
                                        auth()
                                            ->user()
                                            ->hasPermission(
                                                'wishlist.create'
                                            )
                                    )

                                        <a
                                            href="{{ route(
                                                'admin.wishlist.create',
                                                $enrolment
                                            ) }}"
                                            class="inline-flex
                                                   h-9
                                                   items-center
                                                   justify-center
                                                   rounded-lg
                                                   border
                                                   border-purple-300
                                                   bg-white
                                                   px-4
                                                   text-xs
                                                   font-semibold
                                                   text-purple-700
                                                   transition
                                                   hover:bg-purple-50"
                                        >

                                            @if ($hasActiveWishlist)

                                                ✎ Edit Wishlist

                                            @else

                                                + Wishlist

                                            @endif

                                        </a>

                                    @else

                                        <span class="text-slate-400">
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-14
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >
                                    No active class enrolments.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>



            {{-- Info --}}
            @if ($confirmedEnrolments->isNotEmpty())

                <div
                    class="border-t
                           border-slate-100
                           p-5"
                >

                    <div
                        class="rounded-xl
                               border
                               border-blue-100
                               bg-blue-50
                               px-4 py-3"
                    >

                        <p
                            class="text-sm
                                   font-semibold
                                   text-blue-800"
                        >
                            One active wishlist can be recorded
                            for each enrolled class.
                        </p>

                        <p
                            class="mt-1
                                   text-xs
                                   text-blue-600"
                        >
                            The preferred class time can be
                            changed whenever required.
                        </p>

                    </div>

                </div>

            @endif

        </section>



        {{-- =================================================
            RIGHT SIDE
        ================================================== --}}

        <div class="space-y-4">


            {{-- Attendance --}}
            <section
                class="rounded-2xl
                       border
                       border-blue-100
                       bg-blue-50
                       p-5"
            >

                <div
                    class="flex
                           items-center
                           justify-between"
                >

                    <p
                        class="text-sm
                               font-bold
                               text-blue-800"
                    >
                        Attendance Summary
                    </p>

                    <span
                        class="text-xs
                               text-slate-400"
                    >
                        Coming later
                    </span>

                </div>


                <div
                    class="mt-5
                           grid
                           grid-cols-2
                           divide-x
                           divide-blue-200"
                >

                    <div class="text-center">

                        <p
                            class="text-2xl
                                   font-bold
                                   text-blue-700"
                        >
                            —
                        </p>

                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            Attendance Rate
                        </p>

                    </div>


                    <div class="text-center">

                        <p
                            class="text-2xl
                                   font-bold
                                   text-blue-700"
                        >
                            —
                        </p>

                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            Sessions Attended
                        </p>

                    </div>

                </div>

            </section>



            {{-- Absence --}}
            <section
                class="rounded-2xl
                       border
                       border-red-100
                       bg-red-50
                       p-5"
            >

                <div
                    class="flex
                           items-center
                           justify-between"
                >

                    <p
                        class="text-sm
                               font-bold
                               text-red-700"
                    >
                        Absence Summary
                    </p>

                    <span
                        class="text-xs
                               text-slate-400"
                    >
                        Coming later
                    </span>

                </div>


                <div class="mt-5 text-center">

                    <p
                        class="text-2xl
                               font-bold
                               text-red-600"
                    >
                        —
                    </p>

                    <p
                        class="mt-1
                               text-xs
                               text-slate-500"
                    >
                        Total Absences
                    </p>

                </div>

            </section>



            {{-- =================================================
                WISHLIST SLIDER
            ================================================== --}}

            <section
                class="rounded-2xl
                       border
                       border-purple-100
                       bg-purple-50
                       p-5"
            >

                {{-- Header --}}
                <div
                    class="flex
                           items-center
                           justify-between
                           gap-3"
                >

                    <div>

                        <p
                            class="text-sm
                                   font-bold
                                   text-purple-800"
                        >
                            Wishlist
                        </p>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            {{ $wishlistEnrolments->count() }}

                            active

                            {{
                                $wishlistEnrolments->count() === 1
                                    ? 'request'
                                    : 'requests'
                            }}
                        </p>

                    </div>


                    @if (
                        auth()
                            ->user()
                            ->hasPermission(
                                'wishlist.view'
                            )
                    )

                        <a
                            href="{{ route(
                                'admin.wishlist.index',
                                [
                                    'search' =>
                                        $student->external_id,
                                ]
                            ) }}"
                            class="text-xs
                                   font-semibold
                                   text-purple-700
                                   hover:text-purple-900"
                        >
                            View All
                        </a>

                    @endif

                </div>



                {{-- Slider --}}
                @if ($wishlistEnrolments->isNotEmpty())

                    <div
                        id="wishlistSlider"
                        class="mt-4"
                    >

                        @foreach (
                            $wishlistEnrolments
                            as $wishlist
                        )

                            @php

                                $requested =
                                    $wishlist
                                        ->sectionOffering;


                                $sourceEnrolment =
                                    $wishlist
                                        ->wishlistForEnrolment;


                                $sourceOffering =
                                    $sourceEnrolment
                                        ?->sectionOffering;

                            @endphp


                            <div
                                class="wishlist-slide
                                       {{
                                           $loop->first
                                               ? ''
                                               : 'hidden'
                                       }}"
                                data-index="{{ $loop->index }}"
                            >

                                <div
                                    class="rounded-xl
                                           border
                                           border-purple-100
                                           bg-white
                                           p-4"
                                >


                                    {{-- Requested --}}
                                    <div
                                        class="flex
                                               items-start
                                               justify-between
                                               gap-3"
                                    >

                                        <div>

                                            <p
                                                class="text-xs
                                                       font-semibold
                                                       uppercase
                                                       tracking-wide
                                                       text-purple-500"
                                            >
                                                Requested
                                            </p>


                                            <p
                                                class="mt-1
                                                       text-lg
                                                       font-bold
                                                       text-slate-900"
                                            >
                                                {{
                                                    $requested
                                                        ?->section
                                                        ?->section_name
                                                    ?? '—'
                                                }}
                                            </p>

                                        </div>


                                        <span
                                            class="rounded-full
                                                   bg-purple-100
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-purple-700"
                                        >
                                            Wishlist
                                        </span>

                                    </div>



                                    {{-- Preferred Class --}}
                                    <div class="mt-4">

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Preferred Class
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-sm
                                                   font-semibold
                                                   text-slate-700"
                                        >

                                            {{
                                                $requested
                                                    ?->day
                                                    ?->day_name
                                                ?? '—'
                                            }}

                                            @if ($requested)

                                                ·

                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $requested
                                                            ->start_time
                                                    )->format(
                                                        'g:i A'
                                                    )
                                                }}

                                                –

                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $requested
                                                            ->end_time
                                                    )->format(
                                                        'g:i A'
                                                    )
                                                }}

                                            @endif

                                        </p>

                                    </div>



                                    {{-- Current Class --}}
                                    @if ($sourceOffering)

                                        <div class="mt-4">

                                            <p
                                                class="text-xs
                                                       text-slate-400"
                                            >
                                                Current Class
                                            </p>


                                            <p
                                                class="mt-1
                                                       text-sm
                                                       font-semibold
                                                       text-slate-700"
                                            >
                                                {{
                                                    $sourceOffering
                                                        ?->section
                                                        ?->section_name
                                                    ?? '—'
                                                }}
                                            </p>


                                            <p
                                                class="mt-1
                                                       text-xs
                                                       text-slate-500"
                                            >

                                                {{
                                                    $sourceOffering
                                                        ?->day
                                                        ?->day_name
                                                    ?? '—'
                                                }}

                                                ·

                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $sourceOffering
                                                            ->start_time
                                                    )->format(
                                                        'g:i A'
                                                    )
                                                }}

                                                –

                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $sourceOffering
                                                            ->end_time
                                                    )->format(
                                                        'g:i A'
                                                    )
                                                }}

                                            </p>

                                        </div>

                                    @endif



                                    {{-- Change Wishlist --}}
                                    @if (
                                        auth()
                                            ->user()
                                            ->hasPermission(
                                                'wishlist.create'
                                            )
                                        &&
                                        $sourceEnrolment
                                    )

                                        <div
                                            class="mt-4
                                                   border-t
                                                   border-purple-100
                                                   pt-3"
                                        >

                                            <a
                                                href="{{ route(
                                                    'admin.wishlist.create',
                                                    $sourceEnrolment
                                                ) }}"
                                                class="inline-flex
                                                       items-center
                                                       gap-1
                                                       text-xs
                                                       font-semibold
                                                       text-purple-700
                                                       hover:text-purple-900"
                                            >
                                                ✎ Change Wishlist
                                            </a>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach



                        {{-- Controls --}}
                        @if ($wishlistEnrolments->count() > 1)

                            <div
                                class="mt-4
                                       flex
                                       items-center
                                       justify-between"
                            >

                                {{-- Previous --}}
                                <button
                                    type="button"
                                    id="wishlistPrev"
                                    class="inline-flex
                                           h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-lg
                                           border
                                           border-purple-200
                                           bg-white
                                           text-purple-700
                                           transition
                                           hover:bg-purple-100"
                                >
                                    ←
                                </button>



                                {{-- Counter + Dots --}}
                                <div
                                    class="flex
                                           items-center
                                           gap-3"
                                >

                                    <span
                                        id="wishlistCounter"
                                        class="text-xs
                                               font-semibold
                                               text-purple-700"
                                    >
                                        1 /
                                        {{ $wishlistEnrolments->count() }}
                                    </span>


                                    <div
                                        class="flex
                                               items-center
                                               gap-1.5"
                                    >

                                        @foreach (
                                            $wishlistEnrolments
                                            as $wishlist
                                        )

                                            <button
                                                type="button"
                                                class="wishlist-dot
                                                       h-2 w-2
                                                       rounded-full
                                                       transition
                                                       {{
                                                           $loop->first
                                                               ? 'bg-purple-600'
                                                               : 'bg-purple-200'
                                                       }}"
                                                data-index="{{ $loop->index }}"
                                                aria-label="Wishlist {{ $loop->iteration }}"
                                            ></button>

                                        @endforeach

                                    </div>

                                </div>



                                {{-- Next --}}
                                <button
                                    type="button"
                                    id="wishlistNext"
                                    class="inline-flex
                                           h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-lg
                                           border
                                           border-purple-200
                                           bg-white
                                           text-purple-700
                                           transition
                                           hover:bg-purple-100"
                                >
                                    →
                                </button>

                            </div>

                        @endif

                    </div>


                @else

                    {{-- No Wishlist --}}
                    <div
                        class="mt-4
                               rounded-xl
                               border
                               border-dashed
                               border-purple-200
                               bg-white/60
                               px-4 py-6
                               text-center"
                    >

                        <p
                            class="text-sm
                                   font-semibold
                                   text-slate-600"
                        >
                            No active wishlist
                        </p>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-400"
                        >
                            Wishlist details will appear here.
                        </p>

                    </div>

                @endif

            </section>

        </div>

    </div>



    {{-- =====================================================
        STUDENT NOTES
    ====================================================== --}}

    <section
        class="rounded-2xl
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="flex
                   items-center
                   justify-between
                   gap-4"
        >

            <h2
                class="text-lg
                       font-bold
                       text-slate-900"
            >
                Student Notes
            </h2>


            @if (
                auth()
                    ->user()
                    ->hasPermission(
                        'students.edit'
                    )
            )

                <a
                    href="{{ route(
                        'admin.students.edit',
                        [
                            'student' =>
                                $student,

                            'section' =>
                                'notes',
                        ]
                    ) }}"
                    class="text-sm
                           font-semibold
                           text-blue-600
                           hover:text-blue-800"
                >
                    ✎ Edit
                </a>

            @endif

        </div>


        @if ($student->notes)

            <p
                class="mt-4
                       text-sm
                       leading-relaxed
                       text-slate-700"
            >
                {{ $student->notes }}
            </p>

        @else

            <p
                class="mt-4
                       text-sm
                       text-slate-400"
            >
                No notes have been recorded
                for this student.
            </p>

        @endif

    </section>

</div>

@endsection



{{-- =========================================================
    WISHLIST SLIDER SCRIPT
========================================================== --}}

@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const slides =
            document.querySelectorAll(
                '.wishlist-slide'
            );


        const dots =
            document.querySelectorAll(
                '.wishlist-dot'
            );


        const previousButton =
            document.getElementById(
                'wishlistPrev'
            );


        const nextButton =
            document.getElementById(
                'wishlistNext'
            );


        const counter =
            document.getElementById(
                'wishlistCounter'
            );


        /*
         * No slider needed when there
         * is only one wishlist.
         */
        if (slides.length <= 1) {
            return;
        }


        let currentIndex = 0;


        /*
        |--------------------------------------------------------------------------
        | Show Slide
        |--------------------------------------------------------------------------
        */

        function showSlide(index) {

            /*
             * Previous from first
             * goes to last.
             */
            if (index < 0) {

                index =
                    slides.length - 1;
            }


            /*
             * Next from last
             * returns to first.
             */
            if (
                index >=
                slides.length
            ) {

                index = 0;
            }


            currentIndex =
                index;


            /*
             * Slides
             */
            slides.forEach(
                function (
                    slide,
                    slideIndex
                ) {

                    if (
                        slideIndex
                        ===
                        currentIndex
                    ) {

                        slide.classList.remove(
                            'hidden'
                        );

                    }
                    else {

                        slide.classList.add(
                            'hidden'
                        );

                    }
                }
            );


            /*
             * Dots
             */
            dots.forEach(
                function (
                    dot,
                    dotIndex
                ) {

                    if (
                        dotIndex
                        ===
                        currentIndex
                    ) {

                        dot.classList.remove(
                            'bg-purple-200'
                        );

                        dot.classList.add(
                            'bg-purple-600'
                        );

                    }
                    else {

                        dot.classList.remove(
                            'bg-purple-600'
                        );

                        dot.classList.add(
                            'bg-purple-200'
                        );

                    }
                }
            );


            /*
             * Counter
             */
            if (counter) {

                counter.textContent =
                    (currentIndex + 1)
                    +
                    ' / '
                    +
                    slides.length;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Previous
        |--------------------------------------------------------------------------
        */

        if (previousButton) {

            previousButton.addEventListener(
                'click',
                function () {

                    showSlide(
                        currentIndex - 1
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Next
        |--------------------------------------------------------------------------
        */

        if (nextButton) {

            nextButton.addEventListener(
                'click',
                function () {

                    showSlide(
                        currentIndex + 1
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Dot Navigation
        |--------------------------------------------------------------------------
        */

        dots.forEach(
            function (dot) {

                dot.addEventListener(
                    'click',
                    function () {

                        showSlide(
                            Number(
                                this.dataset.index
                            )
                        );
                    }
                );
            }
        );

    }
);
</script>

@endpush
