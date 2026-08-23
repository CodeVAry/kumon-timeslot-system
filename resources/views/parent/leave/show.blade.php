@extends('layouts.parent')

@section('title', 'Leave Details')

@section('page-title', 'Leave Details')


@section('content')

@php

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


    $today =
        now()->startOfDay();


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
               text-sm
               font-semibold
               text-violet-700
               hover:text-violet-900"
    >
        ← Back to Leave Management
    </a>



    {{-- =========================================================
        MESSAGES
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


    @if ($errors->any())

        <div
            class="mt-6
                   rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            @foreach ($errors->all() as $error)

                <p class="text-sm text-red-700">
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
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="flex
                   flex-col
                   gap-5
                   sm:flex-row
                   sm:items-start
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
                    Leave Details
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
                    {{ $student->external_id ?? '—' }}
                </p>

            </div>


            <span
                class="inline-flex
                       self-start
                       rounded-full
                       px-4 py-2
                       text-sm
                       font-bold
                       {{
                           $statusClasses[
                               $leave->status
                           ]
                           ??
                           'bg-slate-100 text-slate-600'
                       }}"
            >
                {{
                    $statusLabels[
                        $leave->status
                    ]
                    ??
                    ucfirst(
                        $leave->status
                    )
                }}
            </span>

        </div>

    </section>



    {{-- =========================================================
        LEAVE DETAILS
    ========================================================== --}}

    <section
        class="mt-6
               rounded-[26px]
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="grid
                   gap-4
                   md:grid-cols-2"
        >


            {{-- Start --}}
            <div
                class="rounded-xl
                       bg-slate-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Start Date
                </p>


                <p
                    class="mt-2
                           font-bold
                           text-slate-900"
                >
                    {{
                        $leave
                            ->start_date
                            ->format('d M Y')
                    }}
                </p>

            </div>



            {{-- Expected --}}
            <div
                class="rounded-xl
                       bg-slate-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Expected Return Date
                </p>


                <p
                    class="mt-2
                           font-bold
                           text-slate-900"
                >
                    {{
                        $leave
                            ->expected_return_date
                            ->format('d M Y')
                    }}
                </p>

            </div>



            {{-- Actual --}}
            <div
                class="rounded-xl
                       bg-slate-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Actual Return Date
                </p>


                <p
                    class="mt-2
                           font-bold
                           text-slate-900"
                >
                    {{
                        $leave->actual_return_date
                            ? $leave
                                ->actual_return_date
                                ->format('d M Y')
                            : '—'
                    }}
                </p>

            </div>



            {{-- Homework --}}
            <div
                class="rounded-xl
                       bg-slate-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Homework Required
                </p>


                <p
                    class="mt-2
                           font-bold
                           text-slate-900"
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
                </p>

            </div>

        </div>



        {{-- Reason --}}
        <div
            class="mt-5
                   rounded-xl
                   border
                   border-slate-200
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Reason
            </p>


            <p
                class="mt-2
                       text-sm
                       text-slate-700"
            >
                {{ $leave->reason ?? '—' }}
            </p>

        </div>



        {{-- Parent Note --}}
        <div
            class="mt-5
                   rounded-xl
                   border
                   border-slate-200
                   p-5"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-slate-400"
            >
                Notes
            </p>


            <p
                class="mt-2
                       whitespace-pre-line
                       text-sm
                       leading-6
                       text-slate-700"
            >
                {{ $leave->notes ?? '—' }}
            </p>

        </div>



        {{-- =====================================================
            ADMIN REVIEW
        ====================================================== --}}

        @if (
            $leave->review_note
            ||
            $leave->reviewed_at
        )

            <div
                class="mt-5
                       rounded-xl
                       border
                       border-violet-100
                       bg-violet-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-violet-500"
                >
                    Centre Review
                </p>


                @if ($leave->review_note)

                    <p
                        class="mt-2
                               whitespace-pre-line
                               text-sm
                               leading-6
                               text-violet-800"
                    >
                        {{ $leave->review_note }}
                    </p>

                @else

                    <p
                        class="mt-2
                               text-sm
                               text-violet-700"
                    >
                        No additional review note.
                    </p>

                @endif

            </div>

        @endif



        {{-- =====================================================
            RECORD INFORMATION
        ====================================================== --}}

        <div
            class="mt-5
                   grid
                   gap-4
                   sm:grid-cols-2"
        >

            <div
                class="rounded-xl
                       border
                       border-slate-200
                       p-5"
            >

                <p
                    class="text-xs
                           uppercase
                           text-slate-400"
                >
                    Requested At
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
                        ?? '—'
                    }}
                </p>

            </div>


            <div
                class="rounded-xl
                       border
                       border-slate-200
                       p-5"
            >

                <p
                    class="text-xs
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
                        ?? 'Not reviewed yet'
                    }}
                </p>

            </div>

        </div>



        {{-- =====================================================
            PENDING ACTIONS
        ====================================================== --}}

        @if ($leave->status === 'pending')

            <div
                class="mt-7
                       rounded-2xl
                       border
                       border-amber-200
                       bg-amber-50
                       p-5"
            >

                <div
                    class="flex
                           flex-col
                           gap-4
                           sm:flex-row
                           sm:items-center
                           sm:justify-between"
                >

                    <div>

                        <p
                            class="font-bold
                                   text-amber-800"
                        >
                            Waiting for centre approval
                        </p>


                        <p
                            class="mt-1
                                   text-sm
                                   text-amber-700"
                        >
                            You can edit or cancel this request
                            before it is reviewed.
                        </p>

                    </div>


                    <div
                        class="flex
                               flex-wrap
                               gap-3"
                    >

                        {{-- Edit --}}
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


                        {{-- Cancel --}}
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
                                           bg-red-50
                                           px-5
                                           text-sm
                                           font-semibold
                                           text-red-700
                                           hover:bg-red-100"
                                >
                                    Cancel Request
                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            </div>

        @endif



        {{-- =====================================================
            APPROVED EARLY RETURN
        ====================================================== --}}

        @if ($canReturnEarly)

            <div
                class="mt-7
                       rounded-2xl
                       border
                       border-green-200
                       bg-green-50
                       p-5"
            >

                <div
                    class="flex
                           flex-col
                           gap-4
                           sm:flex-row
                           sm:items-center
                           sm:justify-between"
                >

                    <div>

                        <p
                            class="font-bold
                                   text-green-800"
                        >
                            Returning earlier than expected?
                        </p>


                        <p
                            class="mt-1
                                   text-sm
                                   text-green-700"
                        >
                            Record the student's return as today.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route(
                            'parent.leave.return-early',
                            $leave
                        ) }}"
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

                </div>

            </div>


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

            <div
                class="mt-7
                       rounded-xl
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
                    Early return will become available
                    after the leave start date.
                </p>

            </div>

        @endif



        {{-- =====================================================
            COMPLETED
        ====================================================== --}}

        @if (
            $leave->status === 'approved'
            &&
            $leave->actual_return_date
        )

            <div
                class="mt-7
                       rounded-xl
                       border
                       border-green-200
                       bg-green-50
                       p-5"
            >

                <p
                    class="font-bold
                           text-green-800"
                >
                    @if ($leave->returned_early)

                        Returned Early

                    @else

                        Leave Completed

                    @endif
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
                            ->format('d M Y')
                    }}
                </p>

            </div>

        @endif



        {{-- =====================================================
            DELETE
        ====================================================== --}}

        @if ($canDelete)

            <div
                class="mt-7
                       border-t
                       border-slate-100
                       pt-5"
            >

                <div
                    class="flex
                           flex-col
                           gap-4
                           sm:flex-row
                           sm:items-center
                           sm:justify-between"
                >

                    <div>

                        <p
                            class="font-semibold
                                   text-slate-800"
                        >
                            Remove this request
                        </p>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            This request is already
                            {{ $leave->status }} and can be removed
                            from your leave list.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route(
                            'parent.leave.destroy',
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
                                   hover:bg-red-100"
                        >
                            Delete
                        </button>

                    </form>

                </div>

            </div>

        @endif

    </section>

</div>

@endsection
