@extends('layouts.admin')

@section('title', 'Remove Class')

@section('page-title', 'Remove Class')

@section('content')

<div class="space-y-6">

    <a
        href="{{ route(
            'admin.students.show',
            $student
        ) }}"
        class="inline-flex
               text-sm font-semibold
               text-blue-600"
    >
        ← Back to Student Profile
    </a>


    <section
        class="rounded-2xl
               border border-slate-200
               bg-white p-6
               shadow-sm"
    >

        <h1
            class="text-2xl
                   font-bold
                   text-slate-900"
        >
            Remove Class
        </h1>

        <p
            class="mt-1 text-sm
                   text-slate-500"
        >
            Select the class to remove from

            {{ $student->first_name }}
            {{ $student->last_name }}.
        </p>

    </section>


    <section
        class="overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white shadow-sm"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-4
                                   text-left text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            Section
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            Day
                        </th>

                        <th
                            class="px-6 py-4
                                   text-left text-xs
                                   font-semibold uppercase
                                   text-slate-500"
                        >
                            Time
                        </th>

                        <th
                            class="px-6 py-4
                                   text-right text-xs
                                   font-semibold uppercase
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

                    @forelse ($enrolments as $enrolment)

                        @php
                            $offering =
                                $enrolment
                                    ->sectionOffering;
                        @endphp


                        <tr>

                            <td
                                class="px-6 py-5
                                       text-sm
                                       font-semibold
                                       text-slate-900"
                            >
                                {{
                                    $offering
                                        ?->section
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
                                        ?->day
                                        ?->day_name
                                    ?? '—'
                                }}
                            </td>


                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-700"
                            >

                                @if ($offering)

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

                                @else

                                    —

                                @endif

                            </td>


                            <td
                                class="px-6 py-5
                                       text-right"
                            >

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.student-enrolments.destroy',
                                        [
                                            'student' =>
                                                $student,

                                            'enrolment' =>
                                                $enrolment,
                                        ]
                                    ) }}"
                                    onsubmit="
                                        return confirm(
                                            'Are you sure you want to remove this class?'
                                        );
                                    "
                                >

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="inline-flex
                                               rounded-xl
                                               border
                                               border-red-200
                                               bg-red-50
                                               px-4 py-2
                                               text-sm
                                               font-semibold
                                               text-red-600
                                               hover:bg-red-100"
                                    >
                                        Remove
                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-6 py-14
                                       text-center
                                       text-sm
                                       text-slate-500"
                            >
                                This student has
                                no current classes.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection
