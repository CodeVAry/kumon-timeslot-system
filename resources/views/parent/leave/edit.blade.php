@extends('layouts.parent')

@section('title', 'Edit Leave Request')

@section('page-title', 'Edit Leave Request')


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
        class="inline-flex
               text-sm
               font-semibold
               text-violet-700
               hover:text-violet-900"
    >
        ← Back to Leave Details
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
            Edit Leave Request
        </h1>


        <p
            class="mt-2
                   text-sm
                   text-slate-500"
        >
            Update the pending leave request for

            <span class="font-semibold text-slate-700">
                {{ $student->first_name }}
                {{ $student->last_name }}
            </span>.
        </p>

    </div>



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



    <section
        class="mt-7
               rounded-[26px]
               border
               border-slate-200
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
                        Start Date *
                    </label>


                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ old(
                            'start_date',
                            $leave
                                ->start_date
                                ->format('Y-m-d')
                        ) }}"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               px-4
                               focus:border-violet-500
                               focus:ring-violet-200"
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
                        Expected Return Date *
                    </label>


                    <input
                        type="date"
                        id="expected_return_date"
                        name="expected_return_date"
                        value="{{ old(
                            'expected_return_date',
                            $leave
                                ->expected_return_date
                                ->format('Y-m-d')
                        ) }}"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               px-4
                               focus:border-violet-500
                               focus:ring-violet-200"
                    >

                </div>



                {{-- Homework --}}
                <div>

                    <label
                        for="homework_requirement"
                        class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700"
                    >
                        Homework Required *
                    </label>


                    <select
                        id="homework_requirement"
                        name="homework_requirement"
                        required
                        class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-4"
                    >

                        <option
                            value="same_as_normal"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave
                                        ->homework_requirement
                                )
                                ===
                                'same_as_normal'
                            )
                        >
                            Same as normal
                        </option>


                        <option
                            value="increase"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave
                                        ->homework_requirement
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
                                    $leave
                                        ->homework_requirement
                                )
                                ===
                                'decrease'
                            )
                        >
                            Decrease
                        </option>

                    </select>

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
                        Reason
                    </label>


                    <input
                        type="text"
                        id="reason"
                        name="reason"
                        maxlength="255"
                        value="{{ old(
                            'reason',
                            $leave->reason
                        ) }}"
                        class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               px-4"
                    >

                </div>

            </div>



            {{-- Notes --}}
            <div class="mt-5">

                <label
                    for="notes"
                    class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700"
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
                           border
                           border-slate-300
                           p-4"
                >{{ old(
                    'notes',
                    $leave->notes
                ) }}</textarea>

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
                           justify-center
                           rounded-xl
                           border
                           border-slate-300
                           px-6
                           text-sm
                           font-semibold
                           text-slate-600"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="inline-flex
                           h-12
                           items-center
                           justify-center
                           rounded-xl
                           bg-violet-600
                           px-7
                           text-sm
                           font-bold
                           text-white
                           hover:bg-violet-700"
                >
                    Update Request
                </button>

            </div>

        </form>

    </section>

</div>

@endsection
