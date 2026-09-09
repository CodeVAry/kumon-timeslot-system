@extends('layouts.admin')

@section('title', 'Leave Management')

@section('page-title', 'Leave Management')


@php

    $breadcrumbs = [
        [
            'label' => 'Leave Management',
            'url' => null,
        ],
    ];


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


@section('content')

<div class="space-y-6">


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

            <h1
                class="text-4xl
                       font-bold
                       tracking-tight
                       text-slate-900"
            >
                Leave Management
            </h1>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                View student leave information,
                upcoming leave and return dates.
            </p>

        </div>


        <div
            class="flex
                   flex-wrap
                   gap-3"
        >

            <a
                href="{{ route(
                    'admin.leave.history'
                ) }}"
                class="inline-flex
                       h-11
                       items-center
                       justify-center
                       rounded-xl
                       border
                       border-blue-300
                       bg-white
                       px-5
                       text-sm
                       font-semibold
                       text-blue-700
                       transition
                       hover:bg-blue-50"
            >
                Leave History
            </a>


            @if (
                auth()
                    ->user()
                    ->hasPermission(
                        'leave.create'
                    )
            )

                <a
                    href="{{ route(
                        'admin.leave.create'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           gap-2
                           rounded-xl
                           bg-blue-600
                           px-5
                           text-sm
                           font-semibold
                           text-white
                           shadow-sm
                           transition
                           hover:bg-blue-700"
                >
                    + Add New Leave
                </a>

            @endif

        </div>

    </div>



    {{-- =========================================================
        MESSAGES
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
        SUMMARY
    ========================================================== --}}

    <div
        class="grid
               gap-4
               sm:grid-cols-2
               xl:grid-cols-4"
    >


        {{-- Current --}}
        <div
            class="rounded-2xl
                   border
                   border-blue-100
                   bg-blue-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Current Leave
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-blue-700"
            >
                {{ $currentCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Students currently on leave
            </p>

        </div>



        {{-- Upcoming --}}
        <div
            class="rounded-2xl
                   border
                   border-purple-100
                   bg-purple-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Upcoming Leave
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-purple-700"
            >
                {{ $upcomingCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Future leave already recorded
            </p>

        </div>



        {{-- Returning Soon --}}
        <div
            class="rounded-2xl
                   border
                   border-green-100
                   bg-green-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Returning Soon
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-green-600"
            >
                {{ $returningSoonCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Returning within 7 days
            </p>

        </div>



        {{-- History --}}
        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Leave History
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-800"
            >
                {{ $historyCount }}
            </p>


            <p
                class="mt-1
                       text-xs
                       text-slate-500"
            >
                Completed or cancelled records
            </p>

        </div>

    </div>



    {{-- =========================================================
        LEAVE TABLE
    ========================================================== --}}

    <section
        class="overflow-hidden
               rounded-2xl
               border
               border-slate-200
               bg-white
               shadow-sm"
    >


        {{-- Tabs --}}
        <div
            class="flex
                   overflow-x-auto
                   border-b
                   border-slate-200"
        >

            <a
                href="{{ route(
                    'admin.leave.index',
                    [
                        'tab' => 'current',
                    ]
                ) }}"
                class="whitespace-nowrap
                       px-7 py-4
                       text-sm
                       font-semibold
                       transition
                       {{
                           $tab === 'current'
                               ? 'border-b-2 border-blue-600 text-blue-700'
                               : 'text-slate-500 hover:text-blue-600'
                       }}"
            >
                Current Leave

                @if ($currentCount > 0)

                    <span
                        class="ml-2
                               rounded-full
                               bg-blue-100
                               px-2 py-0.5
                               text-xs
                               font-bold
                               text-blue-700"
                    >
                        {{ $currentCount }}
                    </span>

                @endif

            </a>


            <a
                href="{{ route(
                    'admin.leave.index',
                    [
                        'tab' => 'upcoming',
                    ]
                ) }}"
                class="whitespace-nowrap
                       px-7 py-4
                       text-sm
                       font-semibold
                       transition
                       {{
                           $tab === 'upcoming'
                               ? 'border-b-2 border-purple-600 text-purple-700'
                               : 'text-slate-500 hover:text-purple-600'
                       }}"
            >
                Upcoming Leave

                @if ($upcomingCount > 0)

                    <span
                        class="ml-2
                               rounded-full
                               bg-purple-100
                               px-2 py-0.5
                               text-xs
                               font-bold
                               text-purple-700"
                    >
                        {{ $upcomingCount }}
                    </span>

                @endif

            </a>


            <a
                href="{{ route(
                    'admin.leave.history'
                ) }}"
                class="whitespace-nowrap
                       px-7 py-4
                       text-sm
                       font-semibold
                       text-slate-500
                       transition
                       hover:text-blue-600"
            >
                Leave History
            </a>

        </div>



        {{-- Search --}}
        <div
            class="border-b
                   border-slate-100
                   p-5"
        >

            <form
                method="GET"
                action="{{ route(
                    'admin.leave.index'
                ) }}"
                class="flex
                       flex-col
                       gap-3
                       md:flex-row"
            >

                <input
                    type="hidden"
                    name="tab"
                    value="{{ $tab }}"
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
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Search
                </button>


                @if ($search !== '')

                    <a
                        href="{{ route(
                            'admin.leave.index',
                            [
                                'tab' => $tab,
                            ]
                        ) }}"
                        class="inline-flex
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-slate-300
                               px-5 py-2.5
                               text-sm
                               font-semibold
                               text-slate-600
                               hover:bg-slate-50"
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

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Student
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Enrolled Classes
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Start Date
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Expected Return
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                            Duration
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                            Homework
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($leaves as $leave)

                        @php

                            $student =
                                $leave->student;


                            $duration =
                                $leave
                                    ->start_date
                                    ->diffInDays(
                                        $leave
                                            ->expected_return_date
                                    );


                            $classNames =
                                $student
                                    ?->enrolments
                                    ?->map(
                                        function ($enrolment) {

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
                                ??
                                collect();

                        @endphp


                        <tr class="transition hover:bg-blue-50/30">

                            <td class="px-6 py-5">

                                <p class="font-bold text-slate-900">
                                    {{ $student?->first_name }}
                                    {{ $student?->last_name }}
                                </p>


                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $student?->external_id ?? '—' }}
                                </p>

                            </td>


                            <td class="px-6 py-5 text-sm text-slate-600">

                                @if ($classNames->isNotEmpty())

                                    {{ $classNames->implode(', ') }}

                                @else

                                    —

                                @endif

                            </td>


                            <td class="whitespace-nowrap px-6 py-5 text-sm text-slate-700">
                                {{ $leave->start_date->format('d M Y') }}
                            </td>


                            <td class="whitespace-nowrap px-6 py-5 text-sm text-slate-700">
                                {{ $leave->expected_return_date->format('d M Y') }}
                            </td>


                            <td class="px-6 py-5 text-center text-sm text-slate-700">

                                {{ $duration }}

                                {{
                                    $duration === 1
                                        ? 'day'
                                        : 'days'
                                }}

                            </td>


                            <td class="px-6 py-5 text-sm text-slate-600">

                                {{
                                    $homeworkLabels[
                                        $leave->homework_requirement
                                    ]
                                    ??
                                    $leave->homework_requirement
                                }}

                            </td>


                            <td class="px-6 py-5 text-center">

                                <div
                                    class="flex
                                           flex-wrap
                                           items-center
                                           justify-center
                                           gap-2"
                                >

                                    <a
                                        href="{{ route(
                                            'admin.leave.show',
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
                                        View
                                    </a>


                                    @if (
                                        auth()
                                            ->user()
                                            ->hasPermission(
                                                'leave.edit'
                                            )
                                    )

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
                                                   border-slate-300
                                                   bg-white
                                                   px-4
                                                   text-xs
                                                   font-semibold
                                                   text-slate-700
                                                   hover:bg-slate-50"
                                        >
                                            Edit
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-16 text-center"
                            >

                                <p class="font-semibold text-slate-700">

                                    @if ($tab === 'current')

                                        No students are currently on leave

                                    @else

                                        No upcoming leave records

                                    @endif

                                </p>


                                <p class="mt-1 text-sm text-slate-500">
                                    Student leave information will appear here.
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
