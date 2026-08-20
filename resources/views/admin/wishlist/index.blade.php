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

@endphp


@section('content')

<div class="space-y-6">


    {{-- Header --}}
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
            Manage students waiting
            to move to another class time.
        </p>

    </div>



    {{-- Messages --}}
    @if (session('success'))

        <div
            class="rounded-xl
                   border border-green-200
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
                   border border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm
                   font-semibold
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    {{-- Summary --}}
    <div
        class="grid gap-4
               sm:grid-cols-2"
    >

        <div
            class="rounded-2xl
                   border border-blue-100
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


        <div
            class="rounded-2xl
                   border border-green-100
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



    {{-- Main table --}}
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
                           text-white"
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
                               px-5 py-2.5
                               text-sm
                               font-semibold
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
                            Current Class
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Requested Class
                        </th>


                        <th
                            class="px-6 py-4
                                   text-center
                                   text-xs font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Availability
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

                    @forelse ($wishlists as $wishlist)

                        @php

                            $student =
                                $wishlist->student;


                            $current =
                                $wishlist
                                    ->wishlistForEnrolment
                                    ?->sectionOffering;


                            $requested =
                                $wishlist
                                    ->sectionOffering;


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


                            $availableSeats =
                                $requested
                                    ? max(
                                        0,
                                        $requested->max_seats
                                        -
                                        $confirmedCount
                                    )
                                    : 0;

                        @endphp


                        <tr
                            class="hover:bg-blue-50/30"
                        >


                            {{-- Student --}}
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



                            {{-- Current --}}
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

                                        ·

                                        {{
                                            \Carbon\Carbon::parse(
                                                $current->start_time
                                            )->format('g:i A')
                                        }}

                                    @endif
                                </p>

                            </td>



                            {{-- Requested --}}
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

                                        ·

                                        {{
                                            \Carbon\Carbon::parse(
                                                $requested->start_time
                                            )->format('g:i A')
                                        }}

                                    @endif
                                </p>

                            </td>



                            {{-- Availability --}}
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
                                        seat available
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



                            {{-- Action --}}
                            <td
                                class="px-6 py-5"
                            >

                                <div
                                    class="flex
                                           justify-center
                                           gap-2"
                                >

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
                                                    'Move this student to the requested class?'
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
                                                       rounded-lg
                                                       bg-green-600
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-white
                                                       hover:bg-green-700"
                                            >
                                                Approve / Move
                                            </button>

                                        </form>

                                    @endif



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
                                                       rounded-lg
                                                       border
                                                       border-red-300
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-red-600
                                                       hover:bg-red-50"
                                            >
                                                Cancel
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-14
                                       text-center"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-700"
                                >
                                    No active wishlist requests
                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >
                                    Wishlist requests will
                                    appear here.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


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
