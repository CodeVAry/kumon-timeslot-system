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


    /*
    |--------------------------------------------------------------------------
    | Only changed fields
    |--------------------------------------------------------------------------
    */

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
            ->filter(
                function ($field) use (
                    $oldValues,
                    $newValues
                ) {

                    return
                        data_get(
                            $oldValues,
                            $field
                        )
                        !==
                        data_get(
                            $newValues,
                            $field
                        );
                }
            )
            ->values();


    /*
    |--------------------------------------------------------------------------
    | Friendly Field Names
    |--------------------------------------------------------------------------
    */

    $fieldLabels = [

        'external_id' =>
            'Student ID',

        'student_status_id' =>
            'Student Status',

        'first_name' =>
            'First Name',

        'last_name' =>
            'Last Name',

        'date_of_birth' =>
            'Date of Birth',

        'section_offering_id' =>
            'Class Offering',

        'homework_requirement' =>
            'Homework Requirement',

        'review_note' =>
            'Centre Homework Instructions',

        'is_active' =>
            'Active',

        'max_seats' =>
            'Maximum Seats',
    ];


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


    /*
    |--------------------------------------------------------------------------
    | Browser
    |--------------------------------------------------------------------------
    */

    $browser =
        $auditLog->user_agent
        ?: 'Unknown device';

@endphp


<div
    class="min-h-full
           rounded-[28px]
           bg-slate-50
           p-5
           sm:p-6
           lg:p-8"
>

    <div
        class="mx-auto
               max-w-7xl"
    >


        {{-- =====================================================
            HEADER
        ====================================================== --}}

        <div
            class="flex
                   flex-col
                   gap-4
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div>

                <a
                    href="{{ route(
                        'admin.audit-logs.index'
                    ) }}"
                    class="text-sm
                           font-semibold
                           text-blue-600"
                >
                    ← Back to Audit Logs
                </a>


                <h1
                    class="mt-3
                           text-3xl
                           font-bold
                           text-slate-900"
                >
                    Audit Log Details
                </h1>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Review what changed,
                    who changed it and when.
                </p>

            </div>

        </div>



        {{-- =====================================================
            TOP SUMMARY
        ====================================================== --}}

        <div
            class="mt-6
                   grid
                   gap-4
                   sm:grid-cols-2
                   xl:grid-cols-5"
        >


            {{-- Action --}}
            <div
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-5
                       shadow-sm"
            >

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-slate-400"
                >
                    Action
                </p>


                <span
                    class="mt-3
                           inline-flex
                           rounded-full
                           px-3 py-1
                           text-xs
                           font-bold
                           {{ $actionClass }}"
                >
                    {{
                        ucfirst(
                            $auditLog->action
                        )
                    }}
                </span>

            </div>



            {{-- Module --}}
            <div
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-5
                       shadow-sm"
            >

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-slate-400"
                >
                    Module
                </p>


                <p
                    class="mt-3
                           font-bold
                           text-slate-900"
                >
                    {{
                        class_basename(
                            $auditLog
                                ->entity_type
                        )
                    }}
                </p>

            </div>



            {{-- User --}}
            <div
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-5
                       shadow-sm"
            >

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-slate-400"
                >
                    Performed By
                </p>


                <p
                    class="mt-3
                           font-bold
                           text-slate-900"
                >
                    {{
                        $auditLog
                            ->user_name
                        ??
                        'Unknown User'
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

            </div>



            {{-- Date --}}
            <div
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-5
                       shadow-sm"
            >

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-slate-400"
                >
                    Date & Time
                </p>


                <p
                    class="mt-3
                           font-bold
                           text-slate-900"
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

            </div>



            {{-- Retention --}}
            <div
                class="rounded-2xl
                       border
                       border-blue-200
                       bg-blue-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-blue-500"
                >
                    Retention
                </p>


                <p
                    class="mt-3
                           font-bold
                           text-blue-800"
                >
                    4 Weeks
                </p>


                <p
                    class="mt-1
                           text-xs
                           text-blue-600"
                >
                    Automatically deleted
                    after 28 days
                </p>

            </div>

        </div>



        {{-- =====================================================
            INFORMATION
        ====================================================== --}}

        <section
            class="mt-6
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-6
                   shadow-sm"
        >

            <h2
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                Activity Summary
            </h2>



            <div
                class="mt-5
                       grid
                       gap-x-10
                       gap-y-5
                       md:grid-cols-2"
            >


                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               text-slate-400"
                    >
                        Entity Type
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
                    </p>

                </div>



                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               text-slate-400"
                    >
                        Reference
                    </p>


                    <p
                        class="mt-1
                               font-semibold
                               text-blue-700"
                    >
                        {{
                            $auditLog
                                ->entity_reference
                            ??
                            '—'
                        }}
                    </p>


                    @if (
                        class_basename(
                            $auditLog
                                ->entity_type
                        )
                        ===
                        'Student'
                    )

                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-400"
                        >
                            Manual Student ID
                        </p>

                    @endif

                </div>



                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               text-slate-400"
                    >
                        IP Address
                    </p>


                    <p
                        class="mt-1
                               font-semibold
                               text-slate-800"
                    >
                        {{
                            $auditLog
                                ->ip_address
                            ??
                            '—'
                        }}
                    </p>

                </div>



                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               text-slate-400"
                    >
                        Browser / Device
                    </p>


                    <p
                        class="mt-1
                               break-all
                               text-sm
                               text-slate-600"
                    >
                        {{ $browser }}
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
                           border
                           border-blue-100
                           bg-blue-50
                           p-4"
                >

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               text-blue-500"
                    >
                        Description
                    </p>


                    <p
                        class="mt-2
                               text-sm
                               text-blue-800"
                    >
                        {{
                            $auditLog
                                ->description
                        }}
                    </p>

                </div>

            @endif

        </section>



        {{-- =====================================================
            CHANGED FIELDS
        ====================================================== --}}

        <section
            class="mt-6
                   overflow-hidden
                   rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   shadow-sm"
        >

            <div
                class="border-b
                       border-slate-100
                       p-6"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Changed Fields
                </h2>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Only fields that actually changed
                    are displayed.
                </p>

            </div>



            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-slate-500"
                            >
                                Field
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-red-600"
                            >
                                Previous Value
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       text-xs
                                       font-bold
                                       uppercase
                                       text-green-600"
                            >
                                New Value
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


                                $label =
                                    $fieldLabels[$field]
                                    ??
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $field
                                        )
                                    );


                                $formatValue =
                                    function ($value) {

                                        if (
                                            is_null($value)
                                            ||
                                            $value === ''
                                        ) {
                                            return '—';
                                        }


                                        if (
                                            is_bool($value)
                                        ) {
                                            return
                                                $value
                                                    ? 'Yes'
                                                    : 'No';
                                        }


                                        if (
                                            is_array($value)
                                        ) {
                                            return implode(
                                                ', ',
                                                $value
                                            );
                                        }


                                        return $value;
                                    };

                            @endphp


                            <tr>

                                <td
                                    class="px-5 py-4
                                           font-semibold
                                           text-slate-700"
                                >
                                    {{ $label }}
                                </td>


                                <td
                                    class="px-5 py-4"
                                >

                                    <div
                                        class="rounded-lg
                                               bg-red-50
                                               px-3 py-2
                                               text-sm
                                               text-red-700"
                                    >
                                        {{
                                            $formatValue(
                                                $before
                                            )
                                        }}
                                    </div>

                                </td>


                                <td
                                    class="px-5 py-4"
                                >

                                    <div
                                        class="rounded-lg
                                               bg-green-50
                                               px-3 py-2
                                               text-sm
                                               text-green-700"
                                    >
                                        {{
                                            $formatValue(
                                                $after
                                            )
                                        }}
                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="3"
                                    class="px-6 py-14
                                           text-center
                                           text-sm
                                           text-slate-500"
                                >

                                    @if (
                                        $auditLog
                                            ->action
                                        ===
                                        'created'
                                    )

                                        Record created.
                                        No previous values are available.

                                    @elseif (
                                        $auditLog
                                            ->action
                                        ===
                                        'deleted'
                                    )

                                        Record deleted.
                                        No replacement values are available.

                                    @else

                                        No field changes available.

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>



        {{-- =====================================================
            BOTTOM
        ====================================================== --}}

        <div
            class="mt-6
                   flex
                   justify-end"
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
                       px-6
                       text-sm
                       font-semibold
                       text-slate-700
                       hover:bg-slate-50"
            >
                ← Back to Audit Logs
            </a>

        </div>

    </div>

</div>

@endsection
