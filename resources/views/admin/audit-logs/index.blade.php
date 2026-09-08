@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('page-title', 'Audit Logs')


@section('content')

<div
    class="min-h-full
           rounded-[28px]
           bg-slate-50
           p-5
           sm:p-6
           lg:p-8"
>


    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div
        class="flex
               flex-col
               gap-4
               lg:flex-row
               lg:items-center
               lg:justify-between"
    >

        <div>

            <h1
                class="text-3xl
                       font-bold
                       tracking-tight
                       text-slate-900"
            >
                Audit Logs
            </h1>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Track and review staff activity
                from the last four weeks.
            </p>

        </div>


        <div
            class="inline-flex
                   items-center
                   gap-2
                   rounded-xl
                   border
                   border-blue-200
                   bg-blue-50
                   px-4 py-3
                   text-sm
                   font-medium
                   text-blue-700"
        >
            <span>
                ⏱
            </span>

            Logs are retained for 4 weeks
        </div>

    </div>



    {{-- =========================================================
        FILTERS
    ========================================================== --}}

    <section
        class="mt-6
               rounded-2xl
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <form
            method="GET"
            action="{{ route(
                'admin.audit-logs.index'
            ) }}"
        >

            <div
                class="grid
                       gap-4
                       md:grid-cols-2
                       xl:grid-cols-4"
            >


                {{-- Search --}}
                <div>

                    <label
                        class="mb-2
                               block
                               text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-slate-500"
                    >
                        Search
                    </label>


                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="User, student ID, description..."
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                </div>



                {{-- Action --}}
                <div>

                    <label
                        class="mb-2
                               block
                               text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-slate-500"
                    >
                        Action
                    </label>


                    <select
                        name="action"
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm"
                    >

                        <option value="">
                            All Actions
                        </option>


                        <option
                            value="created"
                            @selected(
                                $action === 'created'
                            )
                        >
                            Created
                        </option>


                        <option
                            value="updated"
                            @selected(
                                $action === 'updated'
                            )
                        >
                            Updated
                        </option>


                        <option
                            value="deleted"
                            @selected(
                                $action === 'deleted'
                            )
                        >
                            Deleted
                        </option>

                    </select>

                </div>



                {{-- Module --}}
                <div>

                    <label
                        class="mb-2
                               block
                               text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-slate-500"
                    >
                        Module
                    </label>


                    <select
                        name="entity"
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm"
                    >

                        <option value="">
                            All Modules
                        </option>


                        @foreach (
                            $entities
                            as $entityOption
                        )

                            <option
                                value="{{ $entityOption }}"
                                @selected(
                                    $entity
                                    ===
                                    $entityOption
                                )
                            >
                                {{
                                    class_basename(
                                        $entityOption
                                    )
                                }}
                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- Time Range --}}
                <div>

                    <label
                        class="mb-2
                               block
                               text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-slate-500"
                    >
                        Time Range
                    </label>


                    <select
                        name="time_range"
                        id="auditTimeRange"
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300
                               text-sm"
                    >

                        <option
                            value="today"
                            @selected(
                                $timeRange
                                ===
                                'today'
                            )
                        >
                            Today
                        </option>


                        <option
                            value="7_days"
                            @selected(
                                $timeRange
                                ===
                                '7_days'
                            )
                        >
                            Last 7 Days
                        </option>


                        <option
                            value="14_days"
                            @selected(
                                $timeRange
                                ===
                                '14_days'
                            )
                        >
                            Last 14 Days
                        </option>


                        <option
                            value="28_days"
                            @selected(
                                $timeRange
                                ===
                                '28_days'
                            )
                        >
                            Last 4 Weeks
                        </option>


                        <option
                            value="custom"
                            @selected(
                                $timeRange
                                ===
                                'custom'
                            )
                        >
                            Custom Range
                        </option>

                    </select>

                </div>

            </div>



            {{-- Custom Dates --}}
            <div
                id="customAuditDates"
                class="mt-4
                       grid
                       gap-4
                       md:grid-cols-2
                       {{
                           $timeRange === 'custom'
                               ? ''
                               : 'hidden'
                       }}"
            >

                <div>

                    <label
                        class="mb-2
                               block
                               text-xs
                               font-bold
                               uppercase
                               text-slate-500"
                    >
                        From Date
                    </label>


                    <input
                        type="date"
                        name="from_date"
                        value="{{ $fromDate }}"
                        min="{{
                            now()
                                ->subDays(28)
                                ->format('Y-m-d')
                        }}"
                        max="{{
                            now()->format('Y-m-d')
                        }}"
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>


                <div>

                    <label
                        class="mb-2
                               block
                               text-xs
                               font-bold
                               uppercase
                               text-slate-500"
                    >
                        To Date
                    </label>


                    <input
                        type="date"
                        name="to_date"
                        value="{{ $toDate }}"
                        min="{{
                            now()
                                ->subDays(28)
                                ->format('Y-m-d')
                        }}"
                        max="{{
                            now()->format('Y-m-d')
                        }}"
                        class="h-11
                               w-full
                               rounded-xl
                               border-slate-300"
                    >

                </div>

            </div>



            <div
                class="mt-5
                       flex
                       flex-wrap
                       justify-end
                       gap-3"
            >

                <a
                    href="{{ route(
                        'admin.audit-logs.index'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           px-5
                           text-sm
                           font-semibold
                           text-slate-600"
                >
                    Reset
                </a>


                <button
                    type="submit"
                    class="inline-flex
                           h-11
                           items-center
                           rounded-xl
                           bg-blue-600
                           px-6
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Apply Filters
                </button>

            </div>

        </form>

    </section>



    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div
        class="mt-6
               grid
               gap-4
               sm:grid-cols-2
               xl:grid-cols-4"
    >


        <div
            class="rounded-2xl
                   border
                   border-blue-200
                   bg-blue-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-blue-600"
            >
                Total Logs
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                {{
                    number_format(
                        $stats['total']
                    )
                }}
            </p>

        </div>



        <div
            class="rounded-2xl
                   border
                   border-green-200
                   bg-green-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-green-600"
            >
                Created
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                {{
                    number_format(
                        $stats['created']
                    )
                }}
            </p>

        </div>



        <div
            class="rounded-2xl
                   border
                   border-amber-200
                   bg-amber-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-amber-600"
            >
                Updated
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                {{
                    number_format(
                        $stats['updated']
                    )
                }}
            </p>

        </div>



        <div
            class="rounded-2xl
                   border
                   border-red-200
                   bg-red-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-red-600"
            >
                Deleted
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-slate-900"
            >
                {{
                    number_format(
                        $stats['deleted']
                    )
                }}
            </p>

        </div>

    </div>



    {{-- =========================================================
        TABLE
    ========================================================== --}}

    <section
        class="mt-6
               overflow-hidden
               rounded-2xl
               border
               border-slate-200
               bg-white
               shadow-sm"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead
                    class="border-b
                           border-slate-200
                           bg-slate-50"
                >

                    <tr>

                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Date & Time
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            User
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Action
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Module
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Reference
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Description
                        </th>


                        <th
                            class="px-5 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            View
                        </th>

                    </tr>

                </thead>



                <tbody
                    class="divide-y
                           divide-slate-100"
                >

                    @forelse (
                        $auditLogs
                        as $auditLog
                    )

                        @php

                            $actionClass =
                                match(
                                    $auditLog->action
                                ) {

                                    'created' =>
                                        'bg-green-100 text-green-700',

                                    'updated' =>
                                        'bg-amber-100 text-amber-700',

                                    'deleted' =>
                                        'bg-red-100 text-red-700',

                                    default =>
                                        'bg-slate-100 text-slate-600',
                                };

                        @endphp


                        <tr
                            class="transition
                                   hover:bg-slate-50"
                        >

                            <td
                                class="whitespace-nowrap
                                       px-5 py-4
                                       text-sm"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $auditLog
                                            ->created_at
                                            ->format(
                                                'd M Y'
                                            )
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    {{
                                        $auditLog
                                            ->created_at
                                            ->format(
                                                'g:i A'
                                            )
                                    }}
                                </p>

                            </td>



                            <td
                                class="px-5 py-4"
                            >

                                <p
                                    class="text-sm
                                           font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $auditLog
                                            ->user_name
                                        ??
                                        'Unknown'
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    {{
                                        $auditLog
                                            ->user_email
                                    }}
                                </p>

                            </td>



                            <td
                                class="px-5 py-4"
                            >

                                <span
                                    class="inline-flex
                                           rounded-full
                                           px-3 py-1
                                           text-xs
                                           font-bold
                                           {{ $actionClass }}"
                                >
                                    {{
                                        ucfirst(
                                            $auditLog
                                                ->action
                                        )
                                    }}
                                </span>

                            </td>



                            <td
                                class="px-5 py-4
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                {{
                                    class_basename(
                                        $auditLog
                                            ->entity_type
                                    )
                                }}
                            </td>



                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-blue-700"
                            >
                                {{
                                    $auditLog
                                        ->entity_reference
                                    ??
                                    '—'
                                }}
                            </td>



                            <td
                                class="max-w-xs
                                       px-5 py-4
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $auditLog
                                        ->description
                                }}
                            </td>



                            <td
                                class="px-5 py-4
                                       text-center"
                            >

                                <a
                                    href="{{ route(
                                        'admin.audit-logs.show',
                                        $auditLog
                                    ) }}"
                                    class="inline-flex
                                           h-9 w-9
                                           items-center
                                           justify-center
                                           rounded-lg
                                           border
                                           border-slate-300
                                           text-slate-600
                                           hover:bg-blue-50
                                           hover:text-blue-700"
                                    title="View details"
                                >
                                    👁
                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-16
                                       text-center
                                       text-sm
                                       text-slate-500"
                            >
                                No audit logs found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        @if (
            $auditLogs
                ->hasPages()
        )

            <div
                class="border-t
                       border-slate-100
                       px-5 py-4"
            >
                {{
                    $auditLogs
                        ->links()
                }}
            </div>

        @endif

    </section>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const range =
            document.getElementById(
                'auditTimeRange'
            );


        const custom =
            document.getElementById(
                'customAuditDates'
            );


        function updateCustomDates()
        {
            if (
                range.value
                ===
                'custom'
            ) {

                custom.classList.remove(
                    'hidden'
                );

            } else {

                custom.classList.add(
                    'hidden'
                );
            }
        }


        range.addEventListener(
            'change',
            updateCustomDates
        );


        updateCustomDates();
    }
);

</script>

@endsection
