@extends('layouts.admin')

@section('title', 'Leave History')

@section('page-title', 'Leave History')


@php

    $breadcrumbs = [
        [
            'label' => 'Leave Management',
            'url' => route('admin.leave.index'),
        ],
        [
            'label' => 'Leave History',
            'url' => null,
        ],
    ];

@endphp


@section('content')

<div class="space-y-6">


    <div
        class="flex flex-col gap-4
               md:flex-row
               md:items-end
               md:justify-between"
    >

        <div>

            <h1
                class="text-3xl font-bold
                       text-slate-900"
            >
                Leave History
            </h1>

            <p
                class="mt-2 text-sm
                       text-slate-500"
            >
                View completed and past
                student leave records.
            </p>

        </div>


        <a
            href="{{ route('admin.leave.index') }}"
            class="inline-flex h-11
                   items-center
                   rounded-xl
                   border border-blue-300
                   bg-white
                   px-5
                   text-sm font-semibold
                   text-blue-700"
        >
            Back to Leave Management
        </a>

    </div>



    <section
        class="overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white
               shadow-sm"
    >


        {{-- Search --}}
        <div
            class="border-b
                   border-slate-100
                   p-5"
        >

            <form
                method="GET"
                action="{{ route(
                    'admin.leave.history'
                ) }}"
                class="flex flex-col
                       gap-3
                       md:flex-row"
            >

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search student name or ID..."
                    class="w-full
                           rounded-xl
                           border-slate-300
                           md:max-w-md"
                >


                <button
                    type="submit"
                    class="rounded-xl
                           bg-blue-600
                           px-5 py-2.5
                           text-sm font-semibold
                           text-white"
                >
                    Search
                </button>


                @if ($search !== '')

                    <a
                        href="{{ route(
                            'admin.leave.history'
                        ) }}"
                        class="inline-flex
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-slate-300
                               px-5 py-2.5
                               text-sm font-semibold
                               text-slate-600"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>



        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Student
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Classes
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Start Date
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Expected Return
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Actual Return
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Reason
                        </th>

                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs font-bold
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

                        @php

                            $student =
                                $leave->student;


                            $classNames =
                                $student
                                    ?->enrolments
                                    ?->map(
                                        function (
                                            $enrolment
                                        ) {

                                            return
                                                $enrolment
                                                    ->sectionOffering
                                                    ?->section
                                                    ?->section_name;
                                        }
                                    )
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ?? collect();

                        @endphp


                        <tr>

                            <td
                                class="px-6 py-5"
                            >

                                <p
                                    class="font-bold
                                           text-slate-900"
                                >
                                    {{ $student?->first_name }}
                                    {{ $student?->last_name }}
                                </p>

                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-400"
                                >
                                    {{ $student?->external_id ?? '—' }}
                                </p>

                            </td>


                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $classNames->isNotEmpty()
                                        ? $classNames->implode(', ')
                                        : '—'
                                }}
                            </td>


                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-700"
                            >
                                {{
                                    $leave
                                        ->start_date
                                        ->format('d M Y')
                                }}
                            </td>


                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-700"
                            >
                                {{
                                    $leave
                                        ->expected_return_date
                                        ->format('d M Y')
                                }}
                            </td>


                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-700"
                            >
                                {{
                                    $leave->actual_return_date
                                        ? $leave
                                            ->actual_return_date
                                            ->format('d M Y')
                                        : '—'
                                }}
                            </td>


                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
                            >
                                {{ $leave->reason ?? '—' }}
                            </td>


                            <td
                                class="px-6 py-5
                                       text-center"
                            >

                                <a
                                    href="{{ route(
                                        'admin.leave.show',
                                        $leave
                                    ) }}"
                                    class="inline-flex h-10
                                           items-center
                                           rounded-xl
                                           border
                                           border-blue-300
                                           px-5
                                           text-xs
                                           font-semibold
                                           text-blue-700"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-14
                                       text-center
                                       text-sm
                                       text-slate-500"
                            >
                                No leave history found.
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
