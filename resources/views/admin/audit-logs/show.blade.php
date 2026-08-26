@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('page-title', 'Audit Log Details')


@section('content')

@php

    $oldValues =
        $auditLog->old_values
        ?? [];

    $newValues =
        $auditLog->new_values
        ?? [];

    $fields =
        collect(
            array_merge(
                array_keys(
                    $oldValues
                ),
                array_keys(
                    $newValues
                )
            )
        )
            ->unique()
            ->values();

@endphp


<div
    class="rounded-[28px]
           bg-cyan-50/70
           p-6"
>

    <div
        class="mx-auto
               max-w-6xl"
    >

        <a
            href="{{ route(
                'admin.audit-logs.index'
            ) }}"
            class="text-sm
                   font-semibold
                   text-cyan-700
                   hover:text-cyan-900"
        >
            ← Back to Audit Logs
        </a>



        <div
            class="mt-5
                   rounded-[26px]
                   border
                   border-cyan-100
                   bg-white
                   p-6
                   shadow-sm"
        >

            <h1
                class="text-3xl
                       font-bold
                       text-slate-900"
            >
                Change Details
            </h1>


            <div
                class="mt-6
                       grid
                       gap-5
                       md:grid-cols-2"
            >

                <div>

                    <p
                        class="text-xs
                               font-semibold
                               uppercase
                               text-slate-400"
                    >
                        Changed By
                    </p>


                    <p
                        class="mt-1
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            $auditLog->user_name
                            ?? 'Unknown User'
                        }}
                    </p>

                </div>



                <div>

                    <p
                        class="text-xs
                               font-semibold
                               uppercase
                               text-slate-400"
                    >
                        Date & Time
                    </p>


                    <p
                        class="mt-1
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            $auditLog
                                ->created_at
                                ?->format(
                                    'd M Y, g:i A'
                                )
                        }}
                    </p>

                </div>



                <div>

                    <p
                        class="text-xs
                               font-semibold
                               uppercase
                               text-slate-400"
                    >
                        Action
                    </p>


                    <p
                        class="mt-1
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            ucfirst(
                                $auditLog->action
                            )
                        }}
                    </p>

                </div>



                <div>

                    <p
                        class="text-xs
                               font-semibold
                               uppercase
                               text-slate-400"
                    >
                        Entity
                    </p>


                    <p
                        class="mt-1
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            class_basename(
                                $auditLog
                                    ->entity_type
                            )
                        }}

                        @if (
                            $auditLog
                                ->entity_id
                        )

                            #{{ $auditLog->entity_id }}

                        @endif
                    </p>

                </div>

            </div>



            @if (
                $auditLog
                    ->description
            )

                <div
                    class="mt-6
                           rounded-xl
                           bg-slate-50
                           p-4"
                >

                    <p
                        class="text-sm
                               text-slate-600"
                    >
                        {{
                            $auditLog
                                ->description
                        }}
                    </p>

                </div>

            @endif

        </div>



        {{-- =====================================================
            BEFORE / AFTER
        ====================================================== --}}

        <div
            class="mt-6
                   overflow-hidden
                   rounded-[26px]
                   border
                   border-cyan-100
                   bg-white
                   shadow-sm"
        >

            <div
                class="border-b
                       border-slate-100
                       p-6"
            >

                <h2
                    class="text-2xl
                           font-bold
                           text-slate-900"
                >
                    Changes
                </h2>

            </div>



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
                                Field
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-red-700"
                            >
                                Before
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       text-green-700"
                            >
                                After
                            </th>

                        </tr>

                    </thead>



                    <tbody
                        class="divide-y
                               divide-slate-100"
                    >

                        @forelse (
                            $fields
                            as $field
                        )

                            @php

                                $before =
                                    data_get(
                                        $oldValues,
                                        $field
                                    );

                                $after =
                                    data_get(
                                        $newValues,
                                        $field
                                    );

                            @endphp


                            <tr>

                                <td
                                    class="px-5 py-4
                                           font-semibold
                                           text-slate-700"
                                >
                                    {{
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $field
                                            )
                                        )
                                    }}
                                </td>


                                <td
                                    class="px-5 py-4
                                           text-sm
                                           text-red-700"
                                >
                                    {{
                                        is_array(
                                            $before
                                        )
                                            ? json_encode(
                                                $before
                                            )
                                            : (
                                                $before
                                                ?? '—'
                                            )
                                    }}
                                </td>


                                <td
                                    class="px-5 py-4
                                           text-sm
                                           text-green-700"
                                >
                                    {{
                                        is_array(
                                            $after
                                        )
                                            ? json_encode(
                                                $after
                                            )
                                            : (
                                                $after
                                                ?? '—'
                                            )
                                    }}
                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="3"
                                    class="px-6 py-12
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >
                                    No field changes available.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
