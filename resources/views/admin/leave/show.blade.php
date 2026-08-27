@extends('layouts.admin')

@section('title', 'Leave Details')

@section('page-title', 'Leave Details')


@php

    $breadcrumbs = [
        [
            'label' => 'Leave Management',
            'url' => route(
                'admin.leave.index'
            ),
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
            'None required',

        'same_as_normal' =>
            'Same as normal',

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
                $leave
                    ->expected_return_date
            );


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    $today =
        now()->startOfDay();


    if (
        $leave->status
        ===
        'pending'
    ) {

        $leaveStatus =
            'Pending Approval';

        $leaveStatusClass =
            'bg-amber-100 text-amber-700';

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

    }
    elseif ($leave->actual_return_date) {

        $leaveStatus =
            $leave->returned_early
                ? 'Returned Early'
                : 'Completed';

        $leaveStatusClass =
            'bg-green-100 text-green-700';

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

    }
    else {

        $leaveStatus =
            'Current Leave';

        $leaveStatusClass =
            'bg-blue-100 text-blue-700';
    }


    /*
    |--------------------------------------------------------------------------
    | Can Record Return
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


    /*
    |--------------------------------------------------------------------------
    | Early Return
    |--------------------------------------------------------------------------
    */

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
    | Can Edit
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
    | Can Delete
    |--------------------------------------------------------------------------
    |
    | Approved leave is preserved as history.
    |
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

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        BACK
    ========================================================== --}}

    <div>

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

    </div>



    {{-- =========================================================
        SUCCESS MESSAGE
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
        ERROR MESSAGE
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

                @foreach ($errors->all() as $error)

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
               border-slate-200
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
                           font-semibold
                           uppercase
                           tracking-wide
                           text-blue-600"
                >
                    Leave Details
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
                           gap-3
                           text-sm
                           text-slate-500"
                >

                    <span>
                        Student ID:

                        {{
                            $student
                                ?->external_id
                            ?? '—'
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



            <span
                class="inline-flex
                       self-start
                       rounded-full
                       px-4 py-2
                       text-sm
                       font-semibold
                       {{ $leaveStatusClass }}"
            >
                {{ $leaveStatus }}
            </span>

        </div>

    </section>



    {{-- =========================================================
        PENDING REQUEST REVIEW
    ========================================================== --}}

    @if ($leave->status === 'pending')

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
                        Parent Leave Request
                    </p>


                    <h2
                        class="mt-2
                               text-xl
                               font-bold
                               text-slate-900"
                    >
                        Review Request
                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               text-slate-600"
                    >
                        Review the requested leave information,
                        homework requirement and parent notes
                        before approving or rejecting.
                    </p>

                </div>


                <span
                    class="inline-flex
                           self-start
                           rounded-full
                           bg-amber-100
                           px-3 py-1.5
                           text-xs
                           font-bold
                           text-amber-700"
                >
                    Waiting for Review
                </span>

            </div>



            {{-- Homework request warning --}}
            @if (
                $leave->homework_requirement
                ===
                'increase'
            )

                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-violet-200
                           bg-violet-50
                           p-4"
                >

                    <p
                        class="font-semibold
                               text-violet-800"
                    >
                        Increased homework requested
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-violet-700"
                    >
                        The parent requested additional homework.
                        Add instructions in the Admin Review Note
                        if required.
                    </p>

                </div>

            @elseif (
                $leave->homework_requirement
                ===
                'decrease'
            )

                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-orange-200
                           bg-orange-50
                           p-4"
                >

                    <p
                        class="font-semibold
                               text-orange-800"
                    >
                        Reduced homework requested
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-orange-700"
                    >
                        The parent requested less homework
                        during this leave.
                    </p>

                </div>

            @else

                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-blue-200
                           bg-blue-50
                           p-4"
                >

                    <p
                        class="font-semibold
                               text-blue-800"
                    >
                        Normal homework requested
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-blue-700"
                    >
                        Homework should remain the same as normal.
                    </p>

                </div>

            @endif



            {{-- Review Note --}}
            <div class="mt-6">

                <label
                    for="review_note"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Homework Review Note
                </label>


                <textarea
                    id="review_note"
                    rows="4"
                    maxlength="2000"
                    placeholder="Add homework instructions, approval comments or rejection reason..."
                    class="w-full
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
                >{{ old('review_note') }}</textarea>


                <p
                    class="mt-2
                           text-xs
                           text-slate-500"
                >
                    Optional for approval.
                    Required when rejecting the request.
                </p>

            </div>



            {{-- Review Actions --}}
            <div
                class="mt-6
                       flex
                       flex-wrap
                       items-center
                       justify-end
                       gap-3"
            >


                {{-- Edit --}}
                @if (
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
                               px-5
                               text-sm
                               font-semibold
                               text-blue-700
                               transition
                               hover:bg-blue-50"
                    >
                        Edit Request
                    </a>

                @endif



                {{-- Delete --}}
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
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-red-700
                                   transition
                                   hover:bg-red-100"
                        >
                            Delete
                        </button>

                    </form>

                @endif



                {{-- Reject --}}
                @if (
                    auth()
                        ->user()
                        ->hasPermission(
                            'leave.edit'
                        )
                )

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
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-red-700
                                   transition
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
                                   shadow-sm
                                   transition
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
        MAIN INFORMATION
    ========================================================== --}}

    <div
        class="grid
               gap-6
               xl:grid-cols-[1fr_1fr]"
    >


        {{-- =====================================================
            LEAVE INFORMATION
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
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Leave Information
                </h2>


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
                               px-4
                               text-xs
                               font-semibold
                               text-blue-700
                               hover:bg-blue-50"
                    >

                        @if (
                            $leave->status
                            ===
                            'pending'
                        )

                            Edit Request

                        @else

                            Edit / Extend

                        @endif

                    </a>

                @endif

            </div>



            <dl
                class="mt-6
                       grid
                       gap-6
                       sm:grid-cols-2"
            >


                {{-- Start Date --}}
                <div>

                    <dt
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Start Date
                    </dt>


                    <dd
                        class="mt-2
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            $leave
                                ->start_date
                                ->format(
                                    'd M Y'
                                )
                        }}
                    </dd>

                </div>



                {{-- Expected Return --}}
                <div>

                    <dt
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Expected Return
                    </dt>


                    <dd
                        class="mt-2
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            $leave
                                ->expected_return_date
                                ->format(
                                    'd M Y'
                                )
                        }}
                    </dd>

                </div>



                {{-- Actual Return --}}
                <div>

                    <dt
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Actual Return
                    </dt>


                    <dd
                        class="mt-2
                               font-semibold
                               text-slate-800"
                    >

                        @if ($leave->actual_return_date)

                            {{
                                $leave
                                    ->actual_return_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}

                        @else

                            —

                        @endif

                    </dd>

                </div>



                {{-- Duration --}}
                <div>

                    <dt
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Planned Duration
                    </dt>


                    <dd
                        class="mt-2
                               font-semibold
                               text-slate-800"
                    >
                        {{ $duration }}

                        {{
                            $duration === 1
                                ? 'day'
                                : 'days'
                        }}
                    </dd>

                </div>



                {{-- Returned Early --}}
                <div>

                    <dt
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Returned Early
                    </dt>


                    <dd
                        class="mt-2"
                    >

                        @if ($leave->returned_early)

                            <span
                                class="inline-flex
                                       rounded-full
                                       bg-green-100
                                       px-3 py-1
                                       text-xs
                                       font-semibold
                                       text-green-700"
                            >
                                Yes
                            </span>

                        @else

                            <span
                                class="font-semibold
                                       text-slate-700"
                            >
                                No
                            </span>

                        @endif

                    </dd>

                </div>



                {{-- Homework --}}
                <div>

                    <dt
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-slate-400"
                    >
                        Homework Required
                    </dt>


                    <dd
                        class="mt-2
                               font-semibold
                               text-slate-800"
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
                    </dd>

                </div>

            </dl>



            {{-- Reason --}}
            <div
                class="mt-7
                       border-t
                       border-slate-100
                       pt-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           tracking-wide
                           text-slate-400"
                >
                    Reason
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-700"
                >
                    {{
                        $leave->reason
                        ?? '—'
                    }}
                </p>

            </div>



            {{-- Parent Notes --}}
            <div class="mt-5">

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           tracking-wide
                           text-slate-400"
                >
                    Parent / Leave Notes
                </p>


                <p
                    class="mt-2
                           whitespace-pre-line
                           text-sm
                           leading-6
                           text-slate-700"
                >
                    {{
                        $leave->notes
                        ?? '—'
                    }}
                </p>

            </div>



            {{-- Admin Review Note --}}
            @if ($leave->review_note)

                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-blue-100
                           bg-blue-50
                           p-4"
                >

                    <p
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-blue-500"
                    >
                        Homework Review Note
                    </p>


                    <p
                        class="mt-2
                               whitespace-pre-line
                               text-sm
                               leading-6
                               text-blue-800"
                    >
                        {{ $leave->review_note }}
                    </p>

                </div>

            @endif

        </section>



        {{-- =====================================================
            ENROLLED CLASSES
        ====================================================== --}}

        <section
            class="rounded-2xl
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

                        This leave applies automatically
                        to all active enrolled classes.

                    @else

                        These are the student's current
                        active enrolled classes.

                    @endif

                </p>

            </div>



            <div
                class="mt-5
                       space-y-3"
            >

                @forelse (
                    $student?->enrolments
                    ?? collect()
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
                                   flex-col
                                   gap-2
                                   sm:flex-row
                                   sm:items-center
                                   sm:justify-between"
                        >

                            <div>

                                <p
                                    class="font-bold
                                           text-slate-800"
                                >
                                    {{
                                        $offering
                                            ?->section
                                            ?->section_name
                                        ?? 'Class'
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
                                        ?? '—'
                                    }}

                                    @if ($offering)

                                        <span
                                            class="mx-1
                                                   text-slate-300"
                                        >
                                            •
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
                                    class="inline-flex
                                           self-start
                                           rounded-full
                                           bg-blue-100
                                           px-3 py-1
                                           text-xs
                                           font-semibold
                                           text-blue-700"
                                >
                                    Included in Leave
                                </span>

                            @else

                                <span
                                    class="inline-flex
                                           self-start
                                           rounded-full
                                           bg-slate-200
                                           px-3 py-1
                                           text-xs
                                           font-semibold
                                           text-slate-600"
                                >
                                    Active Class
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
                               p-8
                               text-center"
                    >

                        <p
                            class="text-sm
                                   text-slate-500"
                        >
                            No active confirmed classes.
                        </p>

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
                           border
                           border-blue-100
                           bg-blue-50
                           p-4"
                >

                    <p
                        class="text-sm
                               leading-6
                               text-blue-800"
                    >
                        When the student returns,
                        the leave ends for all of these
                        classes together.
                    </p>

                </div>

            @endif

        </section>

    </div>



    {{-- =========================================================
        RETURN STUDENT
    ========================================================== --}}

    @if ($canRecordReturn)

        <section
            class="rounded-2xl
                   border
                   border-green-200
                   bg-green-50/50
                   p-6
                   shadow-sm"
        >

            <div
                class="grid
                       gap-6
                       lg:grid-cols-[1fr_auto]
                       lg:items-end"
            >

                <div>

                    <p
                        class="text-xs
                               font-semibold
                               uppercase
                               tracking-wide
                               text-green-700"
                    >

                        @if ($isEarlyReturnPossible)

                            Early Return

                        @else

                            Student Return

                        @endif

                    </p>


                    <h2
                        class="mt-2
                               text-xl
                               font-bold
                               text-slate-900"
                    >

                        @if ($isEarlyReturnPossible)

                            Return before expected leave end

                        @else

                            Record student return

                        @endif

                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               text-slate-600"
                    >
                        Expected return date:

                        <span
                            class="font-semibold
                                   text-slate-800"
                        >
                            {{
                                $leave
                                    ->expected_return_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}
                        </span>
                    </p>

                </div>



                <form
                    method="POST"
                    action="{{ route(
                        'admin.leave.return',
                        $leave
                    ) }}"
                    class="flex
                           flex-col
                           gap-3
                           sm:flex-row
                           sm:items-end"
                >

                    @csrf
                    @method('PATCH')


                    <div>

                        <label
                            for="actual_return_date"
                            class="mb-2
                                   block
                                   text-sm
                                   font-semibold
                                   text-slate-700"
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
                                $leave
                                    ->start_date
                                    ->format(
                                        'Y-m-d'
                                    )
                            }}"
                            max="{{
                                now()->format(
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
                               min-w-[160px]
                               items-center
                               justify-center
                               rounded-xl
                               bg-green-600
                               px-6
                               text-sm
                               font-semibold
                               text-white
                               shadow-sm
                               transition
                               hover:bg-green-700"
                    >

                        @if ($isEarlyReturnPossible)

                            Return Early

                        @else

                            Record Return

                        @endif

                    </button>

                </form>

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
            class="rounded-2xl
                   border
                   border-purple-200
                   bg-purple-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-purple-700"
            >
                This leave has not started yet.
            </p>


            <p
                class="mt-1
                       text-sm
                       text-purple-600"
            >
                The return action will become available
                after the leave start date.
            </p>

        </section>

    @endif



    {{-- =========================================================
        COMPLETED SUMMARY
    ========================================================== --}}

    @if (
        $leave->status === 'approved'
        &&
        $leave->actual_return_date
    )

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
                       gap-3
                       sm:flex-row
                       sm:items-center
                       sm:justify-between"
            >

                <div>

                    <p
                        class="font-bold
                               text-green-800"
                    >

                        @if ($leave->returned_early)

                            Student Returned Early

                        @else

                            Student Returned

                        @endif

                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-green-700"
                    >
                        Actual return date:

                        {{
                            $leave
                                ->actual_return_date
                                ->format(
                                    'd M Y'
                                )
                        }}
                    </p>

                </div>


                <span
                    class="inline-flex
                           self-start
                           rounded-full
                           bg-white
                           px-4 py-2
                           text-sm
                           font-semibold
                           text-green-700"
                >

                    @if ($leave->returned_early)

                        Early Return

                    @else

                        Completed

                    @endif

                </span>

            </div>

        </section>

    @endif



    {{-- =========================================================
        RECORD INFORMATION
    ========================================================== --}}

    <section
        class="rounded-2xl
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
            Record Information
        </h2>


        <div
            class="mt-5
                   grid
                   gap-5
                   sm:grid-cols-2
                   xl:grid-cols-4"
        >


            {{-- Requested By --}}
            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Requested By
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-slate-800"
                >

                    @if (
                        $leave
                            ->requestedByGuardian
                    )

                        {{
                            $leave
                                ->requestedByGuardian
                                ->first_name
                        }}

                        {{
                            $leave
                                ->requestedByGuardian
                                ->last_name
                        }}

                    @elseif ($leave->createdBy)

                        {{
                            $leave
                                ->createdBy
                                ->name
                            ??
                            $leave
                                ->createdBy
                                ->email
                        }}

                    @else

                        —

                    @endif

                </p>

            </div>



            {{-- Reviewed By --}}
            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Reviewed By
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-slate-800"
                >
                    {{
                        $leave
                            ->reviewedBy
                            ?->name
                        ??
                        $leave
                            ->reviewedBy
                            ?->email
                        ??
                        '—'
                    }}
                </p>

            </div>



            {{-- Reviewed At --}}
            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Reviewed At
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-slate-800"
                >
                    {{
                        $leave
                            ->reviewed_at
                            ?->format(
                                'd M Y, g:i A'
                            )
                        ??
                        '—'
                    }}
                </p>

            </div>



            {{-- Created At --}}
            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Created At
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-slate-800"
                >
                    {{
                        $leave
                            ->created_at
                            ?->format(
                                'd M Y, g:i A'
                            )
                        ??
                        '—'
                    }}
                </p>

            </div>

        </div>

    </section>



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
                   transition
                   hover:bg-slate-50"
        >
            Back
        </a>



        {{-- Edit --}}
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
                       transition
                       hover:bg-blue-50"
            >

                @if (
                    $leave->status
                    ===
                    'pending'
                )

                    Edit Request

                @else

                    Edit / Extend Leave

                @endif

            </a>

        @endif



        {{-- Delete --}}
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
                           transition
                           hover:bg-red-100"
                >
                    Delete
                </button>

            </form>

        @endif

    </div>

</div>

@endsection
