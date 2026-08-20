@extends('layouts.admin')

@section('title', 'Add Wishlist')

@section('page-title', 'Add Wishlist')


@php

    $breadcrumbs = [
        [
            'label' => 'Student Details',
            'url' => route(
                'admin.students.show',
                $enrolment->student
            ),
        ],
        [
            'label' => 'Wishlist',
            'url' => null,
        ],
    ];


    $current =
        $enrolment->sectionOffering;

@endphp


@section('content')

<div class="space-y-6">


    {{-- =====================================================
        BACK
    ====================================================== --}}

    <div>

        <a
            href="{{ route(
                'admin.students.show',
                $enrolment->student
            ) }}"
            class="inline-flex
                   items-center
                   gap-2
                   text-sm
                   font-semibold
                   text-blue-600
                   hover:text-blue-800"
        >
            ← Back to Student Details
        </a>

    </div>



    {{-- =====================================================
        ERRORS
    ====================================================== --}}

    @if ($errors->any())

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            <p
                class="font-semibold
                       text-red-700"
            >
                Please correct the following:
            </p>


            <div
                class="mt-2
                       space-y-1"
            >

                @foreach ($errors->all() as $error)

                    <p
                        class="text-sm
                               text-red-700"
                    >
                        {{ $error }}
                    </p>

                @endforeach

            </div>

        </div>

    @endif



    {{-- =====================================================
        MAIN CONTENT
    ====================================================== --}}

    <div
        class="grid
               gap-6
               xl:grid-cols-2"
    >


        {{-- =================================================
            STUDENT / CURRENT CLASS
        ================================================== --}}

        <section
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-6
                   shadow-sm"
        >

            <p
                class="text-xs
                       font-semibold
                       uppercase
                       text-blue-600"
            >
                Student
            </p>


            <h1
                class="mt-2
                       text-2xl
                       font-bold
                       text-slate-900"
            >
                {{ $enrolment->student?->first_name }}
                {{ $enrolment->student?->last_name }}
            </h1>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Student ID:

                {{
                    $enrolment
                        ->student
                        ?->external_id
                    ?? '—'
                }}
            </p>



            {{-- Current Class --}}
            <div
                class="mt-6
                       rounded-xl
                       border
                       border-blue-100
                       bg-blue-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-blue-600"
                >
                    Current Class
                </p>


                <p
                    class="mt-2
                           text-lg
                           font-bold
                           text-slate-900"
                >
                    {{
                        $current
                            ?->section
                            ?->section_name
                        ?? '—'
                    }}
                </p>


                @if ($current)

                    <p
                        class="mt-1
                               text-sm
                               text-slate-600"
                    >

                        {{
                            $current
                                ?->day
                                ?->day_name
                            ?? '—'
                        }}

                        <span
                            class="mx-1
                                   text-slate-300"
                        >
                            •
                        </span>

                        {{
                            \Carbon\Carbon::parse(
                                $current->start_time
                            )->format('g:i A')
                        }}

                        –

                        {{
                            \Carbon\Carbon::parse(
                                $current->end_time
                            )->format('g:i A')
                        }}

                    </p>

                @endif

            </div>



            {{-- Information --}}
            <div
                class="mt-5
                       rounded-xl
                       border
                       border-slate-200
                       bg-slate-50
                       p-4"
            >

                <p
                    class="text-sm
                           leading-6
                           text-slate-600"
                >
                    The student's current class will remain
                    unchanged while this wishlist request is active.
                    A seat is only used after the wishlist is approved.
                </p>

            </div>

        </section>



        {{-- =================================================
            PREFERRED CLASS TIME
        ================================================== --}}

        <section
            class="rounded-2xl
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
                Preferred Class Time
            </h2>


            <p
                class="mt-1
                       text-sm
                       text-slate-500"
            >
                Select the class time
                the student would prefer.
            </p>



            {{-- Existing Wishlist Information --}}
            @if ($existingWishlist)

                <div
                    class="mt-5
                           rounded-xl
                           border
                           border-purple-200
                           bg-purple-50
                           p-4"
                >

                    <p
                        class="font-semibold
                               text-purple-800"
                    >
                        Active wishlist already exists
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-purple-700"
                    >
                        The currently requested class
                        is selected below.
                        Select another class time
                        if you want to update it.
                    </p>


                    @if ($existingWishlist->sectionOffering)

                        <div
                            class="mt-3
                                   rounded-lg
                                   bg-white
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       font-semibold
                                       uppercase
                                       text-purple-500"
                            >
                                Current Wishlist
                            </p>


                            <p
                                class="mt-1
                                       font-semibold
                                       text-slate-800"
                            >

                                {{
                                    $existingWishlist
                                        ->sectionOffering
                                        ?->day
                                        ?->day_name
                                    ?? '—'
                                }}

                                <span
                                    class="mx-1
                                           text-slate-300"
                                >
                                    •
                                </span>

                                {{
                                    \Carbon\Carbon::parse(
                                        $existingWishlist
                                            ->sectionOffering
                                            ->start_time
                                    )->format('g:i A')
                                }}

                            </p>

                        </div>

                    @endif

                </div>

            @endif



            {{-- =================================================
                WISHLIST FORM
            ================================================== --}}

            <form
                method="POST"
                action="{{ route(
                    'admin.wishlist.store',
                    $enrolment
                ) }}"
                class="mt-6"
            >

                @csrf



                <div class="space-y-3">

                    @forelse ($availableOfferings as $offering)

                        @php

                            /*
                             * Confirmed students only.
                             * Wishlist students do not consume seats.
                             */
                            $confirmed =
                                $offering
                                    ->enrolments()
                                    ->where(
                                        'is_active',
                                        true
                                    )
                                    ->where(
                                        'is_wishlist',
                                        false
                                    )
                                    ->count();


                            $available =
                                max(
                                    0,
                                    $offering->max_seats
                                    -
                                    $confirmed
                                );


                            /*
                             * Previously selected wishlist.
                             */
                            $selectedOfferingId =
                                old(
                                    'section_offering_id',
                                    $existingWishlist
                                        ?->section_offering_id
                                );


                            $isSelected =
                                (string) $selectedOfferingId
                                ===
                                (string) $offering->id;

                        @endphp



                        <label
                            for="offering_{{ $offering->id }}"
                            class="flex
                                   cursor-pointer
                                   items-center
                                   justify-between
                                   gap-4
                                   rounded-xl
                                   border
                                   p-4
                                   transition

                                {{
                                    $isSelected
                                        ? 'border-purple-400 bg-purple-50'
                                        : 'border-slate-200 hover:border-blue-300 hover:bg-blue-50/40'
                                }}"
                        >


                            <div
                                class="flex
                                       items-center
                                       gap-4"
                            >

                                <input
                                    type="radio"
                                    name="section_offering_id"
                                    id="offering_{{ $offering->id }}"
                                    value="{{ $offering->id }}"

                                    @checked(
                                        $isSelected
                                    )

                                    required

                                    class="text-purple-600
                                           focus:ring-purple-500"
                                >



                                <div>

                                    <p
                                        class="font-semibold
                                               text-slate-800"
                                    >

                                        {{
                                            $offering
                                                ->day
                                                ?->day_name
                                            ?? '—'
                                        }}

                                        <span
                                            class="mx-1
                                                   text-slate-300"
                                        >
                                            •
                                        </span>

                                        {{
                                            \Carbon\Carbon::parse(
                                                $offering->start_time
                                            )->format('g:i A')
                                        }}

                                    </p>


                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-500"
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

                                    </p>

                                </div>

                            </div>



                            {{-- Availability --}}
                            <div
                                class="flex
                                       items-center
                                       gap-2"
                            >

                                @if ($isSelected)

                                    <span
                                        class="rounded-full
                                               bg-purple-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-purple-700"
                                    >
                                        Selected
                                    </span>

                                @endif


                                @if ($available > 0)

                                    <span
                                        class="rounded-full
                                               bg-green-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-green-700"
                                    >
                                        {{ $available }}

                                        {{
                                            $available === 1
                                                ? 'seat available'
                                                : 'seats available'
                                        }}
                                    </span>

                                @else

                                    <span
                                        class="rounded-full
                                               bg-yellow-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-yellow-700"
                                    >
                                        Full — Wishlist
                                    </span>

                                @endif

                            </div>

                        </label>


                    @empty

                        <div
                            class="rounded-xl
                                   border
                                   border-dashed
                                   border-slate-300
                                   p-8
                                   text-center"
                        >

                            <p
                                class="font-semibold
                                       text-slate-700"
                            >
                                No other class times available
                            </p>


                            <p
                                class="mt-1
                                       text-sm
                                       text-slate-500"
                            >
                                There are currently no other active
                                offerings for this class.
                            </p>

                        </div>

                    @endforelse

                </div>



                {{-- =================================================
                    ACTION BUTTONS
                ================================================== --}}

                @if ($availableOfferings->isNotEmpty())

                    <div
                        class="mt-6
                               flex
                               flex-wrap
                               justify-end
                               gap-3"
                    >

                        <a
                            href="{{ route(
                                'admin.students.show',
                                $enrolment->student
                            ) }}"
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-slate-300
                                   bg-white
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-slate-600
                                   hover:bg-slate-50"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="inline-flex
                                   h-11
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-600
                                   px-7
                                   text-sm
                                   font-semibold
                                   text-white
                                   shadow-sm
                                   hover:bg-blue-700"
                        >

                            @if ($existingWishlist)

                                Update Wishlist

                            @else

                                Add to Wishlist

                            @endif

                        </button>

                    </div>

                @endif

            </form>

        </section>

    </div>

</div>

@endsection
