@extends('layouts.admin')

@section('title', 'Add Class')

@section('page-title', 'Add Class')

@section('content')

<div class="space-y-6">

    {{-- Back --}}
    <div>
        <a
            href="{{ route(
                'admin.students.show',
                $student
            ) }}"
            class="inline-flex items-center gap-2
                   text-sm font-semibold
                   text-blue-600
                   hover:text-blue-800"
        >
            ← Back to Student Profile
        </a>
    </div>


    {{-- Header --}}
    <section
        class="rounded-2xl
               border border-slate-200
               bg-white p-6
               shadow-sm"
    >

        <div
            class="flex flex-col gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>
                <h1
                    class="text-2xl font-bold
                           text-slate-900"
                >
                    Add New Class
                </h1>

                <p
                    class="mt-1 text-sm
                           text-slate-500"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}

                    · Student ID:
                    {{ $student->external_id }}
                </p>
            </div>

        </div>

    </section>


    {{-- Validation --}}
    @if ($errors->any())

        <div
            class="rounded-xl
                   border border-red-200
                   bg-red-50 px-5 py-4"
        >

            <p
                class="font-semibold
                       text-red-700"
            >
                Please check the information below.
            </p>

            <ul
                class="mt-2 list-disc
                       space-y-1 pl-5
                       text-sm text-red-600"
            >
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- Classes --}}
    <form
        method="POST"
        action="{{ route(
            'admin.student-enrolments.store',
            $student
        ) }}"
    >

        @csrf


        <section
            class="overflow-hidden
                   rounded-2xl
                   border border-slate-200
                   bg-white
                   shadow-sm"
        >

            <div
                class="border-b
                       border-slate-100
                       px-6 py-5"
            >
                <h2
                    class="text-lg
                           font-bold
                           text-slate-900"
                >
                    Available Classes
                </h2>

                <p
                    class="mt-1 text-sm
                           text-slate-500"
                >
                    Select one class and choose
                    Enrol or Wishlist.
                </p>
            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Select
                            </th>

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Section
                            </th>

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Day
                            </th>

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Time
                            </th>

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Seats
                            </th>

                            <th
                                class="px-6 py-4
                                       text-left
                                       text-xs font-semibold
                                       uppercase
                                       text-slate-500"
                            >
                                Availability
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse ($offerings as $offering)

                            @php

                                $availableSeats =
                                    max(
                                        0,
                                        $offering->max_seats
                                        -
                                        $offering->allocated_seats
                                    );

                                $isFull =
                                    $availableSeats <= 0;

                            @endphp


                            <tr
                                class="hover:bg-slate-50"
                            >

                                <td class="px-6 py-5">

                                    <input
                                        type="radio"
                                        name="section_offering_id"
                                        value="{{ $offering->id }}"
                                        class="h-4 w-4
                                               border-slate-300
                                               text-blue-600"
                                        @checked(
                                            old(
                                                'section_offering_id'
                                            )
                                            ==
                                            $offering->id
                                        )
                                        required
                                    >

                                </td>


                                <td
                                    class="px-6 py-5
                                           text-sm
                                           font-semibold
                                           text-slate-900"
                                >
                                    {{
                                        $offering
                                            ->section
                                            ?->section_name
                                        ?? '—'
                                    }}
                                </td>


                                <td
                                    class="px-6 py-5
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        $offering
                                            ->day
                                            ?->day_name
                                        ?? '—'
                                    }}
                                </td>


                                <td
                                    class="whitespace-nowrap
                                           px-6 py-5
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        \Carbon\Carbon::parse(
                                            $offering->start_time
                                        )->format('g:i A')
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::parse(
                                            $offering->end_time
                                        )->format('g:i A')
                                    }}
                                </td>


                                <td
                                    class="px-6 py-5
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        $offering->allocated_seats
                                    }}
                                    /
                                    {{
                                        $offering->max_seats
                                    }}
                                </td>


                                <td class="px-6 py-5">

                                    @if ($isFull)

                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-red-100
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-red-700"
                                        >
                                            Full
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-green-100
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-green-700"
                                        >
                                            {{ $availableSeats }}
                                            available
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-14
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >
                                    No additional classes
                                    are currently available.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>


        @if ($offerings->count() > 0)

            {{-- Enrolment Type --}}
            <section
                class="mt-6
                       rounded-2xl
                       border border-slate-200
                       bg-white p-6
                       shadow-sm"
            >

                <h2
                    class="text-lg font-bold
                           text-slate-900"
                >
                    Enrolment Type
                </h2>


                <div
                    class="mt-5
                           grid gap-4
                           md:grid-cols-2"
                >

                    {{-- Confirmed --}}
                    <label
                        class="cursor-pointer
                               rounded-xl
                               border border-slate-200
                               p-5
                               hover:border-blue-300
                               hover:bg-blue-50"
                    >

                        <div
                            class="flex
                                   items-start
                                   gap-3"
                        >

                            <input
                                type="radio"
                                name="enrolment_type"
                                value="confirmed"
                                class="mt-1"
                                @checked(
                                    old(
                                        'enrolment_type',
                                        'confirmed'
                                    )
                                    ===
                                    'confirmed'
                                )
                            >

                            <div>

                                <p
                                    class="font-semibold
                                           text-slate-900"
                                >
                                    Enrol Student
                                </p>

                                <p
                                    class="mt-1 text-sm
                                           text-slate-500"
                                >
                                    Student receives
                                    a confirmed seat.
                                </p>

                            </div>

                        </div>

                    </label>


                    {{-- Wishlist --}}
                    <label
                        class="cursor-pointer
                               rounded-xl
                               border border-slate-200
                               p-5
                               hover:border-purple-300
                               hover:bg-purple-50"
                    >

                        <div
                            class="flex
                                   items-start
                                   gap-3"
                        >

                            <input
                                type="radio"
                                name="enrolment_type"
                                value="wishlist"
                                class="mt-1"
                                @checked(
                                    old(
                                        'enrolment_type'
                                    )
                                    ===
                                    'wishlist'
                                )
                            >

                            <div>

                                <p
                                    class="font-semibold
                                           text-purple-800"
                                >
                                    Add to Wishlist
                                </p>

                                <p
                                    class="mt-1 text-sm
                                           text-slate-500"
                                >
                                    Does not consume
                                    a class seat.
                                </p>

                            </div>

                        </div>

                    </label>

                </div>

            </section>


            {{-- Buttons --}}
            <div
                class="mt-6
                       flex justify-end
                       gap-3"
            >

                <a
                    href="{{ route(
                        'admin.students.show',
                        $student
                    ) }}"
                    class="rounded-xl
                           border border-slate-300
                           bg-white px-5 py-2.5
                           text-sm font-semibold
                           text-slate-700
                           hover:bg-slate-50"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="rounded-xl
                           bg-blue-600
                           px-6 py-2.5
                           text-sm font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Add Class
                </button>

            </div>

        @endif

    </form>

</div>

@endsection
