@extends('layouts.admin')

@section('title', 'Leave Details')

@section('page-title', 'Leave Details')


@php

    $breadcrumbs = [
        [
            'label' => 'Leave Management',
            'url' => route('admin.leave.index'),
        ],
        [
            'label' => 'Leave Details',
            'url' => null,
        ],
    ];


    $student =
        $leave->student;


    $homeworkLabels = [
        'none_required' =>
            'None Required',

        'same_as_normal' =>
            'Same as Normal',

        'increase' =>
            'Increase',

        'decrease' =>
            'Decrease',
    ];


    /*
    |--------------------------------------------------------------------------
    | Duration
    |--------------------------------------------------------------------------
    */

    $duration =
        $leave
            ->start_date
            ->diffInDays(
                $leave->expected_return_date
            );


    /*
    |--------------------------------------------------------------------------
    | Today
    |--------------------------------------------------------------------------
    */

    $today =
        now()->startOfDay();


    /*
    |--------------------------------------------------------------------------
    | Leave Status
    |--------------------------------------------------------------------------
    */

    if (
        $leave->status
        ===
        'pending'
    ) {

        $leaveStatus =
            'Pending Approval';

        $leaveStatusClass =
            'bg-amber-100 text-amber-700';

        $statusBorder =
            'border-amber-200';

    }
    elseif (
        $leave->status
        ===
        'rejected'
    ) {

        $leaveStatus =
            'Rejected';

        $leaveStatusClass =
            'bg-red-100 text-red-700';

        $statusBorder =
            'border-red-200';

    }
    elseif (
        $leave->status
        ===
        'cancelled'
    ) {

        $leaveStatus =
            'Cancelled';

        $leaveStatusClass =
            'bg-slate-200 text-slate-600';

        $statusBorder =
            'border-slate-200';

    }
    elseif ($leave->actual_return_date) {

        $leaveStatus =
            $leave->returned_early
                ? 'Returned Early'
                : 'Completed';

        $leaveStatusClass =
            'bg-green-100 text-green-700';

        $statusBorder =
            'border-green-200';

    }
    elseif (
        $leave
            ->start_date
            ->copy()
            ->startOfDay()
            ->gt($today)
    ) {

        $leaveStatus =
            'Upcoming Leave';

        $leaveStatusClass =
            'bg-purple-100 text-purple-700';

        $statusBorder =
            'border-purple-200';

    }
    else {

        $leaveStatus =
            'Current Leave';

        $leaveStatusClass =
            'bg-blue-100 text-blue-700';

        $statusBorder =
            'border-blue-200';
    }


    /*
    |--------------------------------------------------------------------------
    | Return
    |--------------------------------------------------------------------------
    */

    $canRecordReturn =
        $leave->status
            ===
            'approved'
        &&
        !$leave->actual_return_date
        &&
        $leave
            ->start_date
            ->copy()
            ->startOfDay()
            ->lte($today);


    $isEarlyReturnPossible =
        $canRecordReturn
        &&
        $today->lt(
            $leave
                ->expected_return_date
                ->copy()
                ->startOfDay()
        );


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    $canEdit =
        in_array(
            $leave->status,
            [
                'pending',
                'approved',
            ]
        )
        &&
        !$leave->actual_return_date;


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    $canDelete =
        in_array(
            $leave->status,
            [
                'pending',
                'rejected',
                'cancelled',
            ]
        );


    /*
    |--------------------------------------------------------------------------
    | Homework Colours
    |--------------------------------------------------------------------------
    */

    if (
        $leave->homework_requirement
        ===
        'increase'
    ) {

        $homeworkBox =
            'border-violet-200 bg-violet-50';

        $homeworkText =
            'text-violet-700';

        $homeworkBadge =
            'bg-violet-100 text-violet-700';

    }
    elseif (
        $leave->homework_requirement
        ===
        'decrease'
    ) {

        $homeworkBox =
            'border-orange-200 bg-orange-50';

        $homeworkText =
            'text-orange-700';

        $homeworkBadge =
            'bg-orange-100 text-orange-700';

    }
    elseif (
        $leave->homework_requirement
        ===
        'none_required'
    ) {

        $homeworkBox =
            'border-slate-200 bg-slate-50';

        $homeworkText =
            'text-slate-600';

        $homeworkBadge =
            'bg-slate-200 text-slate-600';

    }
    else {

        $homeworkBox =
            'border-blue-200 bg-blue-50';

        $homeworkText =
            'text-blue-700';

        $homeworkBadge =
            'bg-blue-100 text-blue-700';
    }

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        BACK
    ========================================================== --}}

    <a
        href="{{ route(
            'admin.leave.index'
        ) }}"
        class="inline-flex
               items-center
               gap-2
               text-sm
               font-semibold
               text-blue-600
               transition
               hover:text-blue-800"
    >
        ← Back to Leave Management
    </a>



    {{-- =========================================================
        SUCCESS
    ========================================================== --}}

    @if (session('success'))

        <div
            class="rounded-xl
                   border
                   border-green-200
                   bg-green-50
                   px-5 py-4
                   text-sm
                   font-semibold
                   text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif



    {{-- =========================================================
        ERROR
    ========================================================== --}}

    @if (session('error'))

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm
                   font-semibold
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if ($errors->any())

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            <p
                class="font-semibold
                       text-red-700"
            >
                Please correct the following:
            </p>


            <ul
                class="mt-2
                       list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600"
            >

                @foreach (
                    $errors->all()
                    as $error
                )

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
        STUDENT HEADER
    ========================================================== --}}

    <section
        class="rounded-2xl
               border
               {{ $statusBorder }}
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="flex
                   flex-col
                   gap-5
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           tracking-wide
                           text-blue-600"
                >
                    Student Leave
                </p>


                <h1
                    class="mt-2
                           text-3xl
                           font-bold
                           text-slate-900"
                >
                    {{ $student?->first_name }}
                    {{ $student?->last_name }}
                </h1>


                <div
                    class="mt-2
                           flex
                           flex-wrap
                           items-center
                           gap-2
                           text-sm
                           text-slate-500"
                >

                    <span>
                        Student ID:
                        {{
                            $student?->external_id
                            ??
                            '—'
                        }}
                    </span>


                    @if (
                        $student
                            ?->studentStatus
                    )

                        <span
                            class="text-slate-300"
                        >
                            •
                        </span>


                        <span>
                            {{
                                $student
                                    ->studentStatus
                                    ->status_name
                            }}
                        </span>

                    @endif

                </div>

            </div>



            <div
                class="flex
                       flex-wrap
                       items-center
                       gap-3"
            >

                <span
                    class="inline-flex
                           rounded-full
                           px-4 py-2
                           text-sm
                           font-bold
                           {{ $leaveStatusClass }}"
                >
                    {{ $leaveStatus }}
                </span>


                @if (
                    $canEdit
                    &&
                    auth()
                        ->user()
                        ->hasPermission(
                            'leave.edit'
                        )
                )

                    <a
                        href="{{ route(
                            'admin.leave.edit',
                            $leave
                        ) }}"
                        class="inline-flex
                               h-10
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-blue-300
                               bg-white
                               px-4
                               text-xs
                               font-semibold
                               text-blue-700
                               hover:bg-blue-50"
                    >

                        {{
                            $leave->status
                            ===
                            'pending'
                                ? 'Edit Request'
                                : 'Edit / Extend'
                        }}

                    </a>

                @endif

            </div>

        </div>

    </section>



    {{-- =========================================================
        PENDING REVIEW
    ========================================================== --}}

    @if (
        $leave->status
        ===
        'pending'
    )

        <section
            class="rounded-2xl
                   border
                   border-amber-200
                   bg-amber-50
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       flex-col
                       gap-4
                       lg:flex-row
                       lg:items-start
                       lg:justify-between"
            >

                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-amber-700"
                    >
                        Parent Request
                    </p>


                    <h2
                        class="mt-1
                               text-xl
                               font-bold
                               text-slate-900"
                    >
                        Review Leave Request
                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               text-slate-600"
                    >
                        Review the leave information,
                        parent note and homework request
                        before approving or rejecting.
                    </p>

                </div>


                <span
                    class="inline-flex
                           self-start
                           rounded-full
                           bg-white
                           px-3 py-1.5
                           text-xs
                           font-bold
                           text-amber-700"
                >
                    Waiting for Review
                </span>

            </div>



            {{-- =================================================
                REQUEST DETAILS
            ================================================== --}}

            <div
                class="mt-5
                       grid
                       gap-4
                       md:grid-cols-2"
            >


                {{-- Parent Note --}}
                <div
                    class="rounded-xl
                           border
                           border-amber-200
                           bg-white
                           p-5"
                >

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-amber-600"
                    >
                        Parent Note
                    </p>


                    <p
                        class="mt-3
                               whitespace-pre-line
                               text-sm
                               leading-6
                               text-slate-700"
                    >
                        {{
                            $leave->notes
                            ?: 'No parent note provided.'
                        }}
                    </p>

                </div>



                {{-- Homework Request --}}
                <div
                    class="rounded-xl
                           border
                           {{ $homeworkBox }}
                           p-5"
                >

                    <div
                        class="flex
                               items-center
                               justify-between
                               gap-3"
                    >

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-500"
                        >
                            Homework Request
                        </p>


                        <span
                            class="rounded-full
                                   px-3 py-1
                                   text-xs
                                   font-bold
                                   {{ $homeworkBadge }}"
                        >
                            {{
                                $homeworkLabels[
                                    $leave
                                        ->homework_requirement
                                ]
                                ??
                                $leave
                                    ->homework_requirement
                            }}
                        </span>

                    </div>


                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               {{ $homeworkText }}"
                    >

                        @if (
                            $leave->homework_requirement
                            ===
                            'increase'
                        )

                            Parent requested additional homework.

                        @elseif (
                            $leave->homework_requirement
                            ===
                            'decrease'
                        )

                            Parent requested reduced homework.

                        @elseif (
                            $leave->homework_requirement
                            ===
                            'none_required'
                        )

                            No homework is required during this leave.

                        @else

                            Continue the normal homework amount.

                        @endif

                    </p>

                </div>

            </div>



            {{-- =================================================
                HOMEWORK INSTRUCTION
            ================================================== --}}

            <div
                class="mt-5
                       rounded-xl
                       border
                       border-blue-200
                       bg-white
                       p-5"
            >

                <label
                    for="review_note"
                    class="block
                           text-sm
                           font-bold
                           text-slate-800"
                >
                    Centre Homework Instructions
                </label>


                <p
                    class="mt-1
                           text-xs
                           text-slate-500"
                >
                    This message will be visible
                    to the parent after review.
                </p>


                <textarea
                    id="review_note"
                    rows="4"
                    maxlength="2000"
                    placeholder="Example: Complete Chapter 1 and Chapter 2."
                    class="mt-4
                           w-full
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           p-4
                           text-sm
                           text-slate-800
                           outline-none
                           transition
                           focus:border-blue-500
                           focus:ring-4
                           focus:ring-blue-100"
                >{{ old(
                    'review_note',
                    $leave->review_note
                ) }}</textarea>

            </div>



            {{-- =================================================
                ACTIONS
            ================================================== --}}

            <div
                class="mt-5
                       flex
                       flex-wrap
                       justify-end
                       gap-3"
            >

                @if (
                    $canDelete
                    &&
                    auth()
                        ->user()
                        ->hasPermission(
                            'leave.edit'
                        )
                )

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.leave.destroy',
                            $leave
                        ) }}"
                    >

                        @csrf
                        @method('DELETE')


                        <button
                            type="submit"
                            onclick="
                                return confirm(
                                    'Delete this leave request permanently?'
                                );
                            "
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-red-300
                                   bg-white
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-red-700
                                   hover:bg-red-50"
                        >
                            Delete
                        </button>

                    </form>

                @endif



                @if (
                    auth()
                        ->user()
                        ->hasPermission(
                            'leave.edit'
                        )
                )

                    {{-- Reject --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'admin.leave.reject',
                            $leave
                        ) }}"
                        onsubmit="
                            document.getElementById(
                                'reject_review_note'
                            ).value =
                            document.getElementById(
                                'review_note'
                            ).value;
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <input
                            type="hidden"
                            name="review_note"
                            id="reject_review_note"
                        >


                        <button
                            type="submit"
                            onclick="
                                return confirm(
                                    'Reject this leave request?'
                                );
                            "
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-red-300
                                   bg-white
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-red-700
                                   hover:bg-red-50"
                        >
                            Reject Request
                        </button>

                    </form>



                    {{-- Approve --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'admin.leave.approve',
                            $leave
                        ) }}"
                        onsubmit="
                            document.getElementById(
                                'approve_review_note'
                            ).value =
                            document.getElementById(
                                'review_note'
                            ).value;
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <input
                            type="hidden"
                            name="review_note"
                            id="approve_review_note"
                        >


                        <button
                            type="submit"
                            onclick="
                                return confirm(
                                    'Approve this leave request?'
                                );
                            "
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-green-600
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-green-700"
                        >
                            Approve Request
                        </button>

                    </form>

                @endif

            </div>

        </section>

    @endif



    {{-- =========================================================
        MAIN GRID
    ========================================================== --}}

    <div
        class="grid
               gap-6
               xl:grid-cols-[1.2fr_0.8fr]"
    >


        {{-- =====================================================
            LEFT COLUMN
        ====================================================== --}}

        <div class="space-y-6">


            {{-- =================================================
                LEAVE OVERVIEW
            ================================================== --}}

            <section
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-6
                       shadow-sm"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Leave Overview
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Main leave dates and duration.
                </p>


                <div
                    class="mt-5
                           grid
                           gap-4
                           sm:grid-cols-2"
                >


                    {{-- Start --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-4"
                    >

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-400"
                        >
                            Start Date
                        </p>


                        <p
                            class="mt-2
                                   text-base
                                   font-bold
                                   text-slate-900"
                        >
                            {{
                                $leave
                                    ->start_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}
                        </p>

                    </div>



                    {{-- Expected --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-4"
                    >

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-400"
                        >
                            Expected Return
                        </p>


                        <p
                            class="mt-2
                                   text-base
                                   font-bold
                                   text-slate-900"
                        >
                            {{
                                $leave
                                    ->expected_return_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}
                        </p>

                    </div>



                    {{-- Duration --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-4"
                    >

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-400"
                        >
                            Planned Duration
                        </p>


                        <p
                            class="mt-2
                                   text-base
                                   font-bold
                                   text-slate-900"
                        >
                            {{ $duration }}

                            {{
                                $duration === 1
                                    ? 'day'
                                    : 'days'
                            }}
                        </p>

                    </div>



                    {{-- Actual --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-4"
                    >

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-400"
                        >
                            Actual Return
                        </p>


                        <p
                            class="mt-2
                                   text-base
                                   font-bold
                                   text-slate-900"
                        >
                            {{
                                $leave
                                    ->actual_return_date
                                    ? $leave
                                        ->actual_return_date
                                        ->format(
                                            'd M Y'
                                        )
                                    : 'Not returned yet'
                            }}
                        </p>

                    </div>

                </div>

            </section>



            {{-- =================================================
                LEAVE REQUEST DETAILS
            ================================================== --}}

            <section
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-6
                       shadow-sm"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Leave Request Details
                </h2>


                <div
                    class="mt-5
                           grid
                           gap-4
                           md:grid-cols-2"
                >


                    {{-- Reason --}}
                    <div
                        class="rounded-xl
                               border
                               border-slate-200
                               bg-slate-50
                               p-5"
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
                                       rounded-lg
                                       bg-blue-100
                                       text-blue-700"
                            >
                                ?
                            </div>


                            <p
                                class="font-bold
                                       text-slate-800"
                            >
                                Leave Reason
                            </p>

                        </div>


                        <p
                            class="mt-4
                                   whitespace-pre-line
                                   text-sm
                                   leading-6
                                   text-slate-700"
                        >
                            {{
                                $leave->reason
                                ?: 'No reason provided.'
                            }}
                        </p>

                    </div>



                    {{-- Parent Note --}}
                    <div
                        class="rounded-xl
                               border
                               border-amber-200
                               bg-amber-50
                               p-5"
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
                                       rounded-lg
                                       bg-amber-100
                                       text-amber-700"
                            >
                                ✎
                            </div>


                            <div>

                                <p
                                    class="font-bold
                                           text-slate-800"
                                >
                                    Parent Note
                                </p>


                                <p
                                    class="text-xs
                                           text-slate-500"
                                >
                                    Note submitted with leave request
                                </p>

                            </div>

                        </div>


                        <p
                            class="mt-4
                                   whitespace-pre-line
                                   text-sm
                                   leading-6
                                   text-slate-700"
                        >
                            {{
                                $leave->notes
                                ?: 'No parent note provided.'
                            }}
                        </p>

                    </div>

                </div>

            </section>

        </div>



        {{-- =====================================================
            RIGHT COLUMN
        ====================================================== --}}

        <div class="space-y-6">


            {{-- =================================================
                ENROLLED CLASSES
            ================================================== --}}

            <section
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-6
                       shadow-sm"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Enrolled Classes
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >

                    @if (
                        $leave->status
                        ===
                        'approved'
                    )

                        Leave applies to all active classes.

                    @else

                        Current active classes.

                    @endif

                </p>



                <div
                    class="mt-5
                           space-y-3"
                >

                    @forelse (
                        $student?->enrolments
                        ??
                        collect()
                        as $enrolment
                    )

                        @php

                            $offering =
                                $enrolment
                                    ->sectionOffering;

                        @endphp


                        <div
                            class="rounded-xl
                                   border
                                   border-slate-200
                                   bg-slate-50
                                   p-4"
                        >

                            <div
                                class="flex
                                       items-start
                                       justify-between
                                       gap-4"
                            >

                                <div>

                                    <p
                                        class="font-bold
                                               text-slate-900"
                                    >
                                        {{
                                            $offering
                                                ?->section
                                                ?->section_name
                                            ??
                                            'Class'
                                        }}
                                    </p>


                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500"
                                    >
                                        {{
                                            $offering
                                                ?->day
                                                ?->day_name
                                            ??
                                            '—'
                                        }}


                                        @if ($offering)

                                            <span class="mx-1">
                                                ·
                                            </span>


                                            {{
                                                \Carbon\Carbon::parse(
                                                    $offering
                                                        ->start_time
                                                )->format(
                                                    'g:i A'
                                                )
                                            }}

                                            –

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $offering
                                                        ->end_time
                                                )->format(
                                                    'g:i A'
                                                )
                                            }}

                                        @endif

                                    </p>

                                </div>



                                @if (
                                    $leave->status
                                    ===
                                    'approved'
                                )

                                    <span
                                        class="shrink-0
                                               rounded-full
                                               bg-blue-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-blue-700"
                                    >
                                        Included
                                    </span>

                                @endif

                            </div>

                        </div>


                    @empty

                        <div
                            class="rounded-xl
                                   border
                                   border-dashed
                                   border-slate-300
                                   p-7
                                   text-center
                                   text-sm
                                   text-slate-500"
                        >
                            No active confirmed classes.
                        </div>

                    @endforelse

                </div>



                @if (
                    $leave->status
                    ===
                    'approved'
                )

                    <div
                        class="mt-5
                               rounded-xl
                               bg-blue-50
                               p-4"
                    >

                        <p
                            class="text-sm
                                   leading-6
                                   text-blue-700"
                        >
                            When the student returns,
                            leave ends for all included classes.
                        </p>

                    </div>

                @endif

            </section>



            {{-- =================================================
                HOMEWORK PLAN
            ================================================== --}}

            <section
                class="rounded-2xl
                       border
                       {{ $homeworkBox }}
                       p-6
                       shadow-sm"
            >

                <div
                    class="flex
                           items-start
                           justify-between
                           gap-4"
                >

                    <div>

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   {{ $homeworkText }}"
                        >
                            Homework Plan
                        </p>


                        <h2
                            class="mt-2
                                   text-xl
                                   font-bold
                                   text-slate-900"
                        >
                            Homework During Leave
                        </h2>

                    </div>


                    <span
                        class="inline-flex
                               shrink-0
                               rounded-full
                               px-4 py-2
                               text-sm
                               font-bold
                               {{ $homeworkBadge }}"
                    >
                        {{
                            $homeworkLabels[
                                $leave
                                    ->homework_requirement
                            ]
                            ??
                            $leave
                                ->homework_requirement
                        }}
                    </span>

                </div>



                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-white
                           bg-white
                           p-5"
                >

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Centre Homework Instructions
                    </p>


                    @if ($leave->review_note)

                        <p
                            class="mt-3
                                   whitespace-pre-line
                                   text-sm
                                   font-semibold
                                   leading-6
                                   text-slate-800"
                        >
                            {{ $leave->review_note }}
                        </p>


                    @elseif (
                        $leave->status
                        ===
                        'pending'
                    )

                        <p
                            class="mt-3
                                   text-sm
                                   leading-6
                                   text-slate-500"
                        >
                            Waiting for centre review.
                        </p>


                    @else

                        <p
                            class="mt-3
                                   text-sm
                                   leading-6
                                   text-slate-500"
                        >
                            No additional homework instructions provided.
                        </p>

                    @endif

                </div>

            </section>

        </div>

    </div>



    {{-- =========================================================
        RETURN STUDENT
    ========================================================== --}}

    @if ($canRecordReturn)

        <section
            class="rounded-2xl
                   border
                   border-green-200
                   bg-green-50
                   p-6"
        >

            <div
                class="flex
                       flex-col
                       gap-5
                       lg:flex-row
                       lg:items-end
                       lg:justify-between"
            >

                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-green-700"
                    >

                        {{
                            $isEarlyReturnPossible
                                ? 'Early Return'
                                : 'Student Return'
                        }}

                    </p>


                    <h2
                        class="mt-2
                               text-xl
                               font-bold
                               text-slate-900"
                    >

                        {{
                            $isEarlyReturnPossible
                                ? 'Return before expected leave end'
                                : 'Record student return'
                        }}

                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-600"
                    >
                        Expected return:

                        {{
                            $leave
                                ->expected_return_date
                                ->format(
                                    'd M Y'
                                )
                        }}
                    </p>

                </div>



                <form
                    method="POST"
                    action="{{ route(
                        'admin.leave.return',
                        $leave
                    ) }}"
                    class="flex
                           flex-wrap
                           items-end
                           gap-3"
                >

                    @csrf
                    @method('PATCH')


                    <div>

                        <label
                            for="actual_return_date"
                            class="mb-2
                                   block
                                   text-xs
                                   font-semibold
                                   text-slate-600"
                        >
                            Actual Return Date
                        </label>


                        <input
                            type="date"
                            name="actual_return_date"
                            id="actual_return_date"
                            value="{{
                                old(
                                    'actual_return_date',
                                    now()->format(
                                        'Y-m-d'
                                    )
                                )
                            }}"
                            min="{{
                                now()->format(
                                    'Y-m-d'
                                )
                            }}"
                            max="{{
                                $leave
                                    ->expected_return_date
                                    ->format(
                                        'Y-m-d'
                                    )
                            }}"
                            required
                            class="h-11
                                   rounded-xl
                                   border-slate-300
                                   text-sm"
                        >

                    </div>



                    <button
                        type="submit"
                        onclick="
                            return confirm(
                                'Record this student return?'
                            );
                        "
                        class="inline-flex
                               h-11
                               items-center
                               justify-center
                               rounded-xl
                               bg-green-600
                               px-6
                               text-sm
                               font-semibold
                               text-white
                               hover:bg-green-700"
                    >

                        {{
                            $isEarlyReturnPossible
                                ? 'Return Early'
                                : 'Record Return'
                        }}

                    </button>

                </form>

            </div>

        </section>


    @elseif (
        $leave->status
            ===
            'approved'
        &&
        !$leave->actual_return_date
        &&
        $leave
            ->start_date
            ->copy()
            ->startOfDay()
            ->gt($today)
    )

        <section
            class="rounded-2xl
                   border
                   border-purple-200
                   bg-purple-50
                   p-5"
        >

            <p
                class="font-semibold
                       text-purple-700"
            >
                Upcoming approved leave
            </p>


            <p
                class="mt-1
                       text-sm
                       text-purple-600"
            >
                Return options become available
                after the leave start date.
            </p>

        </section>

    @endif



    {{-- =========================================================
        COMPLETED
    ========================================================== --}}

    @if (
        $leave->status
            ===
            'approved'
        &&
        $leave->actual_return_date
    )

        <section
            class="rounded-2xl
                   border
                   border-green-200
                   bg-green-50
                   p-5"
        >

            <p
                class="font-bold
                       text-green-800"
            >
                {{
                    $leave->returned_early
                        ? 'Student Returned Early'
                        : 'Leave Completed'
                }}
            </p>


            <p
                class="mt-1
                       text-sm
                       text-green-700"
            >
                Actual return:

                {{
                    $leave
                        ->actual_return_date
                        ->format(
                            'd M Y'
                        )
                }}
            </p>

        </section>

    @endif



    {{-- =========================================================
        BOTTOM ACTIONS
    ========================================================== --}}

    <div
        class="flex
               flex-wrap
               justify-end
               gap-3"
    >

        <a
            href="{{ route(
                'admin.leave.index'
            ) }}"
            class="inline-flex
                   h-11
                   items-center
                   justify-center
                   rounded-xl
                   border
                   border-slate-300
                   bg-white
                   px-6
                   text-sm
                   font-semibold
                   text-slate-600
                   hover:bg-slate-50"
        >
            Back
        </a>



        @if (
            $canEdit
            &&
            auth()
                ->user()
                ->hasPermission(
                    'leave.edit'
                )
        )

            <a
                href="{{ route(
                    'admin.leave.edit',
                    $leave
                ) }}"
                class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       border
                       border-blue-300
                       bg-white
                       px-6
                       text-sm
                       font-semibold
                       text-blue-700
                       hover:bg-blue-50"
            >

                {{
                    $leave->status
                    ===
                    'pending'
                        ? 'Edit Request'
                        : 'Edit / Extend Leave'
                }}

            </a>

        @endif



        @if (
            $canDelete
            &&
            auth()
                ->user()
                ->hasPermission(
                    'leave.edit'
                )
        )

            <form
                method="POST"
                action="{{ route(
                    'admin.leave.destroy',
                    $leave
                ) }}"
            >

                @csrf
                @method('DELETE')


                <button
                    type="submit"
                    onclick="
                        return confirm(
                            'Delete this leave request permanently?'
                        );
                    "
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-red-300
                           bg-red-50
                           px-6
                           text-sm
                           font-semibold
                           text-red-700
                           hover:bg-red-100"
                >
                    Delete
                </button>

            </form>

        @endif

    </div>

</div>

@endsection
