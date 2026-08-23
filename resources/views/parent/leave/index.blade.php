@extends('layouts.parent')

@section('title', 'Leave Management')

@section('page-title', 'Leave Management')


@section('content')

@php

    $today =
        now()->startOfDay();


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


    $requestStatusLabels = [

        'pending' =>
            'Pending Approval',

        'rejected' =>
            'Rejected',

        'cancelled' =>
            'Cancelled',

    ];


    $requestStatusClasses = [

        'pending' =>
            'bg-amber-100 text-amber-700',

        'rejected' =>
            'bg-red-100 text-red-700',

        'cancelled' =>
            'bg-slate-200 text-slate-600',

    ];

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
        HEADER
    ========================================================== --}}

    <div
        class="flex
               flex-col
               gap-4
               lg:flex-row
               lg:items-end
               lg:justify-between"
    >

        <div>

            <p
                class="text-xs
                       font-bold
                       uppercase
                       tracking-[0.15em]
                       text-violet-600"
            >
                Parent Portal
            </p>


            <h1
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                Leave Management
            </h1>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                View leave status and leave requests for

                <span
                    class="font-semibold
                           text-slate-700"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </span>.
            </p>

        </div>


        <a
            href="{{ route(
                'parent.leave.create'
            ) }}"
            class="inline-flex
                   h-11
                   items-center
                   justify-center
                   rounded-xl
                   bg-violet-600
                   px-5
                   text-sm
                   font-bold
                   text-white
                   shadow-sm
                   transition
                   hover:bg-violet-700"
        >
            + Create Leave Request
        </a>

    </div>



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



    {{-- =========================================================
        REQUEST SUMMARY
    ========================================================== --}}

    <div
        class="mt-7
               grid
               gap-4
               sm:grid-cols-2
               xl:grid-cols-4"
    >

        {{-- Pending --}}
        <div
            class="rounded-2xl
                   border
                   border-amber-100
                   bg-amber-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Pending
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-amber-600"
            >
                {{ $pendingCount }}
            </p>

        </div>



        {{-- Approved --}}
        <div
            class="rounded-2xl
                   border
                   border-green-100
                   bg-green-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Approved
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-green-600"
            >
                {{ $approvedCount }}
            </p>

        </div>



        {{-- Rejected --}}
        <div
            class="rounded-2xl
                   border
                   border-red-100
                   bg-red-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Rejected
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-red-600"
            >
                {{ $rejectedCount }}
            </p>

        </div>



        {{-- Cancelled --}}
        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Cancelled
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-700"
            >
                {{ $cancelledCount }}
            </p>

        </div>

    </div>



    {{-- =========================================================
        OPERATIONAL LEAVE STATUS SUMMARY
    ========================================================== --}}

    <div
        class="mt-6
               grid
               gap-4
               sm:grid-cols-2
               xl:grid-cols-4"
    >

        {{-- Current --}}
        <div
            class="rounded-2xl
                   border
                   border-blue-100
                   bg-blue-50
                   p-4"
        >

            <p
                class="text-xs
                       font-bold
                       uppercase
                       tracking-wide
                       text-blue-600"
            >
                Current Leave
            </p>


            <p
                class="mt-2
                       text-2xl
                       font-bold
                       text-blue-700"
            >
                {{ $currentLeaveCount }}
            </p>

        </div>



        {{-- Upcoming --}}
        <div
            class="rounded-2xl
                   border
                   border-purple-100
                   bg-purple-50
                   p-4"
        >

            <p
                class="text-xs
                       font-bold
                       uppercase
                       tracking-wide
                       text-purple-600"
            >
                Upcoming
            </p>


            <p
                class="mt-2
                       text-2xl
                       font-bold
                       text-purple-700"
            >
                {{ $upcomingLeaveCount }}
            </p>

        </div>



        {{-- Returned Early --}}
        <div
            class="rounded-2xl
                   border
                   border-cyan-100
                   bg-cyan-50
                   p-4"
        >

            <p
                class="text-xs
                       font-bold
                       uppercase
                       tracking-wide
                       text-cyan-700"
            >
                Returned Early
            </p>


            <p
                class="mt-2
                       text-2xl
                       font-bold
                       text-cyan-700"
            >
                {{ $returnedEarlyCount }}
            </p>

        </div>



        {{-- Completed --}}
        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-slate-50
                   p-4"
        >

            <p
                class="text-xs
                       font-bold
                       uppercase
                       tracking-wide
                       text-slate-600"
            >
                Completed
            </p>


            <p
                class="mt-2
                       text-2xl
                       font-bold
                       text-slate-700"
            >
                {{ $completedLeaveCount }}
            </p>

        </div>

    </div>



    {{-- =========================================================
        LEAVE STATUS TABLE
    ========================================================== --}}

    <section
        class="mt-6
               overflow-hidden
               rounded-[26px]
               border
               border-slate-200
               bg-white
               shadow-sm"
    >

        <div
            class="border-b
                   border-slate-100
                   px-6 py-5"
        >

            <h2
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                Leave Status
            </h2>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Track approved leave as current,
                upcoming, completed or returned early.
            </p>

        </div>



        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Leave Period
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Homework
                        </th>


                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Leave Status
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Actual Return
                        </th>


                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Action
                        </th>

                    </tr>

                </thead>



                <tbody
                    class="divide-y
                           divide-slate-100"
                >

                    @forelse (
                        $approvedLeaves
                        as $leave
                    )

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Actual Leave State
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $leave->actual_return_date
                                &&
                                $leave->returned_early
                            ) {

                                $leaveState =
                                    'Returned Early';

                                $leaveStateClass =
                                    'bg-cyan-100 text-cyan-700';

                            }
                            elseif (
                                $leave->actual_return_date
                            ) {

                                $leaveState =
                                    'Completed';

                                $leaveStateClass =
                                    'bg-slate-200 text-slate-700';

                            }
                            elseif (
                                $leave
                                    ->start_date
                                    ->copy()
                                    ->startOfDay()
                                    ->gt($today)
                            ) {

                                $leaveState =
                                    'Upcoming';

                                $leaveStateClass =
                                    'bg-purple-100 text-purple-700';

                            }
                            elseif (
                                $leave
                                    ->expected_return_date
                                    ->copy()
                                    ->startOfDay()
                                    ->lt($today)
                            ) {

                                $leaveState =
                                    'Completed';

                                $leaveStateClass =
                                    'bg-slate-200 text-slate-700';

                            }
                            else {

                                $leaveState =
                                    'Current Leave';

                                $leaveStateClass =
                                    'bg-blue-100 text-blue-700';

                            }


                            $canReturnEarly =
                                !$leave->actual_return_date
                                &&
                                $leaveState
                                    ===
                                    'Current Leave'
                                &&
                                $today->lt(
                                    $leave
                                        ->expected_return_date
                                        ->copy()
                                        ->startOfDay()
                                );

                        @endphp


                        <tr
                            class="transition
                                   hover:bg-violet-50/30"
                        >


                            {{-- Leave Period --}}
                            <td
                                class="px-6 py-5"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-900"
                                >
                                    {{
                                        $leave
                                            ->start_date
                                            ->format(
                                                'd M Y'
                                            )
                                    }}

                                    →

                                    {{
                                        $leave
                                            ->expected_return_date
                                            ->format(
                                                'd M Y'
                                            )
                                    }}
                                </p>

                            </td>



                            {{-- Homework --}}
                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
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
                            </td>



                            {{-- Status --}}
                            <td
                                class="px-6 py-5
                                       text-center"
                            >

                                <span
                                    class="inline-flex
                                           rounded-full
                                           px-3 py-1.5
                                           text-xs
                                           font-bold
                                           {{ $leaveStateClass }}"
                                >
                                    {{ $leaveState }}
                                </span>

                            </td>



                            {{-- Actual Return --}}
                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
                            >

                                @if (
                                    $leave
                                        ->actual_return_date
                                )

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

                            </td>



                            {{-- Actions --}}
                            <td
                                class="px-6 py-5"
                            >

                                <div
                                    class="flex
                                           flex-wrap
                                           items-center
                                           justify-center
                                           gap-2"
                                >

                                    <a
                                        href="{{ route(
                                            'parent.leave.show',
                                            $leave
                                        ) }}"
                                        class="inline-flex
                                               h-10
                                               items-center
                                               justify-center
                                               rounded-xl
                                               border
                                               border-violet-300
                                               bg-white
                                               px-4
                                               text-xs
                                               font-semibold
                                               text-violet-700
                                               hover:bg-violet-50"
                                    >
                                        View Details
                                    </a>


                                    @if ($canReturnEarly)

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
                                                       h-10
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       bg-green-600
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-white
                                                       hover:bg-green-700"
                                            >
                                                Return Early
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-14
                                       text-center"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-700"
                                >
                                    No approved leave records.
                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >
                                    Approved leave will appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>



    {{-- =========================================================
        REQUEST HISTORY HEADER
    ========================================================== --}}

    <div class="mt-8">

        <h2
            class="text-xl
                   font-bold
                   text-slate-900"
        >
            Leave Requests & History
        </h2>


        <p
            class="mt-1
                   text-sm
                   text-slate-500"
        >
            View pending, rejected and cancelled leave requests.
        </p>

    </div>



    {{-- =========================================================
        REQUEST FILTER
    ========================================================== --}}

    <section
        class="mt-4
               rounded-2xl
               border
               border-slate-200
               bg-white
               p-5"
    >

        <form
            method="GET"
            action="{{ route(
                'parent.leave.index'
            ) }}"
            class="flex
                   flex-col
                   gap-3
                   sm:flex-row"
        >

            <select
                name="status"
                class="h-11
                       rounded-xl
                       border-slate-300
                       sm:min-w-[220px]"
            >

                <option
                    value="all"
                    @selected(
                        $status === 'all'
                    )
                >
                    All Requests
                </option>


                <option
                    value="pending"
                    @selected(
                        $status === 'pending'
                    )
                >
                    Pending
                </option>


                <option
                    value="rejected"
                    @selected(
                        $status === 'rejected'
                    )
                >
                    Rejected
                </option>


                <option
                    value="cancelled"
                    @selected(
                        $status === 'cancelled'
                    )
                >
                    Cancelled
                </option>

            </select>


            <button
                type="submit"
                class="h-11
                       rounded-xl
                       bg-violet-600
                       px-5
                       text-sm
                       font-semibold
                       text-white
                       transition
                       hover:bg-violet-700"
            >
                Filter
            </button>


            @if ($status !== 'all')

                <a
                    href="{{ route(
                        'parent.leave.index'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           px-5
                           text-sm
                           font-semibold
                           text-slate-600
                           hover:bg-slate-50"
                >
                    Clear
                </a>

            @endif

        </form>

    </section>



    {{-- =========================================================
        REQUEST TABLE
    ========================================================== --}}

    <section
        class="mt-5
               overflow-hidden
               rounded-[26px]
               border
               border-slate-200
               bg-white
               shadow-sm"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Leave Period
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Homework
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Reason
                        </th>


                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Request Status
                        </th>


                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Action
                        </th>

                    </tr>

                </thead>



                <tbody
                    class="divide-y
                           divide-slate-100"
                >

                    @forelse ($leaves as $leave)

                        <tr
                            class="transition
                                   hover:bg-violet-50/30"
                        >

                            {{-- Leave Period --}}
                            <td
                                class="px-6 py-5"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-900"
                                >
                                    {{
                                        $leave
                                            ->start_date
                                            ->format(
                                                'd M Y'
                                            )
                                    }}

                                    →

                                    {{
                                        $leave
                                            ->expected_return_date
                                            ->format(
                                                'd M Y'
                                            )
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-400"
                                >
                                    Requested:

                                    {{
                                        $leave
                                            ->created_at
                                            ?->format(
                                                'd M Y'
                                            )
                                    }}
                                </p>

                            </td>



                            {{-- Homework --}}
                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
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
                            </td>



                            {{-- Reason --}}
                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $leave->reason
                                    ?? '—'
                                }}
                            </td>



                            {{-- Request Status --}}
                            <td
                                class="px-6 py-5
                                       text-center"
                            >

                                <span
                                    class="inline-flex
                                           rounded-full
                                           px-3 py-1.5
                                           text-xs
                                           font-bold
                                           {{
                                               $requestStatusClasses[
                                                   $leave->status
                                               ]
                                               ??
                                               'bg-slate-100 text-slate-600'
                                           }}"
                                >
                                    {{
                                        $requestStatusLabels[
                                            $leave->status
                                        ]
                                        ??
                                        ucfirst(
                                            $leave->status
                                        )
                                    }}
                                </span>

                            </td>



                            {{-- Actions --}}
                            <td
                                class="px-6 py-5"
                            >

                                <div
                                    class="flex
                                           flex-wrap
                                           items-center
                                           justify-center
                                           gap-2"
                                >

                                    <a
                                        href="{{ route(
                                            'parent.leave.show',
                                            $leave
                                        ) }}"
                                        class="inline-flex
                                               h-10
                                               items-center
                                               justify-center
                                               rounded-xl
                                               border
                                               border-violet-300
                                               bg-white
                                               px-4
                                               text-xs
                                               font-semibold
                                               text-violet-700
                                               hover:bg-violet-50"
                                    >
                                        View Details
                                    </a>



                                    {{-- Edit Pending --}}
                                    @if (
                                        $leave->status
                                        ===
                                        'pending'
                                    )

                                        <a
                                            href="{{ route(
                                                'parent.leave.edit',
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
                                            Edit
                                        </a>


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
                                                       h-10
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       border
                                                       border-red-300
                                                       bg-red-50
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-red-700
                                                       hover:bg-red-100"
                                            >
                                                Cancel
                                            </button>

                                        </form>

                                    @endif



                                    {{-- Delete Rejected/Cancelled --}}
                                    @if (
                                        in_array(
                                            $leave->status,
                                            [
                                                'rejected',
                                                'cancelled',
                                            ]
                                        )
                                    )

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
                                                       h-10
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       border
                                                       border-red-300
                                                       bg-red-50
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-red-700
                                                       hover:bg-red-100"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-14
                                       text-center"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-700"
                                >
                                    No leave requests found.
                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >
                                    New parent leave requests
                                    will appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- Pagination --}}
        @if ($leaves->hasPages())

            <div
                class="border-t
                       border-slate-100
                       px-6 py-4"
            >
                {{ $leaves->links() }}
            </div>

        @endif

    </section>

</div>

@endsection
