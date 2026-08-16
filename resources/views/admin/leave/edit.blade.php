
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
            'label' => 'Edit Leave',
            'url' => null,
        ],
    ];

@endphp


@section('content')

<div class="space-y-6">


    <a
        href="{{ route(
            'admin.leave.show',
            $leave
        ) }}"
        class="text-sm
               font-semibold
               text-blue-600"
    >
        ← Back to Leave Details
    </a>



    @if ($errors->any())

        <div
            class="rounded-xl
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
        class="rounded-2xl
               border border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <div
            class="flex flex-col
                   gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <p
                    class="text-xs font-semibold
                           uppercase
                           text-blue-600"
                >
                    Edit / Extend Leave
                </p>


                <h1
                    class="mt-2 text-2xl
                           font-bold
                           text-slate-900"
                >
                    {{ $leave->student?->first_name }}
                    {{ $leave->student?->last_name }}
                </h1>


                <p
                    class="mt-1 text-sm
                           text-slate-500"
                >
                    Student ID:
                    {{ $leave->student?->external_id ?? '—' }}
                </p>

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
                    class="mt-1 font-semibold
                           text-blue-700"
                >
                    {{
                        $leave
                            ->start_date
                            ->format('d M Y')
                    }}
                    –
                    {{
                        $leave
                            ->expected_return_date
                            ->format('d M Y')
                    }}
                </p>

            </div>

        </div>

    </section>



    <section
        class="rounded-2xl
               border border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <form
            method="POST"
            action="{{ route(
                'admin.leave.update',
                $leave
            ) }}"
        >

            @csrf
            @method('PATCH')


            <div
                class="grid gap-5
                       md:grid-cols-2"
            >


                <div>

                    <label
                        for="start_date"
                        class="mb-2 block
                               text-sm font-semibold
                               text-slate-700"
                    >
                        Start Date
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        value="{{ old(
                            'start_date',
                            $leave->start_date->format('Y-m-d')
                        ) }}"
                        required
                        class="w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>



                <div>

                    <label
                        for="expected_return_date"
                        class="mb-2 block
                               text-sm font-semibold
                               text-slate-700"
                    >
                        Expected Return Date
                    </label>

                    <input
                        type="date"
                        name="expected_return_date"
                        id="expected_return_date"
                        value="{{ old(
                            'expected_return_date',
                            $leave
                                ->expected_return_date
                                ->format('Y-m-d')
                        ) }}"
                        required
                        class="w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>



                <div>

                    <label
                        for="homework_requirement"
                        class="mb-2 block
                               text-sm font-semibold
                               text-slate-700"
                    >
                        Homework Required
                    </label>

                    <select
                        name="homework_requirement"
                        id="homework_requirement"
                        required
                        class="w-full
                               rounded-xl
                               border-slate-300"
                    >

                        <option
                            value="none_required"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                === 'none_required'
                            )
                        >
                            None required
                        </option>

                        <option
                            value="same_as_normal"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                === 'same_as_normal'
                            )
                        >
                            Same as normal
                        </option>

                        <option
                            value="increase"
                            @selected(
                                old(
                                    'homework_requirement',
                                    $leave->homework_requirement
                                )
                                === 'increase'
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
                                === 'decrease'
                            )
                        >
                            Decrease
                        </option>

                    </select>

                </div>



                <div>

                    <label
                        for="reason"
                        class="mb-2 block
                               text-sm font-semibold
                               text-slate-700"
                    >
                        Reason
                    </label>

                    <input
                        type="text"
                        name="reason"
                        id="reason"
                        value="{{ old(
                            'reason',
                            $leave->reason
                        ) }}"
                        maxlength="255"
                        class="w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>

            </div>



            <div class="mt-5">

                <label
                    for="notes"
                    class="mb-2 block
                           text-sm font-semibold
                           text-slate-700"
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
                >{{ old(
                    'notes',
                    $leave->notes
                ) }}</textarea>

            </div>



            <div
                class="mt-6 flex
                       justify-end gap-3"
            >

                <a
                    href="{{ route(
                        'admin.leave.show',
                        $leave
                    ) }}"
                    class="inline-flex h-11
                           items-center
                           rounded-xl
                           border border-slate-300
                           px-6
                           text-sm font-semibold
                           text-slate-600"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="inline-flex h-11
                           items-center
                           rounded-xl
                           bg-blue-600
                           px-7
                           text-sm font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Update Leave
                </button>

            </div>

        </form>

    </section>

</div>

@endsection
