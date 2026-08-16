@extends('layouts.admin')

@section('title', 'Class Student List')

@section('page-title', 'Class Student List')


@section('content')

<div class="space-y-6">


    <div>

        <a
            href="{{ route(
                'admin.schedule.index',
                [
                    'day_id' =>
                        $sectionOffering->day_id,

                    'time' =>
                        \Carbon\Carbon::parse(
                            $sectionOffering->start_time
                        )->format('H:i:s'),

                    'offering_id' =>
                        $sectionOffering->id,
                ]
            ) }}"
            class="text-sm
                   font-semibold
                   text-blue-600"
        >
            ← Back to Schedule
        </a>

    </div>


    <div
        class="flex flex-col gap-4
               sm:flex-row
               sm:items-end
               sm:justify-between"
    >

        <div>

            <h1
                class="text-3xl
                       font-bold
                       text-slate-900"
            >
                {{
                    $sectionOffering
                        ->section
                        ?->section_name
                    ?? 'Class'
                }}
            </h1>

            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                {{
                    $sectionOffering
                        ->day
                        ?->day_name
                }}

                ·

                {{
                    \Carbon\Carbon::parse(
                        $sectionOffering
                            ->start_time
                    )->format(
                        'g:i A'
                    )
                }}

                –

                {{
                    \Carbon\Carbon::parse(
                        $sectionOffering
                            ->end_time
                    )->format(
                        'g:i A'
                    )
                }}
            </p>

        </div>


        @if (
            auth()->user()
                ->hasPermission(
                    'enrolments.print'
                )
        )

            <a
                href="{{ route(
                    'admin.schedule.class-students.print',
                    $sectionOffering
                ) }}"
                target="_blank"
                class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       bg-blue-600
                       px-5
                       text-sm
                       font-semibold
                       text-white"
            >
                Print Class List
            </a>

        @endif

    </div>



    <div
        class="grid gap-4
               sm:grid-cols-3"
    >

        <div
            class="rounded-xl
                   border
                   border-slate-200
                   bg-white p-5"
        >
            <p class="text-xs text-slate-400">
                Confirmed Students
            </p>

            <p
                class="mt-1
                       text-2xl
                       font-bold"
            >
                {{ $enrolments->total() }}
            </p>
        </div>


        <div
            class="rounded-xl
                   border
                   border-slate-200
                   bg-white p-5"
        >
            <p class="text-xs text-slate-400">
                Maximum Seats
            </p>

            <p
                class="mt-1
                       text-2xl
                       font-bold"
            >
                {{ $sectionOffering->max_seats }}
            </p>
        </div>


        <div
            class="rounded-xl
                   border
                   border-slate-200
                   bg-white p-5"
        >
            <p class="text-xs text-slate-400">
                Wishlist
            </p>

            <p
                class="mt-1
                       text-2xl
                       font-bold"
            >
                {{ $wishlistCount }}
            </p>
        </div>

    </div>



    <div
        class="overflow-hidden
               rounded-2xl
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
                            Student
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Student ID
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Guardian
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Phone
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y
                           divide-slate-100"
                >

                    @forelse ($enrolments as $enrolment)

                        @php

                            $student =
                                $enrolment->student;

                            $guardian =
                                $student
                                    ?->guardians
                                    ?->first(
                                        function (
                                            $guardian
                                        ) {
                                            return
                                                $guardian
                                                    ->pivot
                                                    ->is_primary;
                                        }
                                    );

                            if (
                                !$guardian &&
                                $student
                            ) {
                                $guardian =
                                    $student
                                        ->guardians
                                        ->first();
                            }

                        @endphp


                        <tr>

                            <td
                                class="px-6 py-4
                                       font-semibold
                                       text-slate-800"
                            >
                                {{
                                    $student
                                        ?->first_name
                                }}
                                {{
                                    $student
                                        ?->last_name
                                }}
                            </td>


                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $student
                                        ?->external_id
                                    ?? '—'
                                }}
                            </td>


                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-slate-600"
                            >
                                @if ($guardian)

                                    {{
                                        $guardian
                                            ->first_name
                                    }}
                                    {{
                                        $guardian
                                            ->last_name
                                    }}

                                @else

                                    —

                                @endif
                            </td>


                            <td
                                class="px-6 py-4
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $guardian
                                        ?->phone
                                    ?? '—'
                                }}
                            </td>


                            <td class="px-6 py-4">

                                {{
                                    $student
                                        ?->studentStatus
                                        ?->status_name
                                    ?? '—'
                                }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-12
                                       text-center
                                       text-slate-500"
                            >
                                No students found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($enrolments->hasPages())

            <div
                class="border-t
                       border-slate-100
                       px-6 py-4"
            >
                {{ $enrolments->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
