@extends('layouts.admin')

@section('title', 'Student Reviews')

@section('page-title', 'Student Reviews')


@section('content')

<div class="mx-auto max-w-7xl">


    {{-- =========================================================
        MESSAGES
    ========================================================== --}}

    @if (session('success'))

        <div
            class="mb-5 rounded-xl
                   border border-green-200
                   bg-green-50
                   px-5 py-4
                   text-sm font-medium
                   text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif


    @if (session('error'))

        <div
            class="mb-5 rounded-xl
                   border border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm font-medium
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div
        class="mb-6 rounded-2xl
               border border-slate-200
               bg-white p-6
               shadow-sm"
    >

        <h1
            class="text-2xl
                   font-bold
                   text-slate-900"
        >
            Student Reviews
        </h1>


        <p
            class="mt-1
                   text-sm
                   text-slate-500"
        >
            Review Trial students, long absences
            and students approaching the
            six-month inactive limit.
        </p>

    </div>



    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div
        class="mb-6 grid
               gap-4
               md:grid-cols-3"
    >


        {{-- Trial --}}
        <div
            class="rounded-2xl
                   border border-purple-200
                   bg-purple-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-purple-700"
            >
                Trial Reviews
            </p>

            <p
                class="mt-2 text-3xl
                       font-bold
                       text-purple-900"
            >
                {{ $trialStudents->count() }}
            </p>

        </div>


        {{-- Absence --}}
        <div
            class="rounded-2xl
                   border border-red-200
                   bg-red-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-red-700"
            >
                Absence Reviews
            </p>

            <p
                class="mt-2 text-3xl
                       font-bold
                       text-red-900"
            >
                {{ $absenceReviews->count() }}
            </p>

        </div>


        {{-- Deletion --}}
        <div
            class="rounded-2xl
                   border border-amber-200
                   bg-amber-50
                   p-5"
        >

            <p
                class="text-sm
                       font-semibold
                       text-amber-700"
            >
                Deletion Warnings
            </p>

            <p
                class="mt-2 text-3xl
                       font-bold
                       text-amber-900"
            >
                {{ $deletionWarnings->count() }}
            </p>

        </div>

    </div>



    {{-- =========================================================
        TRIAL REVIEWS
    ========================================================== --}}

    <div
        class="mb-8 overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white shadow-sm"
    >

        <div
            class="border-b
                   border-slate-200
                   px-6 py-5"
        >

            <h2
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                Trial Reviews
            </h2>

            <p
                class="mt-1 text-sm
                       text-slate-500"
            >
                Decide whether each Trial student
                will continue.
            </p>

        </div>


        @forelse (
            $trialStudents
            as $student
        )

            <div
                class="border-b
                       border-slate-100
                       p-6 last:border-b-0"
            >

                <div
                    class="flex flex-col
                           gap-5
                           lg:flex-row
                           lg:items-center
                           lg:justify-between"
                >

                    <div>

                        <div
                            class="flex
                                   items-center
                                   gap-3"
                        >

                            <h3
                                class="text-lg
                                       font-bold
                                       text-slate-900"
                            >
                                {{ $student->first_name }}
                                {{ $student->last_name }}
                            </h3>


                            <span
                                class="rounded-full
                                       bg-purple-100
                                       px-3 py-1
                                       text-xs
                                       font-semibold
                                       text-purple-700"
                            >
                                Trial
                            </span>

                        </div>


                        <p
                            class="mt-1
                                   text-sm
                                   text-slate-500"
                        >
                            Student ID:
                            {{ $student->external_id }}
                        </p>


                        <p
                            class="mt-2
                                   text-sm
                                   text-slate-600"
                        >
                            Active classes:
                            <span class="font-semibold">
                                {{ $student->enrolments->count() }}
                            </span>
                        </p>

                    </div>


                    <div
                        class="flex flex-col
                               gap-3
                               sm:flex-row"
                    >

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.student-reviews.trial.reject',
                                $student
                            ) }}"
                            onsubmit="
                                return confirm(
                                    'Remove this Trial student from active classes and change status to Inactive?'
                                );
                            "
                        >

                            @csrf
                            @method('PATCH')


                            <button
                                type="submit"
                                class="h-11
                                       rounded-xl
                                       border
                                       border-red-200
                                       bg-white
                                       px-5
                                       text-sm
                                       font-semibold
                                       text-red-600
                                       hover:bg-red-50"
                            >
                                No, Remove from Class
                            </button>

                        </form>


                        <form
                            method="POST"
                            action="{{ route(
                                'admin.student-reviews.trial.continue',
                                $student
                            ) }}"
                        >

                            @csrf
                            @method('PATCH')


                            <button
                                type="submit"
                                class="h-11
                                       rounded-xl
                                       bg-blue-600
                                       px-6
                                       text-sm
                                       font-semibold
                                       text-white
                                       hover:bg-blue-700"
                            >
                                Yes, Continue
                            </button>

                        </form>

                    </div>

                </div>

            </div>


        @empty

            <div
                class="p-8
                       text-center
                       text-sm
                       text-slate-500"
            >
                No Trial students require review.
            </div>

        @endforelse

    </div>



    {{-- =========================================================
        30 DAY ABSENCE REVIEWS
    ========================================================== --}}

    <div
        class="mb-8 overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white shadow-sm"
    >

        <div
            class="border-b
                   border-slate-200
                   px-6 py-5"
        >

            <h2
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                Removal Review
            </h2>

            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Students continuously marked
                absent for at least 30 days.
            </p>

        </div>


        @forelse (
            $absenceReviews
            as $review
        )

            @php

                $student =
                    $review['student'];

            @endphp


            <div
                class="border-b
                       border-slate-100
                       p-6
                       last:border-b-0"
            >

                <div
                    class="mb-5
                           rounded-xl
                           border
                           border-red-200
                           bg-red-50
                           px-5 py-4"
                >

                    <p
                        class="font-bold
                               text-red-700"
                    >
                        Admin action required
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-red-600"
                    >
                        {{ $student->first_name }}
                        {{ $student->last_name }}

                        has been absent for

                        <strong>
                            {{ $review['absence_days'] }}
                            days.
                        </strong>
                    </p>

                </div>


                <div
                    class="grid gap-5
                           lg:grid-cols-2"
                >

                    <div>

                        <h3
                            class="text-lg
                                   font-bold
                                   text-slate-900"
                        >
                            {{ $student->first_name }}
                            {{ $student->last_name }}
                        </h3>


                        <p
                            class="mt-1
                                   text-sm
                                   text-slate-500"
                        >
                            Student ID:
                            {{ $student->external_id }}
                        </p>


                        <div
                            class="mt-4
                                   space-y-2
                                   text-sm"
                        >

                            <p>
                                <span
                                    class="font-semibold
                                           text-slate-600"
                                >
                                    Absence started:
                                </span>

                                {{
                                    $review[
                                        'absence_start'
                                    ]->format(
                                        'd M Y'
                                    )
                                }}
                            </p>


                            <p>
                                <span
                                    class="font-semibold
                                           text-slate-600"
                                >
                                    Duration:
                                </span>

                                {{
                                    $review[
                                        'absence_days'
                                    ]
                                }}
                                days
                            </p>


                            <p>
                                <span
                                    class="font-semibold
                                           text-slate-600"
                                >
                                    Current status:
                                </span>

                                Active
                            </p>

                        </div>

                    </div>



                    {{-- Classes --}}
                    <div>

                        <h4
                            class="mb-3
                                   text-sm
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-500"
                        >
                            Current Classes
                        </h4>


                        @forelse (
                            $student->enrolments
                            as $enrolment
                        )

                            <div
                                class="mb-2
                                       rounded-xl
                                       bg-slate-50
                                       px-4 py-3"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $enrolment
                                            ->sectionOffering
                                            ?->section
                                            ?->section_name
                                        ??
                                        'Class'
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >
                                    {{
                                        $enrolment
                                            ->sectionOffering
                                            ?->day
                                            ?->day_name
                                        ??
                                        '—'
                                    }}

                                    @if (
                                        $enrolment
                                            ->sectionOffering
                                            ?->start_time
                                    )

                                        ·

                                        {{
                                            \Carbon\Carbon::parse(
                                                $enrolment
                                                    ->sectionOffering
                                                    ->start_time
                                            )->format(
                                                'g:i A'
                                            )
                                        }}

                                    @endif
                                </p>

                            </div>


                        @empty

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                No active class.
                            </p>

                        @endforelse

                    </div>

                </div>


                <div
                    class="mt-6 flex
                           flex-col gap-3
                           border-t
                           border-slate-200
                           pt-5
                           sm:flex-row
                           sm:justify-end"
                >

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.student-reviews.absence.keep',
                            $student
                        ) }}"
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="h-11
                                   rounded-xl
                                   border
                                   border-blue-200
                                   bg-white
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-blue-700"
                        >
                            Keep Student
                        </button>

                    </form>


                    <form
                        method="POST"
                        action="{{ route(
                            'admin.student-reviews.absence.remove',
                            $student
                        ) }}"
                        onsubmit="
                            return confirm(
                                'Remove this student from all active classes and change status to Inactive?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="h-11
                                   rounded-xl
                                   bg-red-600
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-red-700"
                        >
                            Remove from Class
                        </button>

                    </form>

                </div>

            </div>


        @empty

            <div
                class="p-8
                       text-center
                       text-sm
                       text-slate-500"
            >
                No students currently require
                an absence removal review.
            </div>

        @endforelse

    </div>



    {{-- =========================================================
        SIX MONTH DELETION WARNING
    ========================================================== --}}

    <div
        class="overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white shadow-sm"
    >

        <div
            class="border-b
                   border-slate-200
                   px-6 py-5"
        >

            <h2
                class="text-xl
                       font-bold
                       text-slate-900"
            >
                Inactive Student Deletion Review
            </h2>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Warning begins one month before
                the six-month inactive limit.
            </p>

        </div>


        @forelse (
            $deletionWarnings
            as $warning
        )

            @php

                $student =
                    $warning['student'];

            @endphp


            <div
                class="border-b
                       border-slate-100
                       p-6
                       last:border-b-0"
            >

                <div
                    class="flex flex-col
                           gap-5
                           lg:flex-row
                           lg:items-center
                           lg:justify-between"
                >

                    <div>

                        <div
                            class="flex flex-wrap
                                   items-center
                                   gap-3"
                        >

                            <h3
                                class="text-lg
                                       font-bold
                                       text-slate-900"
                            >
                                {{ $student->first_name }}
                                {{ $student->last_name }}
                            </h3>


                            @if (
                                $warning[
                                    'requires_deletion_review'
                                ]
                            )

                                <span
                                    class="rounded-full
                                           bg-red-100
                                           px-3 py-1
                                           text-xs
                                           font-semibold
                                           text-red-700"
                                >
                                    Deletion Required
                                </span>

                            @else

                                <span
                                    class="rounded-full
                                           bg-amber-100
                                           px-3 py-1
                                           text-xs
                                           font-semibold
                                           text-amber-700"
                                >
                                    Deletion Warning
                                </span>

                            @endif

                        </div>


                        <div
                            class="mt-3
                                   space-y-1
                                   text-sm
                                   text-slate-600"
                        >

                            <p>
                                Inactive since:
                                <strong>
                                    {{
                                        $warning[
                                            'inactive_since'
                                        ]->format(
                                            'd M Y'
                                        )
                                    }}
                                </strong>
                            </p>


                            <p>
                                Six-month date:
                                <strong>
                                    {{
                                        $warning[
                                            'deletion_date'
                                        ]->format(
                                            'd M Y'
                                        )
                                    }}
                                </strong>
                            </p>


                            @if (
                                !$warning[
                                    'requires_deletion_review'
                                ]
                            )

                                <p>
                                    {{
                                        $warning[
                                            'days_until_deletion'
                                        ]
                                    }}
                                    days remaining.
                                </p>

                            @endif

                        </div>

                    </div>


                    <div
                        class="flex flex-col
                               gap-3
                               sm:flex-row"
                    >

                        <a
                            href="{{ route(
                                'admin.students.show',
                                $student
                            ) }}"
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-slate-300
                                   bg-white
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-slate-700"
                        >
                            View Student
                        </a>


                        @if (
                            $warning[
                                'requires_deletion_review'
                            ]
                        )

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.student-reviews.delete',
                                    $student
                                ) }}"
                                onsubmit="
                                    return confirm(
                                        'This will permanently delete the student from the database. Continue?'
                                    );
                                "
                            >

                                @csrf
                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="h-11
                                           rounded-xl
                                           bg-red-600
                                           px-6
                                           text-sm
                                           font-semibold
                                           text-white
                                           hover:bg-red-700"
                                >
                                    Delete Student
                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            </div>


        @empty

            <div
                class="p-8
                       text-center
                       text-sm
                       text-slate-500"
            >
                No inactive students are currently
                approaching the six-month limit.
            </div>

        @endforelse

    </div>

</div>

@endsection
