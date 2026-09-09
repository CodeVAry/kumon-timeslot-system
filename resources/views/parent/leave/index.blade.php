@extends('layouts.parent')

@section('title', 'Leave Management')

@section('page-title', 'Leave Management')


@section('content')

@php

    $today =
        today();


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
                Inform the centre about leave for

                <span
                    class="font-semibold
                           text-slate-700"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </span>.
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-400"
            >
                Leave information is recorded immediately.
                No admin approval is required.
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
            + Add Leave
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
        LEAVE RECORDS
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
                Leave Records
            </h2>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                View current, upcoming,
                completed and cancelled leave.
            </p>

        </div>



        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Leave Period
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Homework
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Reason
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Actual Return
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                            Action
                        </th>

                    </tr>

                </thead>



                <tbody class="divide-y divide-slate-100">

                    @forelse ($leaves as $leave)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Display State
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $leave->status
                                ===
                                'cancelled'
                            ) {

                                $leaveState =
                                    'Cancelled';

                                $leaveStateClass =
                                    'bg-red-100 text-red-700';

                            } elseif (
                                $leave->actual_return_date
                                &&
                                $leave->returned_early
                            ) {

                                $leaveState =
                                    'Returned Early';

                                $leaveStateClass =
                                    'bg-cyan-100 text-cyan-700';

                            } elseif (
                                $leave->actual_return_date
                            ) {

                                $leaveState =
                                    'Completed';

                                $leaveStateClass =
                                    'bg-slate-200 text-slate-700';

                            } elseif (
                                $leave
                                    ->start_date
                                    ->copy()
                                    ->startOfDay()
                                    ->gt(
                                        $today
                                    )
                            ) {

                                $leaveState =
                                    'Upcoming';

                                $leaveStateClass =
                                    'bg-purple-100 text-purple-700';

                            } elseif (
                                $leave
                                    ->expected_return_date
                                    ->copy()
                                    ->startOfDay()
                                    ->lt(
                                        $today
                                    )
                            ) {

                                $leaveState =
                                    'Completed';

                                $leaveStateClass =
                                    'bg-slate-200 text-slate-700';

                            } else {

                                $leaveState =
                                    'Current Leave';

                                $leaveStateClass =
                                    'bg-blue-100 text-blue-700';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Parent Actions
                            |--------------------------------------------------------------------------
                            */

                            $canEdit =
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
                                    ->gt(
                                        $today
                                    );


                            $canCancel =
                                $canEdit;


                            $canReturnEarly =
                                $leave->status
                                ===
                                'approved'
                                &&
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


                            $canDelete =
                                $leave->status
                                ===
                                'cancelled';

                        @endphp



                        <tr
                            class="transition
                                   hover:bg-violet-50/30"
                        >


                            {{-- Leave Period --}}
                            <td class="px-6 py-5">

                                <p class="font-semibold text-slate-900">

                                    {{ $leave->start_date->format('d M Y') }}

                                    →

                                    {{ $leave->expected_return_date->format('d M Y') }}

                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-400"
                                >
                                    Recorded:

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
                            <td class="px-6 py-5 text-sm text-slate-600">

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
                            <td class="px-6 py-5 text-sm text-slate-600">

                                {{ $leave->reason ?? '—' }}

                            </td>



                            {{-- Status --}}
                            <td class="px-6 py-5 text-center">

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
                            <td class="px-6 py-5 text-sm text-slate-600">

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

                            </td>



                            {{-- Actions --}}
                            <td class="px-6 py-5">

                                <div
                                    class="flex
                                           flex-wrap
                                           items-center
                                           justify-center
                                           gap-2"
                                >

                                    {{-- View --}}
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
                                        View
                                    </a>



                                    {{-- Edit --}}
                                    @if ($canEdit)

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
                                                        'Cancel this upcoming leave?'
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



                                    {{-- Return Early --}}
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



                                    {{-- Delete Cancelled --}}
                                    @if ($canDelete)

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
                                                        'Delete this cancelled leave record permanently?'
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
                                colspan="6"
                                class="px-6 py-14
                                       text-center"
                            >

                                <p class="font-semibold text-slate-700">
                                    No leave information recorded.
                                </p>


                                <p class="mt-1 text-sm text-slate-500">
                                    Use Add Leave to inform the centre about an absence period.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



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
