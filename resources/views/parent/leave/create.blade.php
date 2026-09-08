@extends('layouts.parent')

@section('title', 'Inform Centre About Leave')

@section('page-title', 'Student Leave')


@section('content')

<div
    class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8"
>

    <a
        href="{{ route(
            'parent.leave.index'
        ) }}"
        class="text-sm
               font-semibold
               text-violet-700"
    >
        ← Back to Leave Management
    </a>


    <div class="mt-6">

        <p
            class="text-xs
                   font-bold
                   uppercase
                   tracking-wide
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
            Inform Centre About Leave
        </h1>


        <p
            class="mt-2
                   max-w-2xl
                   text-sm
                   leading-6
                   text-slate-500"
        >
            Enter the planned leave details for

            <strong class="text-slate-700">
                {{ $student->first_name }}
                {{ $student->last_name }}
            </strong>.

            The centre will be informed immediately
            when you submit this form.
        </p>

    </div>


    @if ($errors->any())

        <div
            class="mt-6
                   rounded-xl
                   border border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            <ul
                class="list-disc
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


    <section
        class="mt-7
               rounded-[26px]
               border border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="flex flex-col
                   gap-5
                   lg:flex-row
                   lg:justify-between"
        >

            <div>

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-blue-600"
                >
                    Student Information
                </p>

                <h2
                    class="mt-2
                           text-2xl
                           font-bold"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Student ID:
                    {{ $student->external_id ?? '—' }}
                </p>

            </div>


            <div
                class="rounded-xl
                       border border-green-200
                       bg-green-50
                       px-4 py-3"
            >

                <p
                    class="text-sm
                           font-semibold
                           text-green-700"
                >
                    No Approval Required
                </p>

                <p
                    class="mt-1
                           text-xs
                           text-green-600"
                >
                    Submitting this form immediately
                    records the leave and informs the centre.
                </p>

            </div>

        </div>


        <div
            class="mt-6
                   border-t
                   border-slate-100
                   pt-5"
        >

            <p class="font-bold text-slate-800">
                Active Classes
            </p>


            <div
                class="mt-4
                       grid gap-3
                       md:grid-cols-2
                       xl:grid-cols-3"
            >

                @forelse ($enrolments as $enrolment)

                    @php
                        $offering =
                            $enrolment
                                ->sectionOffering;
                    @endphp


                    <div
                        class="rounded-xl
                               border border-slate-200
                               bg-slate-50
                               p-4"
                    >

                        <p class="font-bold">

                            {{
                                $offering
                                    ?->section
                                    ?->section_name
                                ?? 'Class'
                            }}

                            @if ($enrolment->subSection)

                                -
                                {{
                                    $enrolment
                                        ->subSection
                                        ->sub_section_name
                                }}

                            @endif

                        </p>


                        @if ($offering)

                            <p
                                class="mt-2
                                       text-sm
                                       text-slate-500"
                            >
                                {{
                                    $offering
                                        ->day
                                        ?->day_name
                                    ?? '—'
                                }}

                                •

                                {{
                                    \Carbon\Carbon::parse(
                                        $offering
                                            ->start_time
                                    )->format(
                                        'g:i A'
                                    )
                                }}
                            </p>

                        @endif

                    </div>


                @empty

                    <p class="text-sm text-slate-500">
                        No active enrolled classes.
                    </p>

                @endforelse

            </div>

        </div>

    </section>


    <section
        class="mt-6
               rounded-[26px]
               border border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <h2
            class="text-xl
                   font-bold
                   text-slate-900"
        >
            Leave Information
        </h2>


        <form
            method="POST"
            action="{{ route(
                'parent.leave.store'
            ) }}"
            class="mt-6"
        >

            @csrf


            <div
                class="grid
                       gap-5
                       md:grid-cols-2"
            >

                <div>

                    <label
                        for="start_date"
                        class="mb-2
                               block
                               text-sm
                               font-semibold"
                    >
                        Start Date *
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        value="{{ old('start_date') }}"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>


                <div>

                    <label
                        for="expected_return_date"
                        class="mb-2
                               block
                               text-sm
                               font-semibold"
                    >
                        Expected Return Date *
                    </label>

                    <input
                        type="date"
                        name="expected_return_date"
                        id="expected_return_date"
                        value="{{
                            old(
                                'expected_return_date'
                            )
                        }}"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>


                <div>

                    <label
                        for="homework_requirement"
                        class="mb-2
                               block
                               text-sm
                               font-semibold"
                    >
                        Homework *
                    </label>

                    <select
                        name="homework_requirement"
                        id="homework_requirement"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                        <option value="">
                            Select homework requirement
                        </option>

                        <option
                            value="none_required"
                            @selected(
                                old(
                                    'homework_requirement'
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
                                    'same_as_normal'
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
                                    'homework_requirement'
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
                                    'homework_requirement'
                                )
                                ===
                                'decrease'
                            )
                        >
                            Decrease
                        </option>

                    </select>

                </div>


                <div>

                    <label
                        for="reason"
                        class="mb-2
                               block
                               text-sm
                               font-semibold"
                    >
                        Reason
                    </label>

                    <input
                        type="text"
                        name="reason"
                        id="reason"
                        value="{{ old('reason') }}"
                        maxlength="255"
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>

            </div>


            <div class="mt-5">

                <label
                    for="notes"
                    class="mb-2
                           block
                           text-sm
                           font-semibold"
                >
                    Notes
                </label>

                <textarea
                    name="notes"
                    id="notes"
                    rows="5"
                    maxlength="2000"
                    class="w-full
                           rounded-xl
                           border-slate-300"
                >{{ old('notes') }}</textarea>

            </div>


            <div
                class="mt-6
                       rounded-xl
                       border border-blue-200
                       bg-blue-50
                       p-4"
            >
                <p
                    class="font-semibold
                           text-blue-800"
                >
                    Centre Notification
                </p>

                <p
                    class="mt-1
                           text-sm
                           text-blue-700"
                >
                    When submitted, this leave is recorded
                    immediately. Centre staff can view it
                    from Leave Management.
                </p>
            </div>


            <div
                class="mt-7
                       flex
                       justify-end
                       gap-3"
            >

                <a
                    href="{{ route(
                        'parent.leave.index'
                    ) }}"
                    class="inline-flex
                           h-12
                           items-center
                           rounded-xl
                           border border-slate-300
                           px-6
                           font-semibold"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="inline-flex
                           h-12
                           items-center
                           rounded-xl
                           bg-violet-600
                           px-7
                           font-bold
                           text-white
                           hover:bg-violet-700"
                >
                    Submit Leave Information
                </button>

            </div>

        </form>

    </section>

</div>

@endsection
