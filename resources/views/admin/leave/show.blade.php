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
    | Leave Status
    |--------------------------------------------------------------------------
    */

    $today =
        now()->startOfDay();


    if ($leave->actual_return_date) {

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
    | Can Return
    |--------------------------------------------------------------------------
    */

    $canRecordReturn =
        !$leave->actual_return_date
        &&
        $leave
            ->start_date
            ->copy()
            ->startOfDay()
            ->lte($today);


    /*
    |--------------------------------------------------------------------------
    | Is Expected Return Still Future?
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


                @if (!$leave->actual_return_date)

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
                        Edit / Extend
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



            {{-- Notes --}}
            <div
                class="mt-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           tracking-wide
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
                    {{
                        $leave->notes
                        ?? '—'
                    }}
                </p>

            </div>

        </section>



        {{-- =====================================================
            CLASSES AFFECTED
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
                    This leave applies automatically
                    to all active enrolled classes.
                </p>

            </div>



            <div
                class="mt-5
                       space-y-3"
            >

                @forelse ($student?->enrolments ?? collect() as $enrolment)

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

        </section>

    </div>



    {{-- =========================================================
        EARLY RETURN / RETURN STUDENT
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
                               leading-6
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


                    @if ($isEarlyReturnPossible)

                        <p
                            class="mt-1
                                   text-sm
                                   text-slate-500"
                        >
                            Enter the actual return date.
                            If it is before the expected return
                            date, the system will automatically
                            record this as an early return.
                        </p>

                    @endif

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
                                'Record this student return? This will end the leave for all active enrolled classes.'
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
        !$leave->actual_return_date &&
        $leave
            ->start_date
            ->copy()
            ->startOfDay()
            ->gt($today)
    )

        {{-- Upcoming Leave --}}
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
        COMPLETED RETURN SUMMARY
    ========================================================== --}}

    @if ($leave->actual_return_date)

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
                   sm:grid-cols-3"
        >


            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Created By
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-slate-800"
                >
                    {{
                        $leave
                            ->createdBy
                            ?->name
                        ??
                        $leave
                            ->createdBy
                            ?->email
                        ??
                        '—'
                    }}
                </p>

            </div>



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
                        ?? '—'
                    }}
                </p>

            </div>



            <div>

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Last Updated
                </p>


                <p
                    class="mt-2
                           text-sm
                           font-semibold
                           text-slate-800"
                >
                    {{
                        $leave
                            ->updated_at
                            ?->format(
                                'd M Y, g:i A'
                            )
                        ?? '—'
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
                   hover:bg-slate-50"
        >
            Back
        </a>


        @if (!$leave->actual_return_date)

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
                Edit / Extend Leave
            </a>

        @endif

    </div>

</div>

@endsection
