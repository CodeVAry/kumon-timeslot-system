@extends('layouts.parent')

@section('title', 'Wishlist')

@section('page-title', 'Wishlist')


@section('content')

@php

    $statusLabels = [
        'pending' =>
            'Pending Approval',

        'approved' =>
            'Approved',

        'rejected' =>
            'Rejected',

        'cancelled' =>
            'Cancelled',
    ];


    $statusClasses = [
        'pending' =>
            'bg-amber-100 text-amber-700',

        'approved' =>
            'bg-green-100 text-green-700',

        'rejected' =>
            'bg-red-100 text-red-700',

        'cancelled' =>
            'bg-slate-200 text-slate-600',
    ];

@endphp


<div
    class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8"
>

    <div
        class="flex
               flex-col
               gap-4
               lg:flex-row
               lg:items-end
               lg:justify-between"
    >

        <div>

            <p
                class="text-xs
                       font-bold
                       uppercase
                       tracking-[0.15em]
                       text-violet-600"
            >
                Parent Portal
            </p>


            <h1
                class="mt-2
                       text-3xl
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
                View and manage class change requests for

                <span class="font-semibold text-slate-700">
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </span>.
            </p>

        </div>


        <a
            href="{{ route(
                'parent.wishlist.create'
            ) }}"
            class="inline-flex
                   h-11
                   items-center
                   justify-center
                   rounded-xl
                   bg-violet-600
                   px-5
                   text-sm
                   font-bold
                   text-white
                   shadow-sm
                   hover:bg-violet-700"
        >
            + Create Wishlist Request
        </a>

    </div>



    @if (session('success'))

        <div
            class="mt-6
                   rounded-xl
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
            class="mt-6
                   rounded-xl
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



    {{-- Summary --}}
    <div
        class="mt-7
               grid
               gap-4
               sm:grid-cols-2
               xl:grid-cols-4"
    >

        <div
            class="rounded-2xl
                   border
                   border-amber-100
                   bg-amber-50
                   p-5"
        >
            <p class="text-sm font-semibold text-slate-600">
                Pending
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $pendingCount }}
            </p>
        </div>


        <div
            class="rounded-2xl
                   border
                   border-green-100
                   bg-green-50
                   p-5"
        >
            <p class="text-sm font-semibold text-slate-600">
                Approved
            </p>

            <p class="mt-2 text-3xl font-bold text-green-600">
                {{ $approvedCount }}
            </p>
        </div>


        <div
            class="rounded-2xl
                   border
                   border-red-100
                   bg-red-50
                   p-5"
        >
            <p class="text-sm font-semibold text-slate-600">
                Rejected
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedCount }}
            </p>
        </div>


        <div
            class="rounded-2xl
                   border
                   border-slate-200
                   bg-white
                   p-5"
        >
            <p class="text-sm font-semibold text-slate-600">
                Cancelled
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-700">
                {{ $cancelledCount }}
            </p>
        </div>

    </div>



    {{-- Filter --}}
    <section
        class="mt-6
               rounded-2xl
               border
               border-slate-200
               bg-white
               p-5"
    >

        <form
            method="GET"
            action="{{ route(
                'parent.wishlist.index'
            ) }}"
            class="flex
                   flex-col
                   gap-3
                   sm:flex-row"
        >

            <select
                name="status"
                class="h-11
                       rounded-xl
                       border-slate-300
                       sm:min-w-[220px]"
            >

                <option
                    value="all"
                    @selected(
                        $status === 'all'
                    )
                >
                    All Statuses
                </option>


                <option
                    value="pending"
                    @selected(
                        $status === 'pending'
                    )
                >
                    Pending
                </option>


                <option
                    value="approved"
                    @selected(
                        $status === 'approved'
                    )
                >
                    Approved
                </option>


                <option
                    value="rejected"
                    @selected(
                        $status === 'rejected'
                    )
                >
                    Rejected
                </option>


                <option
                    value="cancelled"
                    @selected(
                        $status === 'cancelled'
                    )
                >
                    Cancelled
                </option>

            </select>


            <button
                type="submit"
                class="h-11
                       rounded-xl
                       bg-violet-600
                       px-5
                       text-sm
                       font-semibold
                       text-white"
            >
                Filter
            </button>


            @if ($status !== 'all')

                <a
                    href="{{ route(
                        'parent.wishlist.index'
                    ) }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-slate-300
                           px-5
                           text-sm
                           font-semibold
                           text-slate-600"
                >
                    Clear
                </a>

            @endif

        </form>

    </section>



    {{-- Table --}}
    <section
        class="mt-6
               overflow-hidden
               rounded-[26px]
               border
               border-slate-200
               bg-white
               shadow-sm"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Class
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Current Time
                        </th>


                        <th
                            class="px-6 py-4
                                   text-left
                                   text-xs
                                   font-bold
                                   uppercase
                                   text-slate-500"
                        >
                            Requested Time
                        </th>


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


                <tbody class="divide-y divide-slate-100">

                    @forelse ($wishlists as $wishlist)

                        @php

                            $requestedOffering =
                                $wishlist
                                    ->sectionOffering;


                            $originalEnrolment =
                                $wishlist
                                    ->wishlistForEnrolment;


                            $originalOffering =
                                $originalEnrolment
                                    ?->sectionOffering;

                        @endphp


                        <tr class="hover:bg-violet-50/30">

                            {{-- Class --}}
                            <td class="px-6 py-5">

                                <p
                                    class="font-bold
                                           text-slate-900"
                                >
                                    {{
                                        $requestedOffering
                                            ?->section
                                            ?->section_name
                                        ??
                                        $originalOffering
                                            ?->section
                                            ?->section_name
                                        ??
                                        'Class'
                                    }}
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-slate-400"
                                >
                                    Requested:
                                    {{
                                        $wishlist
                                            ->created_at
                                            ?->format(
                                                'd M Y'
                                            )
                                        ?? '—'
                                    }}
                                </p>

                            </td>


                            {{-- Current / Original --}}
                            <td
                                class="px-6 py-5
                                       text-sm
                                       text-slate-600"
                            >

                                @if ($originalOffering)

                                    {{
                                        $originalOffering
                                            ->day
                                            ?->day_name
                                        ?? '—'
                                    }}

                                    ·

                                    {{
                                        \Carbon\Carbon::parse(
                                            $originalOffering
                                                ->start_time
                                        )->format('g:i A')
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::parse(
                                            $originalOffering
                                                ->end_time
                                        )->format('g:i A')
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- Requested --}}
                            <td
                                class="px-6 py-5
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >

                                @if ($requestedOffering)

                                    {{
                                        $requestedOffering
                                            ->day
                                            ?->day_name
                                        ?? '—'
                                    }}

                                    ·

                                    {{
                                        \Carbon\Carbon::parse(
                                            $requestedOffering
                                                ->start_time
                                        )->format('g:i A')
                                    }}

                                    –

                                    {{
                                        \Carbon\Carbon::parse(
                                            $requestedOffering
                                                ->end_time
                                        )->format('g:i A')
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- Status --}}
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
                                                   $wishlist
                                                       ->wishlist_status
                                               ]
                                               ??
                                               'bg-slate-100 text-slate-600'
                                           }}"
                                >
                                    {{
                                        $statusLabels[
                                            $wishlist
                                                ->wishlist_status
                                        ]
                                        ??
                                        ucfirst(
                                            $wishlist
                                                ->wishlist_status
                                        )
                                    }}
                                </span>

                            </td>


                            {{-- Actions --}}
                            <td class="px-6 py-5">

                                <div
                                    class="flex
                                           flex-wrap
                                           justify-center
                                           gap-2"
                                >

                                    <a
                                        href="{{ route(
                                            'parent.wishlist.show',
                                            $wishlist
                                        ) }}"
                                        class="inline-flex
                                               h-10
                                               items-center
                                               rounded-xl
                                               border
                                               border-violet-300
                                               px-4
                                               text-xs
                                               font-semibold
                                               text-violet-700"
                                    >
                                        View Details
                                    </a>


                                    @if (
                                        $wishlist
                                            ->wishlist_status
                                        ===
                                        'pending'
                                    )

                                        <a
                                            href="{{ route(
                                                'parent.wishlist.edit',
                                                $wishlist
                                            ) }}"
                                            class="inline-flex
                                                   h-10
                                                   items-center
                                                   rounded-xl
                                                   border
                                                   border-blue-300
                                                   px-4
                                                   text-xs
                                                   font-semibold
                                                   text-blue-700"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'parent.wishlist.cancel',
                                                $wishlist
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')


                                            <button
                                                type="submit"
                                                onclick="
                                                    return confirm(
                                                        'Cancel this wishlist request?'
                                                    );
                                                "
                                                class="h-10
                                                       rounded-xl
                                                       border
                                                       border-red-300
                                                       bg-red-50
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-red-700"
                                            >
                                                Cancel
                                            </button>

                                        </form>

                                    @endif


                                    @if (
                                        in_array(
                                            $wishlist
                                                ->wishlist_status,
                                            [
                                                'rejected',
                                                'cancelled',
                                            ]
                                        )
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'parent.wishlist.destroy',
                                                $wishlist
                                            ) }}"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                onclick="
                                                    return confirm(
                                                        'Delete this wishlist request permanently?'
                                                    );
                                                "
                                                class="h-10
                                                       rounded-xl
                                                       border
                                                       border-red-300
                                                       bg-red-50
                                                       px-4
                                                       text-xs
                                                       font-semibold
                                                       text-red-700"
                                            >
                                                Delete
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
                                    No wishlist requests found.
                                </p>

                                <p
                                    class="mt-1
                                           text-sm
                                           text-slate-500"
                                >
                                    Create a request to choose
                                    another class time.
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