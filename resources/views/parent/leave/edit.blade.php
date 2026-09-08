@extends('layouts.parent')

@section('title', 'Edit Leave')

@section('page-title', 'Edit Leave')


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
            'parent.leave.show',
            $leave
        ) }}"
        class="text-sm
               font-semibold
               text-violet-700"
    >
        ← Back to Leave Details
    </a>


    <div class="mt-6">

        <p
            class="text-xs
                   font-bold
                   uppercase
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
            Edit Leave
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Update the upcoming leave information for

            <strong>
                {{ $student->first_name }}
                {{ $student->last_name }}
            </strong>.
        </p>

    </div>


    @if ($errors->any())

        <div
            class="mt-6
                   rounded-xl
                   border border-red-200
                   bg-red-50
                   p-4"
        >

            @foreach ($errors->all() as $error)

                <p class="text-sm text-red-700">
                    {{ $error }}
                </p>

            @endforeach

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

        <form
            method="POST"
            action="{{ route(
                'parent.leave.update',
                $leave
            ) }}"
        >

            @csrf
            @method('PATCH')


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
                               font-semibold"
                    >
                        Start Date *
                    </label>

                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{
                            old(
                                'start_date',
                                $leave
                                    ->start_date
                                    ->format('Y-m-d')
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
                        for="expected_return_date"
                        class="mb-2
                               block
                               font-semibold"
                    >
                        Expected Return Date *
                    </label>

                    <input
                        type="date"
                        id="expected_return_date"
                        name="expected_return_date"
                        value="{{
                            old(
                                'expected_return_date',
                                $leave
                                    ->expected_return_date
                                    ->format('Y-m-d')
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
                               font-semibold"
                    >
                        Homework *
                    </label>

                    <select
                        id="homework_requirement"
                        name="homework_requirement"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                        @foreach ([
                            'none_required'
                                => 'None Required',

                            'same_as_normal'
                                => 'Same as Normal',

                            'increase'
                                => 'Increase',

                            'decrease'
                                => 'Decrease',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'homework_requirement',
                                        $leave
                                            ->homework_requirement
                                    )
                                    ===
                                    $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div>

                    <label
                        for="reason"
                        class="mb-2
                               block
                               font-semibold"
                    >
                        Reason
                    </label>

                    <input
                        type="text"
                        id="reason"
                        name="reason"
                        value="{{
                            old(
                                'reason',
                                $leave->reason
                            )
                        }}"
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
                           font-semibold"
                >
                    Notes
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="5"
                    maxlength="2000"
                    class="w-full
                           rounded-xl
                           border-slate-300"
                >{{ old(
                    'notes',
                    $leave->notes
                ) }}</textarea>

            </div>


            <div
                class="mt-6
                       rounded-xl
                       border border-blue-200
                       bg-blue-50
                       p-4
                       text-sm
                       text-blue-700"
            >
                Updating this leave immediately updates
                the information available to centre staff.
            </div>


            <div
                class="mt-7
                       flex
                       justify-end
                       gap-3"
            >

                <a
                    href="{{ route(
                        'parent.leave.show',
                        $leave
                    ) }}"
                    class="inline-flex
                           h-12
                           items-center
                           rounded-xl
                           border
                           border-slate-300
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
                           text-white"
                >
                    Update Leave
                </button>

            </div>

        </form>

    </section>

</div>

@endsection
