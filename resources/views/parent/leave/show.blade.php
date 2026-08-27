@extends('layouts.parent')

@section('title', 'Leave Details')

@section('page-title', 'Leave Details')


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Homework Labels
    |--------------------------------------------------------------------------
    */

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
    | Status Labels
    |--------------------------------------------------------------------------
    */

    $statusLabels = [
        'pending' =>
            'Pending Approval',

        'approved' =>
            'Approved',

        'rejected' =>
            'Rejected',

        'cancelled' =>
            'Cancelled',
    ];


    /*
    |--------------------------------------------------------------------------
    | Status Colours
    |--------------------------------------------------------------------------
    */

    $statusClasses = [
        'pending' =>
            'bg-amber-100 text-amber-700',

        'approved' =>
            'bg-green-100 text-green-700',

        'rejected' =>
            'bg-red-100 text-red-700',

        'cancelled' =>
            'bg-slate-200 text-slate-600',
    ];


    /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

    $today =
        now()->startOfDay();


    $duration =
        $leave
            ->start_date
            ->diffInDays(
                $leave
                    ->expected_return_date
            );


    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    $canEdit =
        $leave->status
        ===
        'pending';


    $canCancel =
        $leave->status
        ===
        'pending';


    $canDelete =
        in_array(
            $leave->status,
            [
                'rejected',
                'cancelled',
            ]
        );


    $canReturnEarly =
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
            ->lte($today)
        &&
        $today->lt(
            $leave
                ->expected_return_date
                ->copy()
                ->startOfDay()
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

        $homeworkBadge =
            'bg-violet-100 text-violet-700';

        $homeworkText =
            'text-violet-700';

    }
    elseif (
        $leave->homework_requirement
        ===
        'decrease'
    ) {

        $homeworkBox =
            'border-orange-200 bg-orange-50';

        $homeworkBadge =
            'bg-orange-100 text-orange-700';

        $homeworkText =
            'text-orange-700';

    }
    elseif (
        $leave->homework_requirement
        ===
        'none_required'
    ) {

        $homeworkBox =
            'border-slate-200 bg-slate-50';

        $homeworkBadge =
            'bg-slate-200 text-slate-600';

        $homeworkText =
            'text-slate-600';

    }
    else {

        $homeworkBox =
            'border-blue-200 bg-blue-50';

        $homeworkBadge =
            'bg-blue-100 text-blue-700';

        $homeworkText =
            'text-blue-700';
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Display Status
    |--------------------------------------------------------------------------
    */

    if (
        $leave->status
        ===
        'pending'
    ) {

        $displayStatus =
            'Pending Approval';

        $displayStatusClass =
            'bg-amber-100 text-amber-700';

        $headerBorder =
            'border-amber-200';

    }
    elseif (
        $leave->status
        ===
        'rejected'
    ) {

        $displayStatus =
            'Rejected';

        $displayStatusClass =
            'bg-red-100 text-red-700';

        $headerBorder =
            'border-red-200';

    }
    elseif (
        $leave->status
        ===
        'cancelled'
    ) {

        $displayStatus =
            'Cancelled';

        $displayStatusClass =
            'bg-slate-200 text-slate-600';

        $headerBorder =
            'border-slate-200';

    }
    elseif ($leave->actual_return_date) {

        $displayStatus =
            $leave->returned_early
                ? 'Returned Early'
                : 'Completed';

        $displayStatusClass =
            'bg-green-100 text-green-700';

        $headerBorder =
            'border-green-200';

    }
    elseif (
        $leave
            ->start_date
            ->copy()
            ->startOfDay()
            ->gt($today)
    ) {

        $displayStatus =
            'Upcoming Leave';

        $displayStatusClass =
            'bg-purple-100 text-purple-700';

        $headerBorder =
            'border-purple-200';

    }
    else {

        $displayStatus =
            'Current Leave';

        $displayStatusClass =
            'bg-blue-100 text-blue-700';

        $headerBorder =
            'border-blue-200';
    }

@endphp


<div
    class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8"
>


    {{-- =========================================================
        BACK
    ========================================================== --}}

    <a
        href="{{ route(
            'parent.leave.index'
        ) }}"
        class="inline-flex
               items-center
               gap-2
               text-sm
               font-semibold
               text-violet-700
               transition
               hover:text-violet-900"
    >
        ← Back to Leave Management
    </a>



    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}

    @if (session('success'))

        <div
            class="mt-6
                   rounded-xl
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
        ERROR MESSAGE
    ========================================================== --}}

    @if (session('error'))

        <div
            class="mt-6
                   rounded-xl
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
            class="mt-6
                   rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            @foreach (
                $errors->all()
                as $error
            )

                <p
                    class="text-sm
                           text-red-700"
                >
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif



    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <section
        class="mt-6
               rounded-[26px]
               border
               {{ $headerBorder }}
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="flex
                   flex-col
                   gap-5
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div>

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           tracking-wide
                           text-violet-600"
                >
                    Student Leave
                </p>


                <h1
                    class="mt-2
                           text-3xl
                           font-bold
                           text-slate-900"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </h1>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Student ID:
                    {{
                        $student->external_id
                        ??
                        '—'
                    }}
                </p>

            </div>



            <span
                class="inline-flex
                       self-start
                       rounded-full
                       px-4 py-2
                       text-sm
                       font-bold
                       {{ $displayStatusClass }}"
            >
                {{ $displayStatus }}
            </span>

        </div>

    </section>



    {{-- =========================================================
        MAIN GRID
    ========================================================== --}}

    <div
        class="mt-6
               grid
               gap-6
               xl:grid-cols-[1.15fr_0.85fr]"
    >


        {{-- =====================================================
            LEFT COLUMN
        ====================================================== --}}

        <div class="space-y-6">


            {{-- =================================================
                LEAVE OVERVIEW
            ================================================== --}}

            <section
                class="rounded-[26px]
                       border
                       border-slate-200
                       bg-white
                       p-6
                       shadow-sm"
            >

                <div>

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
                        Important dates for this leave.
                    </p>

                </div>



                <div
                    class="mt-5
                           grid
                           gap-4
                           sm:grid-cols-2"
                >


                    {{-- Start Date --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-5"
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



                    {{-- Expected Return --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-5"
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
                               p-5"
                    >

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-400"
                        >
                            Duration
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



                    {{-- Actual Return --}}
                    <div
                        class="rounded-xl
                               bg-slate-50
                               p-5"
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
                                $leave->actual_return_date
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
                LEAVE REQUEST
            ================================================== --}}

            <section
                class="rounded-[26px]
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
                    Leave Request
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Information submitted with the request.
                </p>



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
                                       shrink-0
                                       items-center
                                       justify-center
                                       rounded-lg
                                       bg-blue-100
                                       font-bold
                                       text-blue-700"
                            >
                                ?
                            </div>


                            <div>

                                <p
                                    class="font-bold
                                           text-slate-800"
                                >
                                    Leave Reason
                                </p>


                                <p
                                    class="text-xs
                                           text-slate-500"
                                >
                                    Reason for the leave
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
                                $leave->reason
                                ?: 'No reason provided.'
                            }}
                        </p>

                    </div>



                    {{-- Your Note --}}
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
                                       shrink-0
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
                                    Your Note
                                </p>


                                <p
                                    class="text-xs
                                           text-slate-500"
                                >
                                    Note you submitted
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
                                ?: 'No note was provided.'
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
                HOMEWORK PLAN
            ================================================== --}}

            <section
                class="rounded-[26px]
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



                {{-- Homework Request Meaning --}}
                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-white
                           bg-white/70
                           p-4"
                >

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Requested Homework
                    </p>


                    <p
                        class="mt-2
                               text-sm
                               leading-6
                               {{ $homeworkText }}"
                    >

                        @if (
                            $leave->homework_requirement
                            ===
                            'increase'
                        )

                            Additional homework was requested
                            for this leave period.

                        @elseif (
                            $leave->homework_requirement
                            ===
                            'decrease'
                        )

                            Reduced homework was requested
                            for this leave period.

                        @elseif (
                            $leave->homework_requirement
                            ===
                            'none_required'
                        )

                            No homework is required
                            during this leave.

                        @else

                            Homework will continue
                            at the normal amount.

                        @endif

                    </p>

                </div>



                {{-- Centre Homework Instructions --}}
                <div
                    class="mt-4
                           rounded-xl
                           border
                           border-violet-100
                           bg-white
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
                                   shrink-0
                                   items-center
                                   justify-center
                                   rounded-lg
                                   bg-violet-100
                                   text-violet-700"
                        >
                            ✓
                        </div>


                        <div>

                            <p
                                class="font-bold
                                       text-slate-800"
                            >
                                Centre Homework Instructions
                            </p>


                            <p
                                class="text-xs
                                       text-slate-500"
                            >
                                Instructions provided by the centre
                            </p>

                        </div>

                    </div>



                    @if ($leave->review_note)

                        <p
                            class="mt-4
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

                        <div
                            class="mt-4
                                   rounded-lg
                                   bg-amber-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-sm
                                       leading-6
                                       text-amber-700"
                            >
                                The centre has not reviewed
                                this request yet.
                            </p>

                        </div>


                    @else

                        <div
                            class="mt-4
                                   rounded-lg
                                   bg-slate-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-sm
                                       leading-6
                                       text-slate-500"
                            >
                                No additional homework
                                instructions were provided.
                            </p>

                        </div>

                    @endif

                </div>

            </section>



            {{-- =================================================
                REQUEST STATUS / ACTIONS
            ================================================== --}}

            @if ($leave->status === 'pending')

                <section
                    class="rounded-[26px]
                           border
                           border-amber-200
                           bg-amber-50
                           p-6"
                >

                    <div
                        class="flex
                               items-center
                               gap-3"
                    >

                        <div
                            class="flex
                                   h-10 w-10
                                   shrink-0
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-amber-100
                                   font-bold
                                   text-amber-700"
                        >
                            !
                        </div>


                        <div>

                            <p
                                class="font-bold
                                       text-amber-800"
                            >
                                Waiting for Centre Approval
                            </p>


                            <p
                                class="mt-1
                                       text-sm
                                       text-amber-700"
                            >
                                The request has not been reviewed yet.
                            </p>

                        </div>

                    </div>


                    <p
                        class="mt-4
                               text-sm
                               leading-6
                               text-amber-700"
                    >
                        You can edit or cancel the request
                        until the centre reviews it.
                    </p>



                    <div
                        class="mt-5
                               flex
                               flex-wrap
                               gap-3"
                    >

                        @if ($canEdit)

                            <a
                                href="{{ route(
                                    'parent.leave.edit',
                                    $leave
                                ) }}"
                                class="inline-flex
                                       h-11
                                       items-center
                                       justify-center
                                       rounded-xl
                                       border
                                       border-violet-300
                                       bg-white
                                       px-5
                                       text-sm
                                       font-semibold
                                       text-violet-700
                                       hover:bg-violet-50"
                            >
                                Edit Request
                            </a>

                        @endif



                        @if ($canCancel)

                            <form
                                method="POST"
                                action="{{ route(
                                    'parent.leave.cancel',
                                    $leave
                                ) }}"
                            >

                                @csrf
                                @method('PATCH')


                                <button
                                    type="submit"
                                    onclick="
                                        return confirm(
                                            'Cancel this leave request?'
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
                                    Cancel Request
                                </button>

                            </form>

                        @endif

                    </div>

                </section>


            @elseif (
                $leave->status === 'approved'
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
                    class="rounded-[26px]
                           border
                           border-purple-200
                           bg-purple-50
                           p-6"
                >

                    <div
                        class="flex
                               items-center
                               gap-3"
                    >

                        <div
                            class="flex
                                   h-10 w-10
                                   shrink-0
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-purple-100
                                   text-purple-700"
                        >
                            ✓
                        </div>


                        <div>

                            <p
                                class="font-bold
                                       text-purple-800"
                            >
                                Leave Approved
                            </p>


                            <p
                                class="mt-1
                                       text-sm
                                       text-purple-700"
                            >
                                Upcoming leave
                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-4
                               rounded-xl
                               bg-white/70
                               p-4"
                    >

                        <p
                            class="text-xs
                                   font-semibold
                                   uppercase
                                   tracking-wide
                                   text-purple-500"
                        >
                            Leave Starts
                        </p>


                        <p
                            class="mt-1
                                   font-bold
                                   text-purple-800"
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


                    <p
                        class="mt-4
                               text-sm
                               leading-6
                               text-purple-700"
                    >
                        Early return becomes available
                        once the leave has started.
                    </p>

                </section>


            @elseif (
                $leave->status === 'approved'
                &&
                !$leave->actual_return_date
            )

                <section
                    class="rounded-[26px]
                           border
                           border-blue-200
                           bg-blue-50
                           p-6"
                >

                    <div
                        class="flex
                               items-center
                               gap-3"
                    >

                        <div
                            class="flex
                                   h-10 w-10
                                   shrink-0
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-100
                                   text-blue-700"
                        >
                            ✓
                        </div>


                        <div>

                            <p
                                class="font-bold
                                       text-blue-800"
                            >
                                Current Leave
                            </p>


                            <p
                                class="mt-1
                                       text-sm
                                       text-blue-700"
                            >
                                The approved leave is currently active.
                            </p>

                        </div>

                    </div>

                </section>

            @endif



            {{-- =================================================
                EARLY RETURN
            ================================================== --}}

            @if ($canReturnEarly)

                <section
                    class="rounded-[26px]
                           border
                           border-green-200
                           bg-green-50
                           p-6"
                >

                    <h2
                        class="text-lg
                               font-bold
                               text-green-800"
                    >
                        Returning Earlier?
                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               leading-6
                               text-green-700"
                    >
                        Expected return:
                        <strong>
                            {{
                                $leave
                                    ->expected_return_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}
                        </strong>
                    </p>


                    <p
                        class="mt-2
                               text-sm
                               leading-6
                               text-green-700"
                    >
                        If the student has already returned,
                        record the return now.
                    </p>



                    <form
                        method="POST"
                        action="{{ route(
                            'parent.leave.return-early',
                            $leave
                        ) }}"
                        class="mt-5"
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            onclick="
                                return confirm(
                                    'Record the student return as today?'
                                );
                            "
                            class="inline-flex
                                   h-11
                                   w-full
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
                            Return Early
                        </button>

                    </form>

                </section>

            @endif



            {{-- =================================================
                COMPLETED
            ================================================== --}}

            @if (
                $leave->status === 'approved'
                &&
                $leave->actual_return_date
            )

                <section
                    class="rounded-[26px]
                           border
                           border-green-200
                           bg-green-50
                           p-6"
                >

                    <div
                        class="flex
                               items-center
                               gap-3"
                    >

                        <div
                            class="flex
                                   h-10 w-10
                                   shrink-0
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-green-100
                                   text-green-700"
                        >
                            ✓
                        </div>


                        <div>

                            <p
                                class="font-bold
                                       text-green-800"
                            >
                                {{
                                    $leave->returned_early
                                        ? 'Returned Early'
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

                        </div>

                    </div>

                </section>

            @endif



            {{-- =================================================
                REJECTED
            ================================================== --}}

            @if (
                $leave->status
                ===
                'rejected'
            )

                <section
                    class="rounded-[26px]
                           border
                           border-red-200
                           bg-red-50
                           p-6"
                >

                    <p
                        class="font-bold
                               text-red-800"
                    >
                        Request Rejected
                    </p>


                    @if ($leave->review_note)

                        <div
                            class="mt-4
                                   rounded-xl
                                   bg-white
                                   p-4"
                        >

                            <p
                                class="text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-red-500"
                            >
                                Centre Note
                            </p>


                            <p
                                class="mt-2
                                       whitespace-pre-line
                                       text-sm
                                       leading-6
                                       text-red-700"
                            >
                                {{ $leave->review_note }}
                            </p>

                        </div>

                    @endif

                </section>

            @endif



            {{-- =================================================
                DELETE
            ================================================== --}}

            @if ($canDelete)

                <section
                    class="rounded-[26px]
                           border
                           border-slate-200
                           bg-white
                           p-6
                           shadow-sm"
                >

                    <h2
                        class="text-lg
                               font-bold
                               text-slate-900"
                    >
                        Remove Request
                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               leading-6
                               text-slate-500"
                    >
                        This request is
                        <strong>
                            {{ $leave->status }}
                        </strong>
                        and can be removed from your list.
                    </p>



                    <form
                        method="POST"
                        action="{{ route(
                            'parent.leave.destroy',
                            $leave
                        ) }}"
                        class="mt-5"
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
                                   w-full
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
                                   hover:bg-red-100"
                        >
                            Delete Request
                        </button>

                    </form>

                </section>

            @endif

        </div>

    </div>

</div>

@endsection
