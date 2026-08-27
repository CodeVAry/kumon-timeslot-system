@extends('layouts.admin')

@section('title', 'Edit Leave')

@section('page-title', 'Edit Leave')


@php

    $breadcrumbs = [
        [
            'label' => 'Leave Management',
            'url' => route('admin.leave.index'),
        ],
        [
            'label' => 'Leave Details',
            'url' => route(
                'admin.leave.show',
                $leave
            ),
        ],
        [
            'label' => 'Edit Leave',
            'url' => null,
        ],
    ];


    /*
    |--------------------------------------------------------------------------
    | Parent-created request?
    |--------------------------------------------------------------------------
    |
    | If requested_by_guardian_id exists, notes belongs to the parent.
    | Admin should not overwrite it.
    |--------------------------------------------------------------------------
    */

    $isParentRequest =
        !is_null(
            $leave->requested_by_guardian_id
        );


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

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        BACK
    ========================================================== --}}

    <a
        href="{{ route(
            'admin.leave.show',
            $leave
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
        ← Back to Leave Details
    </a>



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
        HEADER
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
                    Edit / Extend Leave
                </p>


                <h1
                    class="mt-2
                           text-2xl
                           font-bold
                           text-slate-900"
                >
                    {{ $leave->student?->first_name }}
                    {{ $leave->student?->last_name }}
                </h1>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Student ID:
                    {{
                        $leave
                            ->student
                            ?->external_id
                        ??
                        '—'
                    }}
                </p>


                @if ($isParentRequest)

                    <div
                        class="mt-3
                               inline-flex
                               rounded-full
                               bg-violet-100
                               px-3 py-1
                               text-xs
                               font-semibold
                               text-violet-700"
                    >
                        Parent Leave Request
                    </div>

                @else

                    <div
                        class="mt-3
                               inline-flex
                               rounded-full
                               bg-blue-100
                               px-3 py-1
                               text-xs
                               font-semibold
                               text-blue-700"
                    >
                        Staff-Created Leave
                    </div>

                @endif

            </div>



            <div
                class="rounded-xl
                       bg-blue-50
                       px-5 py-3"
            >

                <p
                    class="text-xs
                           text-slate-500"
                >
                    Current Leave Period
                </p>


                <p
                    class="mt-1
                           font-semibold
                           text-blue-700"
                >
                    {{
                        $leave
                            ->start_date
                            ->format(
                                'd M Y'
                            )
                    }}

                    –

                    {{
                        $leave
                            ->expected_return_date
                            ->format(
                                'd M Y'
                            )
                    }}
                </p>

            </div>

        </div>

    </section>



    {{-- =========================================================
        FORM
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route(
            'admin.leave.update',
            $leave
        ) }}"
        class="space-y-6"
    >

        @csrf
        @method('PATCH')



        {{-- =====================================================
            LEAVE DETAILS
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
                    Leave Details
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Update the leave dates,
                    reason and homework requirement.
                </p>

            </div>



            <div
                class="mt-6
                       grid
                       gap-5
                       md:grid-cols-2"
            >


                {{-- Start Date --}}
                <div>

                    <label
                        for="start_date"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700"
                    >
                        Start Date
                    </label>


                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        value="{{
                            old(
                                'start_date',
                                $leave
                                    ->start_date
                                    ->format(
                                        'Y-m-d'
                                    )
                            )
                        }}"
                        required
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                </div>



                {{-- Expected Return --}}
                <div>

                    <label
                        for="expected_return_date"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700"
                    >
                        Expected Return Date
                    </label>


                    <input
                        type="date"
                        name="expected_return_date"
                        id="expected_return_date"
                        value="{{
                            old(
                                'expected_return_date',
                                $leave
                                    ->expected_return_date
                                    ->format(
                                        'Y-m-d'
                                    )
                            )
                        }}"
                        required
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                </div>



                {{-- Homework Requirement --}}
                <div>

                    <label
                        for="homework_requirement"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700"
                    >
                        Homework Required
                    </label>


                    <select
                        name="homework_requirement"
                        id="homework_requirement"
                        required
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option
                            value="none_required"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                ===
                                'none_required'
                            )
                        >
                            None Required
                        </option>


                        <option
                            value="same_as_normal"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                ===
                                'same_as_normal'
                            )
                        >
                            Same as Normal
                        </option>


                        <option
                            value="increase"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                ===
                                'increase'
                            )
                        >
                            Increase
                        </option>


                        <option
                            value="decrease"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                ===
                                'decrease'
                            )
                        >
                            Decrease
                        </option>

                    </select>


                    <p
                        class="mt-2
                               text-xs
                               text-slate-500"
                    >
                        Current:
                        {{
                            $homeworkLabels[
                                $leave->homework_requirement
                            ]
                            ??
                            $leave->homework_requirement
                        }}
                    </p>

                </div>



                {{-- Reason --}}
                <div>

                    <label
                        for="reason"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700"
                    >
                        Leave Reason
                    </label>


                    <input
                        type="text"
                        name="reason"
                        id="reason"
                        value="{{
                            old(
                                'reason',
                                $leave->reason
                            )
                        }}"
                        maxlength="255"
                        placeholder="Enter leave reason"
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                </div>

            </div>

        </section>



        {{-- =====================================================
            PARENT NOTE
        ====================================================== --}}

        @if ($isParentRequest)

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
                           items-start
                           gap-4"
                >

                    <div
                        class="flex
                               h-10 w-10
                               shrink-0
                               items-center
                               justify-center
                               rounded-xl
                               bg-amber-100
                               text-amber-700"
                    >
                        ✎
                    </div>


                    <div class="min-w-0 flex-1">

                        <h2
                            class="text-lg
                                   font-bold
                                   text-slate-900"
                        >
                            Parent Note
                        </h2>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-500"
                        >
                            This note was submitted by the parent
                            and cannot be changed from the Admin page.
                        </p>


                        <div
                            class="mt-4
                                   rounded-xl
                                   border
                                   border-amber-200
                                   bg-white
                                   p-5"
                        >

                            <p
                                class="whitespace-pre-line
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

                </div>


                {{--
                    Keep the original parent note
                    when Admin submits the form.
                --}}
                <input
                    type="hidden"
                    name="notes"
                    value="{{ $leave->notes }}"
                >

            </section>


        @else

            {{-- =================================================
                STAFF NOTE
            ================================================== --}}

            <section
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-6
                       shadow-sm"
            >

                <label
                    for="notes"
                    class="block
                           text-lg
                           font-bold
                           text-slate-900"
                >
                    Leave Note
                </label>


                <p
                    class="mt-1
                           text-xs
                           text-slate-500"
                >
                    Internal/general note for this
                    staff-created leave.
                </p>


                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    maxlength="2000"
                    placeholder="Add a leave note..."
                    class="mt-4
                           w-full
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           p-4
                           text-sm
                           text-slate-800
                           focus:border-blue-500
                           focus:ring-blue-500"
                >{{ old(
                    'notes',
                    $leave->notes
                ) }}</textarea>

            </section>

        @endif



        {{-- =====================================================
            CENTRE HOMEWORK INSTRUCTIONS
        ====================================================== --}}

        <section
            class="rounded-2xl
                   border
                   border-violet-200
                   bg-violet-50
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       flex-col
                       gap-3
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
                        Homework Plan
                    </p>


                    <h2
                        class="mt-1
                               text-xl
                               font-bold
                               text-slate-900"
                    >
                        Centre Homework Instructions
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-600"
                    >
                        Enter the instructions the parent
                        should see for homework during leave.
                    </p>

                </div>


                <span
                    class="inline-flex
                           self-start
                           rounded-full
                           bg-violet-100
                           px-4 py-2
                           text-sm
                           font-bold
                           text-violet-700"
                >
                    {{
                        $homeworkLabels[
                            old(
                                'homework_requirement',
                                $leave->homework_requirement
                            )
                        ]
                        ??
                        old(
                            'homework_requirement',
                            $leave->homework_requirement
                        )
                    }}
                </span>

            </div>



            <div
                class="mt-5
                       rounded-xl
                       bg-white
                       p-5"
            >

                <label
                    for="review_note"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Homework Instructions
                </label>


                <textarea
                    name="review_note"
                    id="review_note"
                    rows="5"
                    maxlength="2000"
                    placeholder="Example: Complete Chapter 1 and Chapter 2."
                    class="w-full
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           p-4
                           text-sm
                           text-slate-800
                           focus:border-violet-500
                           focus:ring-violet-500"
                >{{ old(
                    'review_note',
                    $leave->review_note
                ) }}</textarea>


                <p
                    class="mt-2
                           text-xs
                           text-slate-500"
                >
                    This field is saved as
                    <strong>Centre Homework Instructions</strong>
                    and is visible to the parent.
                </p>

            </div>

        </section>



        {{-- =====================================================
            ACTIONS
        ====================================================== --}}

        <div
            class="flex
                   flex-wrap
                   justify-end
                   gap-3"
        >

            <a
                href="{{ route(
                    'admin.leave.show',
                    $leave
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
                Cancel
            </a>


            <button
                type="submit"
                class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-600
                       px-7
                       text-sm
                       font-semibold
                       text-white
                       transition
                       hover:bg-blue-700"
            >
                Update Leave
            </button>

        </div>

    </form>

</div>

@endsection
