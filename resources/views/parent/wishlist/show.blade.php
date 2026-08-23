@extends('layouts.parent')

@section('title', 'Wishlist Details')

@section('page-title', 'Wishlist Details')


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


    $requested =
        $wishlist
            ->sectionOffering;


    $original =
        $wishlist
            ->wishlistForEnrolment
            ?->sectionOffering;

@endphp


<div
    class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8"
>

    <a
        href="{{ route(
            'parent.wishlist.index'
        ) }}"
        class="text-sm
               font-semibold
               text-violet-700"
    >
        ← Back to Wishlist
    </a>



    @if (session('success'))

        <div
            class="mt-6
                   rounded-xl
                   border
                   border-green-200
                   bg-green-50
                   p-4
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
                   p-4
                   text-sm
                   font-semibold
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    <section
        class="mt-6
               rounded-[26px]
               border
               border-slate-200
               bg-white
               p-6"
    >

        <div
            class="flex
                   justify-between
                   gap-5"
        >

            <div>

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-violet-600"
                >
                    Wishlist Details
                </p>


                <h1
                    class="mt-2
                           text-3xl
                           font-bold
                           text-slate-900"
                >
                    {{
                        $requested
                            ?->section
                            ?->section_name
                        ??
                        $original
                            ?->section
                            ?->section_name
                        ??
                        'Class'
                    }}
                </h1>


                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </p>

            </div>


            <span
                class="self-start
                       rounded-full
                       px-4 py-2
                       text-sm
                       font-bold
                       {{
                           $statusClasses[
                               $wishlist
                                   ->wishlist_status
                           ]
                       }}"
            >
                {{
                    $statusLabels[
                        $wishlist
                            ->wishlist_status
                    ]
                }}
            </span>

        </div>

    </section>



    <div
        class="mt-6
               grid
               gap-6
               lg:grid-cols-2"
    >

        {{-- Original --}}
        <section
            class="rounded-[26px]
                   border
                   border-slate-200
                   bg-white
                   p-6"
        >

            <p
                class="text-xs
                       font-bold
                       uppercase
                       text-blue-600"
            >
                Original Class
            </p>


            @if ($original)

                <h2
                    class="mt-3
                           text-xl
                           font-bold
                           text-slate-900"
                >
                    {{
                        $original
                            ->section
                            ?->section_name
                    }}
                </h2>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600"
                >
                    {{
                        $original
                            ->day
                            ?->day_name
                    }}

                    ·

                    {{
                        \Carbon\Carbon::parse(
                            $original->start_time
                        )->format('g:i A')
                    }}

                    –

                    {{
                        \Carbon\Carbon::parse(
                            $original->end_time
                        )->format('g:i A')
                    }}
                </p>

            @else

                <p class="mt-3 text-sm text-slate-500">
                    Original class unavailable.
                </p>

            @endif

        </section>



        {{-- Requested --}}
        <section
            class="rounded-[26px]
                   border
                   border-violet-200
                   bg-white
                   p-6"
        >

            <p
                class="text-xs
                       font-bold
                       uppercase
                       text-violet-600"
            >
                Requested Class
            </p>


            @if ($requested)

                <h2
                    class="mt-3
                           text-xl
                           font-bold
                           text-slate-900"
                >
                    {{
                        $requested
                            ->section
                            ?->section_name
                    }}
                </h2>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600"
                >
                    {{
                        $requested
                            ->day
                            ?->day_name
                    }}

                    ·

                    {{
                        \Carbon\Carbon::parse(
                            $requested->start_time
                        )->format('g:i A')
                    }}

                    –

                    {{
                        \Carbon\Carbon::parse(
                            $requested->end_time
                        )->format('g:i A')
                    }}
                </p>

            @endif

        </section>

    </div>



    {{-- Review --}}
    <section
        class="mt-6
               rounded-[26px]
               border
               border-slate-200
               bg-white
               p-6"
    >

        <div
            class="grid
                   gap-5
                   sm:grid-cols-2"
        >

            <div>

                <p class="text-xs uppercase text-slate-400">
                    Requested At
                </p>

                <p class="mt-2 font-semibold text-slate-800">
                    {{
                        $wishlist
                            ->created_at
                            ?->format(
                                'd M Y, g:i A'
                            )
                        ?? '—'
                    }}
                </p>

            </div>


            <div>

                <p class="text-xs uppercase text-slate-400">
                    Reviewed At
                </p>

                <p class="mt-2 font-semibold text-slate-800">
                    {{
                        $wishlist
                            ->reviewed_at
                            ?->format(
                                'd M Y, g:i A'
                            )
                        ??
                        'Not reviewed yet'
                    }}
                </p>

            </div>

        </div>


        @if (
            $wishlist
                ->wishlist_review_note
        )

            <div
                class="mt-5
                       rounded-xl
                       bg-violet-50
                       p-5"
            >

                <p
                    class="text-xs
                           font-bold
                           uppercase
                           text-violet-500"
                >
                    Centre Review
                </p>


                <p
                    class="mt-2
                           whitespace-pre-line
                           text-sm
                           text-violet-800"
                >
                    {{
                        $wishlist
                            ->wishlist_review_note
                    }}
                </p>

            </div>

        @endif

    </section>



    {{-- Actions --}}
    <div
        class="mt-6
               flex
               flex-wrap
               justify-end
               gap-3"
    >

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
                       h-11
                       items-center
                       rounded-xl
                       border
                       border-blue-300
                       px-5
                       text-sm
                       font-semibold
                       text-blue-700"
            >
                Edit Request
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
                    class="h-11
                           rounded-xl
                           border
                           border-red-300
                           bg-red-50
                           px-5
                           text-sm
                           font-semibold
                           text-red-700"
                >
                    Cancel Request
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
                    class="h-11
                           rounded-xl
                           border
                           border-red-300
                           bg-red-50
                           px-5
                           text-sm
                           font-semibold
                           text-red-700"
                >
                    Delete
                </button>

            </form>

        @endif

    </div>

</div>

@endsection