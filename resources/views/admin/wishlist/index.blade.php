@extends('layouts.admin')

@section('title', 'Wishlist')

@section('page-title', 'Wishlist')


@php

    $breadcrumbs = [
        [
            'label' => 'Wishlist',
            'url' => null,
        ],
    ];


    $statusLabels = [
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];


    $statusClasses = [
        'pending' => 'bg-amber-100 text-amber-700',
        'approved' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        'cancelled' => 'bg-slate-200 text-slate-600',
    ];

@endphp


@section('content')

<div class="space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div>

        <h1
            class="text-3xl
                   font-bold
                   text-slate-900"
        >
            Wishlist
        </h1>


        <p
            class="mt-2
                   text-sm
                   text-slate-500"
        >
            Review and manage student requests
            to move to another class time.
        </p>

    </div>



    {{-- =========================================================
        SUCCESS MESSAGE
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



    {{-- =========================================================
        ERROR MESSAGE
    ========================================================== --}}

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
        VALIDATION ERRORS
    ========================================================== --}}

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


            <ul
                class="mt-2
                       list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600"
            >

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div
        class="grid
               gap-4
               sm:grid-cols-2"
    >


        {{-- Active Wishlist --}}
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
                Active Wishlist
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-blue-700"
            >
                {{ $totalWishlist }}
            </p>

        </div>



        {{-- Seat Available --}}
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
                Seat Available
            </p>


            <p
                class="mt-2
                       text-3xl
                       font-bold
                       text-green-700"
            >
                {{ $availableWishlist }}
            </p>

        </div>

    </div>



    {{-- =========================================================
        MAIN TABLE
    ========================================================== --}}

    <section
        class="overflow-hidden
               rounded-2xl
               border
               border-slate-200
               bg-white
               shadow-sm"
    >


        {{-- =====================================================
            SEARCH
        ====================================================== --}}

        <div
            class="border-b
                   border-slate-100
                   p-5"
        >

            <form
                method="GET"
                action="{{ route(
                    'admin.wishlist.index'
                ) }}"
                class="flex
                       flex-col
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
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:bg-blue-700"
                >
                    Search
                </button>


                @if ($search !== '')

                    <a
                        href="{{ route(
                            'admin.wishlist.index'
                        ) }}"
                        class="inline-flex
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-5 py-2.5
                               text-sm
                               font-semibold
                               text-slate-600
                               transition
                               hover:bg-slate-50"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>



        {{-- =====================================================
            TABLE
        ====================================================== --}}

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>


                        {{-- Student --}}
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



                        {{-- Current Class --}}
                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Current Class
                        </th>



                        {{-- Requested Class --}}
                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Requested Class
                        </th>



                        {{-- Availability --}}
                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Availability
                        </th>



                        {{-- Status --}}
                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Status
                        </th>



                        {{-- Action --}}
                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs
                                   font-bold
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

                    @forelse ($wishlists as $wishlist)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Student
                            |--------------------------------------------------------------------------
                            */

                            $student =
                                $wishlist
                                    ->student;


                            /*
                            |--------------------------------------------------------------------------
                            | Current Class
                            |--------------------------------------------------------------------------
                            */

                            $current =
                                $wishlist
                                    ->wishlistForEnrolment
                                    ?->sectionOffering;


                            /*
                            |--------------------------------------------------------------------------
                            | Requested Class
                            |--------------------------------------------------------------------------
                            */

                            $requested =
                                $wishlist
                                    ->sectionOffering;


                            /*
                            |--------------------------------------------------------------------------
                            | Confirmed Seat Count
                            |--------------------------------------------------------------------------
                            */

                            $confirmedCount =
                                $requested
                                    ? $requested
                                        ->enrolments()
                                        ->where(
                                            'is_active',
                                            true
                                        )
                                        ->where(
                                            'is_wishlist',
                                            false
                                        )
                                        ->count()
                                    : 0;


                            /*
                            |--------------------------------------------------------------------------
                            | Available Seats
                            |--------------------------------------------------------------------------
                            */

                            $availableSeats =
                                $requested
                                    ? max(
                                        0,
                                        $requested->max_seats
                                        -
                                        $confirmedCount
                                    )
                                    : 0;


                            /*
                            |--------------------------------------------------------------------------
                            | Wishlist Status
                            |--------------------------------------------------------------------------
                            */

                            $wishlistStatus =
                                $wishlist
                                    ->wishlist_status
                                ?? 'pending';

                        @endphp



                        <tr
                            class="transition
                                   hover:bg-blue-50/30"
                        >


                            {{-- =================================================
                                STUDENT
                            ================================================== --}}

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
                                    {{
                                        $student?->external_id
                                        ?? '—'
                                    }}
                                </p>

                            </td>



                            {{-- =================================================
                                CURRENT CLASS
                            ================================================== --}}

                            <td
                                class="px-6 py-5"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $current
                                            ?->section
                                            ?->section_name
                                        ?? '—'
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    {{
                                        $current
                                            ?->day
                                            ?->day_name
                                        ?? '—'
                                    }}


                                    @if ($current)

                                        <span
                                            class="mx-1
                                                   text-slate-300"
                                        >
                                            ·
                                        </span>


                                        {{
                                            \Carbon\Carbon::parse(
                                                $current
                                                    ->start_time
                                            )->format(
                                                'g:i A'
                                            )
                                        }}

                                        –

                                        {{
                                            \Carbon\Carbon::parse(
                                                $current
                                                    ->end_time
                                            )->format(
                                                'g:i A'
                                            )
                                        }}

                                    @endif
                                </p>

                            </td>



                            {{-- =================================================
                                REQUESTED CLASS
                            ================================================== --}}

                            <td
                                class="px-6 py-5"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        $requested
                                            ?->section
                                            ?->section_name
                                        ?? '—'
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-500"
                                >
                                    {{
                                        $requested
                                            ?->day
                                            ?->day_name
                                        ?? '—'
                                    }}


                                    @if ($requested)

                                        <span
                                            class="mx-1
                                                   text-slate-300"
                                        >
                                            ·
                                        </span>


                                        {{
                                            \Carbon\Carbon::parse(
                                                $requested
                                                    ->start_time
                                            )->format(
                                                'g:i A'
                                            )
                                        }}

                                        –

                                        {{
                                            \Carbon\Carbon::parse(
                                                $requested
                                                    ->end_time
                                            )->format(
                                                'g:i A'
                                            )
                                        }}

                                    @endif

                                </p>

                            </td>



                            {{-- =================================================
                                AVAILABILITY
                            ================================================== --}}

                            <td
                                class="px-6 py-5
                                       text-center"
                            >

                                @if ($availableSeats > 0)

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-green-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-green-700"
                                    >
                                        {{ $availableSeats }}

                                        {{
                                            $availableSeats === 1
                                                ? 'seat available'
                                                : 'seats available'
                                        }}
                                    </span>


                                @else

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-red-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-red-700"
                                    >
                                        Full
                                    </span>

                                @endif

                            </td>



                            {{-- =================================================
                                STATUS
                            ================================================== --}}

                            <td
                                class="px-6 py-5
                                       text-center"
                            >

                                <span
                                    class="inline-flex
                                           rounded-full
                                           px-3 py-1.5
                                           text-xs
                                           font-bold
                                           {{
                                               $statusClasses[
                                                   $wishlistStatus
                                               ]
                                               ??
                                               'bg-slate-100 text-slate-600'
                                           }}"
                                >
                                    {{
                                        $statusLabels[
                                            $wishlistStatus
                                        ]
                                        ??
                                        ucfirst(
                                            $wishlistStatus
                                        )
                                    }}
                                </span>

                            </td>



                            {{-- =================================================
                                ACTION
                            ================================================== --}}

                            <td
                                class="px-6 py-5"
                            >

                                <div
                                    class="flex
                                           flex-wrap
                                           items-center
                                           justify-center
                                           gap-2"
                                >


                                    {{-- =========================================
                                        PENDING REQUEST ACTIONS
                                    ========================================== --}}

                                    @if (
                                        $wishlistStatus
                                        ===
                                        'pending'
                                    )


                                        {{-- =====================================
                                            APPROVE
                                        ====================================== --}}

                                        @if (
                                            auth()
                                                ->user()
                                                ->hasPermission(
                                                    'wishlist.edit'
                                                )
                                            &&
                                            $availableSeats > 0
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.wishlist.approve',
                                                    $wishlist
                                                ) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Approve this wishlist request and move the student to the requested class?'
                                                    );
                                                "
                                            >

                                                @csrf
                                                @method('PATCH')


                                                <input
                                                    type="hidden"
                                                    name="wishlist_review_note"
                                                    value=""
                                                >


                                                <button
                                                    type="submit"
                                                    class="inline-flex
                                                           h-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           bg-green-600
                                                           px-4
                                                           text-xs
                                                           font-semibold
                                                           text-white
                                                           transition
                                                           hover:bg-green-700"
                                                >
                                                    Approve / Move
                                                </button>

                                            </form>

                                        @endif



                                        {{-- =====================================
                                            APPROVE DISABLED WHEN FULL
                                        ====================================== --}}

                                        @if (
                                            auth()
                                                ->user()
                                                ->hasPermission(
                                                    'wishlist.edit'
                                                )
                                            &&
                                            $availableSeats <= 0
                                        )

                                            <button
                                                type="button"
                                                disabled
                                                title="Requested class is full"
                                                class="inline-flex
                                                       h-9
                                                       cursor-not-allowed
                                                       items-center
                                                       justify-center
                                                       rounded-lg
                                                       bg-slate-200
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-slate-400"
                                            >
                                                Approve / Move
                                            </button>

                                        @endif



                                        {{-- =====================================
                                            REJECT
                                        ====================================== --}}

                                        @if (
                                            auth()
                                                ->user()
                                                ->hasPermission(
                                                    'wishlist.edit'
                                                )
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.wishlist.reject',
                                                    $wishlist
                                                ) }}"
                                                id="reject-form-{{ $wishlist->id }}"
                                            >

                                                @csrf
                                                @method('PATCH')


                                                <input
                                                    type="hidden"
                                                    name="wishlist_review_note"
                                                    id="reject-note-{{ $wishlist->id }}"
                                                >


                                                <button
                                                    type="button"
                                                    onclick="
                                                        const reason =
                                                            prompt(
                                                                'Enter the reason for rejecting this wishlist request:'
                                                            );

                                                        if (
                                                            reason !== null
                                                            &&
                                                            reason.trim() !== ''
                                                        ) {

                                                            document.getElementById(
                                                                'reject-note-{{ $wishlist->id }}'
                                                            ).value =
                                                                reason.trim();

                                                            document.getElementById(
                                                                'reject-form-{{ $wishlist->id }}'
                                                            ).submit();
                                                        }
                                                    "
                                                    class="inline-flex
                                                           h-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           border
                                                           border-red-300
                                                           bg-red-50
                                                           px-4
                                                           text-xs
                                                           font-semibold
                                                           text-red-700
                                                           transition
                                                           hover:bg-red-100"
                                                >
                                                    Reject
                                                </button>

                                            </form>

                                        @endif



                                        {{-- =====================================
                                            CANCEL
                                        ====================================== --}}

                                        @if (
                                            auth()
                                                ->user()
                                                ->hasPermission(
                                                    'wishlist.edit'
                                                )
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'admin.wishlist.cancel',
                                                    $wishlist
                                                ) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Cancel this wishlist request?'
                                                    );
                                                "
                                            >

                                                @csrf
                                                @method('PATCH')


                                                <button
                                                    type="submit"
                                                    class="inline-flex
                                                           h-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           border
                                                           border-slate-300
                                                           bg-white
                                                           px-4
                                                           text-xs
                                                           font-semibold
                                                           text-slate-600
                                                           transition
                                                           hover:bg-slate-50"
                                                >
                                                    Cancel
                                                </button>

                                            </form>

                                        @endif


                                    @else


                                        {{-- =====================================
                                            PROCESSED REQUEST
                                        ====================================== --}}

                                        <span
                                            class="text-xs
                                                   font-medium
                                                   text-slate-400"
                                        >
                                            Request processed
                                        </span>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6
                                       py-14
                                       text-center"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-700"
                                >
                                    No wishlist requests
                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >
                                    Parent wishlist requests
                                    will appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- =====================================================
            PAGINATION
        ====================================================== --}}

        @if ($wishlists->hasPages())

            <div
                class="border-t
                       border-slate-100
                       px-6 py-4"
            >
                {{ $wishlists->links() }}
            </div>

        @endif

    </section>

</div>

@endsection