@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('page-title', 'Audit Logs')


@section('content')

<div
    class="rounded-[28px]
           bg-cyan-50/70
           p-6"
>

    {{-- =========================================================
        HEADER
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
                       text-slate-900"
            >
                Audit Logs
            </h1>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                Review changes made by authorised staff.
            </p>

        </div>

    </div>



    {{-- =========================================================
        FILTER
    ========================================================== --}}

    <form
        method="GET"
        action="{{ route(
            'admin.audit-logs.index'
        ) }}"
        class="mt-6
               grid
               gap-4
               rounded-2xl
               border
               border-cyan-100
               bg-white
               p-5
               md:grid-cols-3"
    >

        <div>

            <label
                for="search"
                class="mb-2
                       block
                       text-sm
                       font-semibold
                       text-slate-700"
            >
                Search
            </label>


            <input
                id="search"
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="User, entity or description"
                class="h-11
                       w-full
                       rounded-xl
                       border
                       border-slate-300
                       px-4"
            >

        </div>



        <div>

            <label
                for="action"
                class="mb-2
                       block
                       text-sm
                       font-semibold
                       text-slate-700"
            >
                Action
            </label>


            <select
                id="action"
                name="action"
                class="h-11
                       w-full
                       rounded-xl
                       border
                       border-slate-300
                       px-4"
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



        <div
            class="flex
                   items-end"
        >

            <button
                type="submit"
                class="h-11
                       w-full
                       rounded-xl
                       bg-cyan-500
                       px-5
                       text-sm
                       font-semibold
                       text-white
                       hover:bg-cyan-600"
            >
                Apply Filter
            </button>

        </div>

    </form>



    {{-- =========================================================
        AUDIT TABLE
    ========================================================== --}}

    <div
        class="mt-6
               overflow-hidden
               rounded-2xl
               border
               border-cyan-100
               bg-white"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead
                    class="bg-cyan-50"
                >

                    <tr>

                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-semibold
                                   uppercase
                                   text-cyan-800"
                        >
                            Date & Time
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-semibold
                                   uppercase
                                   text-cyan-800"
                        >
                            User
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-semibold
                                   uppercase
                                   text-cyan-800"
                        >
                            Action
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-semibold
                                   uppercase
                                   text-cyan-800"
                        >
                            Entity
                        </th>


                        <th
                            class="px-5 py-4
                                   text-left
                                   text-xs
                                   font-semibold
                                   uppercase
                                   text-cyan-800"
                        >
                            Description
                        </th>


                        <th
                            class="px-5 py-4
                                   text-center
                                   text-xs
                                   font-semibold
                                   uppercase
                                   text-cyan-800"
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
                        as $log
                    )

                        @php

                            $entityName =
                                class_basename(
                                    $log->entity_type
                                );


                            $actionStyle =
                                match (
                                    $log->action
                                ) {
                                    'created' =>
                                        'bg-green-100 text-green-700',

                                    'updated' =>
                                        'bg-amber-100 text-amber-700',

                                    'deleted' =>
                                        'bg-red-100 text-red-700',

                                    default =>
                                        'bg-slate-100 text-slate-700',
                                };

                        @endphp


                        <tr
                            class="hover:bg-cyan-50/40"
                        >

                            <td
                                class="whitespace-nowrap
                                       px-5 py-4
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $log
                                        ->created_at
                                        ?->format(
                                            'd M Y, g:i A'
                                        )
                                }}
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
                                        $log->user_name
                                        ?? 'Unknown User'
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    {{
                                        $log->user_email
                                        ?? '—'
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
                                           font-semibold
                                           {{ $actionStyle }}"
                                >
                                    {{
                                        ucfirst(
                                            $log->action
                                        )
                                    }}
                                </span>

                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-700"
                            >
                                {{ $entityName }}

                                @if ($log->entity_id)

                                    <span
                                        class="text-slate-400"
                                    >
                                        #{{ $log->entity_id }}
                                    </span>

                                @endif

                            </td>


                            <td
                                class="px-5 py-4
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $log->description
                                    ?? '—'
                                }}
                            </td>


                            <td
                                class="px-5 py-4
                                       text-center"
                            >

                                <a
                                    href="{{ route(
                                        'admin.audit-logs.show',
                                        $log
                                    ) }}"
                                    class="font-semibold
                                           text-cyan-700
                                           hover:text-cyan-900"
                                >
                                    View
                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-16
                                       text-center
                                       text-sm
                                       text-slate-500"
                            >
                                No audit records found.
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
                       p-5"
            >
                {{
                    $auditLogs
                        ->links()
                }}
            </div>

        @endif

    </div>

</div>

@endsection
