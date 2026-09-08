@extends('layouts.parent')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | Safe Defaults
    |--------------------------------------------------------------------------
    |
    | These prevent the page from crashing if a variable
    | is temporarily missing while routes/cache are changing.
    |
    */

    $latestWishlist =
        $latestWishlist
        ?? null;


    $latestLeave =
        $latestLeave
        ?? null;


    $currentLeave =
        $currentLeave
        ?? null;


    $upcomingLeave =
        $upcomingLeave
        ?? null;


    $pendingLeaveCount =
        $pendingLeaveCount
        ?? 0;


    $pendingWishlistCount =
        $pendingWishlistCount
        ?? 0;


    $approvedWishlistCount =
        $approvedWishlistCount
        ?? 0;


    $rejectedWishlistCount =
        $rejectedWishlistCount
        ?? 0;


    $cancelledWishlistCount =
        $cancelledWishlistCount
        ?? 0;


    /*
    |--------------------------------------------------------------------------
    | Wishlist Status Labels
    |--------------------------------------------------------------------------
    */

    $wishlistStatusLabels = [

        'pending' =>
            'Pending',

        'approved' =>
            'Approved',

        'rejected' =>
            'Rejected',

        'cancelled' =>
            'Cancelled',

    ];


    /*
    |--------------------------------------------------------------------------
    | Wishlist Badge Classes
    |--------------------------------------------------------------------------
    */

    $wishlistStatusClasses = [

        'pending' =>
            'bg-amber-100 text-amber-700',

        'approved' =>
            'bg-green-100 text-green-700',

        'rejected' =>
            'bg-red-100 text-red-700',

        'cancelled' =>
            'bg-slate-200 text-slate-600',

    ];


    /*
    |--------------------------------------------------------------------------
    | Wishlist Heading Classes
    |--------------------------------------------------------------------------
    */

    $wishlistHeadingClasses = [

        'pending' =>
            'text-amber-600',

        'approved' =>
            'text-green-700',

        'rejected' =>
            'text-red-700',

        'cancelled' =>
            'text-slate-600',

    ];


    /*
    |--------------------------------------------------------------------------
    | Latest Wishlist Data
    |--------------------------------------------------------------------------
    */

    $latestWishlistStatus =
        $latestWishlist
            ?->wishlist_status;


    $latestRequestedOffering =
        $latestWishlist
            ?->sectionOffering;


    $latestOriginalOffering =
        $latestWishlist
            ?->wishlistForEnrolment
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


    {{-- =========================================================
        PAGE HEADER
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
                       tracking-tight
                       text-slate-900"
            >
                Welcome,
                {{ $guardian->first_name }}
            </h1>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                Viewing

                <span
                    class="font-semibold
                           text-slate-700"
                >
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </span>
            </p>

        </div>


        <div
            class="inline-flex
                   w-fit
                   items-center
                   rounded-full
                   border
                   border-violet-200
                   bg-white
                   px-5
                   py-2.5
                   text-sm
                   font-semibold
                   text-violet-700
                   shadow-sm"
        >

            {{ $enrolments->count() }}

            {{
                $enrolments->count() === 1
                    ? 'Active Class'
                    : 'Active Classes'
            }}

        </div>

    </div>



    {{-- =========================================================
        TOP SUMMARY CARDS
    ========================================================== --}}

    <div
        class="mt-7
               grid
               gap-5
               md:grid-cols-3"
    >


        {{-- =====================================================
            ACTIVE CLASSES
        ====================================================== --}}

        <div
            class="rounded-[24px]
                   border
                   border-violet-100
                   bg-violet-50
                   p-6"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Active Classes
            </p>


            <p
                class="mt-4
                       text-4xl
                       font-bold
                       text-violet-700"
            >
                {{ $enrolments->count() }}
            </p>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                Current confirmed enrolments
            </p>

        </div>



        {{-- =====================================================
            LATEST WISHLIST
        ====================================================== --}}

        <a
            href="{{ route(
                'parent.wishlist.index'
            ) }}"
            class="block
                   rounded-[24px]
                   border
                   border-purple-100
                   bg-purple-50
                   p-6
                   transition
                   hover:-translate-y-0.5
                   hover:shadow-md"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Wishlist
            </p>


            @if ($latestWishlist)

                <div
                    class="mt-4
                           flex
                           flex-wrap
                           items-center
                           gap-3"
                >

                    <p
                        class="text-2xl
                               font-bold
                               {{
                                   $wishlistHeadingClasses[
                                       $latestWishlistStatus
                                   ]
                                   ??
                                   'text-purple-700'
                               }}"
                    >
                        Latest Request
                    </p>


                    <span
                        class="inline-flex
                               rounded-full
                               px-3 py-1
                               text-xs
                               font-bold
                               {{
                                   $wishlistStatusClasses[
                                       $latestWishlistStatus
                                   ]
                                   ??
                                   'bg-slate-100 text-slate-600'
                               }}"
                    >
                        {{
                            $wishlistStatusLabels[
                                $latestWishlistStatus
                            ]
                            ??
                            ucfirst(
                                $latestWishlistStatus
                                ?? 'Unknown'
                            )
                        }}
                    </span>

                </div>



                {{-- Requested Class --}}
                @if ($latestRequestedOffering)

                    <p
                        class="mt-3
                               font-semibold
                               text-slate-700"
                    >
                        {{
                            $latestRequestedOffering
                                ->section
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
                            $latestRequestedOffering
                                ->day
                                ?->day_name
                            ??
                            '—'
                        }}


                        @if (
                            $latestRequestedOffering
                                ->start_time
                            &&
                            $latestRequestedOffering
                                ->end_time
                        )

                            <span
                                class="mx-1
                                       text-slate-300"
                            >
                                ·
                            </span>


                            {{
                                \Carbon\Carbon::parse(
                                    $latestRequestedOffering
                                        ->start_time
                                )->format(
                                    'g:i A'
                                )
                            }}

                            –

                            {{
                                \Carbon\Carbon::parse(
                                    $latestRequestedOffering
                                        ->end_time
                                )->format(
                                    'g:i A'
                                )
                            }}

                        @endif
                    </p>

                @endif



                {{-- Rejected Note --}}
                @if (
                    $latestWishlistStatus
                    ===
                    'rejected'
                )

                    <p
                        class="mt-3
                               text-sm
                               font-semibold
                               text-red-700"
                    >
                        Request rejected by the centre.
                    </p>


                    @if (
                        $latestWishlist
                            ->wishlist_review_note
                    )

                        <p
                            class="mt-1
                                   text-xs
                                   leading-5
                                   text-red-600"
                        >
                            {{
                                \Illuminate\Support\Str::limit(
                                    $latestWishlist
                                        ->wishlist_review_note,
                                    100
                                )
                            }}
                        </p>

                    @endif

                @endif



                {{-- Approved --}}
                @if (
                    $latestWishlistStatus
                    ===
                    'approved'
                )

                    <p
                        class="mt-3
                               text-sm
                               font-semibold
                               text-green-700"
                    >
                        This request has been approved.
                    </p>

                @endif



                {{-- Pending --}}
                @if (
                    $latestWishlistStatus
                    ===
                    'pending'
                )

                    <p
                        class="mt-3
                               text-sm
                               text-amber-700"
                    >
                        Waiting for centre approval.
                    </p>

                @endif



                {{-- Cancelled --}}
                @if (
                    $latestWishlistStatus
                    ===
                    'cancelled'
                )

                    <p
                        class="mt-3
                               text-sm
                               text-slate-600"
                    >
                        This request was cancelled.
                    </p>

                @endif


            @else

                <p
                    class="mt-4
                           text-2xl
                           font-bold
                           text-purple-700"
                >
                    No Request
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    No wishlist request has been submitted.
                </p>

            @endif

        </a>



        {{-- =====================================================
            LEAVE SUMMARY
        ====================================================== --}}

        <a
            href="{{ route(
                'parent.leave.index'
            ) }}"
            class="block
                   rounded-[24px]
                   border
                   border-cyan-100
                   bg-cyan-100/60
                   p-6
                   transition
                   hover:-translate-y-0.5
                   hover:shadow-md"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Leave Management
            </p>



            @if ($currentLeave)

                <p
                    class="mt-4
                           text-2xl
                           font-bold
                           text-blue-700"
                >
                    Current Leave
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    Expected return

                    <span
                        class="font-semibold
                               text-slate-700"
                    >
                        {{
                            $currentLeave
                                ->expected_return_date
                                ->format(
                                    'd M Y'
                                )
                        }}
                    </span>
                </p>


            @elseif ($pendingLeaveCount > 0)

                <div
                    class="mt-4
                           flex
                           items-center
                           gap-3"
                >

                    <p
                        class="text-3xl
                               font-bold
                               text-amber-600"
                    >
                        {{ $pendingLeaveCount }}
                    </p>


                    <span
                        class="rounded-full
                               bg-amber-100
                               px-3 py-1
                               text-xs
                               font-bold
                               text-amber-700"
                    >
                        Pending
                    </span>

                </div>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    Leave request waiting for approval
                </p>


            @elseif ($upcomingLeave)

                <p
                    class="mt-4
                           text-2xl
                           font-bold
                           text-purple-700"
                >
                    Upcoming Leave
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    Starts

                    <span
                        class="font-semibold
                               text-slate-700"
                    >
                        {{
                            $upcomingLeave
                                ->start_date
                                ->format(
                                    'd M Y'
                                )
                        }}
                    </span>
                </p>


            @elseif (
                $latestLeave
                &&
                $latestLeave->status
                    ===
                    'rejected'
            )

                <p
                    class="mt-4
                           text-2xl
                           font-bold
                           text-red-700"
                >
                    Latest Request Rejected
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    Open Leave Management for details.
                </p>


            @elseif (
                $latestLeave
                &&
                $latestLeave->status
                    ===
                    'cancelled'
            )

                <p
                    class="mt-4
                           text-2xl
                           font-bold
                           text-slate-600"
                >
                    Latest Request Cancelled
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    No active leave request.
                </p>


            @else

                <p
                    class="mt-4
                           text-2xl
                           font-bold
                           text-cyan-700"
                >
                    No Active Leave
                </p>


                <p
                    class="mt-2
                           text-sm
                           text-slate-500"
                >
                    Submit a leave request if needed.
                </p>

            @endif

        </a>

    </div>



    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}

    <div
        class="mt-7
               grid
               gap-6
               xl:grid-cols-3"
    >


        {{-- =====================================================
            MY CLASSES
        ====================================================== --}}

        <section
            class="overflow-hidden
                   rounded-[26px]
                   border
                   border-cyan-100
                   bg-white
                   shadow-sm
                   xl:col-span-2"
        >

            <div
                class="flex
                       items-center
                       justify-between
                       border-b
                       border-slate-100
                       p-6"
            >

                <div>

                    <h2
                        class="text-2xl
                               font-bold
                               text-slate-900"
                    >
                        My Classes
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        View the selected student's
                        current class schedule.
                    </p>

                </div>


                @if ($enrolments->count() > 1)

                    <span
                        id="classCounter"
                        class="rounded-full
                               bg-violet-100
                               px-3 py-1
                               text-xs
                               font-semibold
                               text-violet-700"
                    >
                        1 / {{ $enrolments->count() }}
                    </span>

                @endif

            </div>



            @if ($enrolments->isNotEmpty())

                <div class="p-6">

                    @foreach (
                        $enrolments
                        as $enrolment
                    )

                        @php

                            $offering =
                                $enrolment
                                    ->sectionOffering;

                        @endphp


                        <div
                            class="class-slide
                                   {{
                                       $loop->first
                                           ? ''
                                           : 'hidden'
                                   }}"
                            data-index="{{ $loop->index }}"
                        >

                            <div
                                class="rounded-2xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       p-6"
                            >

                                <div
                                    class="flex
                                           items-start
                                           justify-between
                                           gap-4"
                                >

                                    <div>

                                        <p
                                            class="text-xs
                                                   font-semibold
                                                   uppercase
                                                   tracking-wide
                                                   text-violet-600"
                                        >
                                            Current Class
                                        </p>


                                        <h3
                                            class="mt-2
                                                   text-3xl
                                                   font-bold
                                                   text-slate-900"
                                        >
                                            {{
                                                $offering
                                                    ?->section
                                                    ?->section_name
                                                ??
                                                '—'
                                            }}
                                        </h3>

                                    </div>


                                    <span
                                        class="rounded-full
                                               bg-green-100
                                               px-3 py-1
                                               text-xs
                                               font-semibold
                                               text-green-700"
                                    >
                                        Active
                                    </span>

                                </div>



                                <div
                                    class="mt-7
                                           grid
                                           gap-4
                                           md:grid-cols-3"
                                >


                                    {{-- Day --}}
                                    <div
                                        class="rounded-xl
                                               bg-white
                                               p-4"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Day
                                        </p>


                                        <p
                                            class="mt-1
                                                   font-bold
                                                   text-slate-800"
                                        >
                                            {{
                                                $offering
                                                    ?->day
                                                    ?->day_name
                                                ??
                                                '—'
                                            }}
                                        </p>

                                    </div>



                                    {{-- Class Time --}}
                                    <div
                                        class="rounded-xl
                                               bg-white
                                               p-4"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Class Time
                                        </p>


                                        <p
                                            class="mt-1
                                                   font-bold
                                                   text-slate-800"
                                        >

                                            @if ($offering)

                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $offering
                                                            ->start_time
                                                    )->format(
                                                        'g:i A'
                                                    )
                                                }}

                                                –

                                                {{
                                                    \Carbon\Carbon::parse(
                                                        $offering
                                                            ->end_time
                                                    )->format(
                                                        'g:i A'
                                                    )
                                                }}

                                            @else

                                                —

                                            @endif

                                        </p>

                                    </div>



                                    {{-- Duration --}}
                                    <div
                                        class="rounded-xl
                                               bg-white
                                               p-4"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Duration
                                        </p>


                                        <p
                                            class="mt-1
                                                   font-bold
                                                   text-slate-800"
                                        >
                                            {{
                                                $offering
                                                    ? $offering
                                                        ->duration_minutes
                                                        . ' min'
                                                    : '—'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach



                    {{-- =================================================
                        SLIDER CONTROLS
                    ================================================== --}}

                    @if ($enrolments->count() > 1)

                        <div
                            class="mt-5
                                   flex
                                   items-center
                                   justify-between"
                        >

                            <button
                                type="button"
                                id="classPrev"
                                class="inline-flex
                                       h-10 w-10
                                       items-center
                                       justify-center
                                       rounded-xl
                                       border
                                       border-violet-200
                                       bg-white
                                       text-violet-700
                                       transition
                                       hover:bg-violet-50"
                            >
                                ←
                            </button>



                            <div
                                class="flex
                                       items-center
                                       gap-2"
                            >

                                @foreach (
                                    $enrolments
                                    as $enrolment
                                )

                                    <button
                                        type="button"
                                        class="class-dot
                                               h-2.5 w-2.5
                                               rounded-full
                                               {{
                                                   $loop->first
                                                       ? 'bg-violet-600'
                                                       : 'bg-violet-200'
                                               }}"
                                        data-index="{{ $loop->index }}"
                                    ></button>

                                @endforeach

                            </div>



                            <button
                                type="button"
                                id="classNext"
                                class="inline-flex
                                       h-10 w-10
                                       items-center
                                       justify-center
                                       rounded-xl
                                       border
                                       border-violet-200
                                       bg-white
                                       text-violet-700
                                       transition
                                       hover:bg-violet-50"
                            >
                                →
                            </button>

                        </div>

                    @endif

                </div>


            @else

                <div
                    class="px-6
                           py-16
                           text-center"
                >

                    <p
                        class="font-semibold
                               text-slate-700"
                    >
                        No active classes
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        No confirmed class enrolment
                        is currently available.
                    </p>

                </div>

            @endif

        </section>



        {{-- =====================================================
            RIGHT SIDE
        ====================================================== --}}

        <div class="space-y-6">


            {{-- =================================================
                WISHLIST DETAIL CARD
            ================================================== --}}

            <section
                class="rounded-[26px]
                       border
                       border-purple-100
                       bg-purple-50
                       p-6
                       shadow-sm"
            >

                <p
                    class="text-sm
                           font-semibold
                           text-purple-700"
                >
                    Wishlist
                </p>


                <h3
                    class="mt-2
                           text-xl
                           font-bold
                           text-slate-900"
                >
                    Preferred Class Times
                </h3>



                @if ($latestWishlist)

                    <div
                        class="mt-4
                               flex
                               flex-wrap
                               items-center
                               gap-2"
                    >

                        <span
                            class="rounded-full
                                   px-3 py-1
                                   text-xs
                                   font-bold
                                   {{
                                       $wishlistStatusClasses[
                                           $latestWishlistStatus
                                       ]
                                       ??
                                       'bg-slate-100 text-slate-600'
                                   }}"
                        >
                            {{
                                $wishlistStatusLabels[
                                    $latestWishlistStatus
                                ]
                                ??
                                ucfirst(
                                    $latestWishlistStatus
                                    ?? 'Unknown'
                                )
                            }}
                        </span>


                        @if (
                            $latestRequestedOffering
                                ?->section
                        )

                            <span
                                class="text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                {{
                                    $latestRequestedOffering
                                        ->section
                                        ->section_name
                                }}
                            </span>

                        @endif

                    </div>



                    {{-- Pending --}}
                    @if (
                        $latestWishlistStatus
                        ===
                        'pending'
                    )

                        <p
                            class="mt-3
                                   text-sm
                                   leading-6
                                   text-amber-700"
                        >
                            Waiting for centre approval.
                        </p>

                    @endif



                    {{-- Approved --}}
                    @if (
                        $latestWishlistStatus
                        ===
                        'approved'
                    )

                        <p
                            class="mt-3
                                   text-sm
                                   leading-6
                                   text-green-700"
                        >
                            Your latest wishlist request
                            has been approved.
                        </p>

                    @endif



                    {{-- Rejected --}}
                    @if (
                        $latestWishlistStatus
                        ===
                        'rejected'
                    )

                        <p
                            class="mt-3
                                   text-sm
                                   leading-6
                                   font-semibold
                                   text-red-700"
                        >
                            Your latest wishlist request
                            was rejected.
                        </p>


                        @if (
                            $latestWishlist
                                ->wishlist_review_note
                        )

                            <div
                                class="mt-3
                                       rounded-xl
                                       border
                                       border-red-100
                                       bg-white/80
                                       p-3"
                            >

                                <p
                                    class="text-xs
                                           font-bold
                                           uppercase
                                           text-red-500"
                                >
                                    Centre Note
                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           leading-5
                                           text-slate-600"
                                >
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $latestWishlist
                                                ->wishlist_review_note,
                                            120
                                        )
                                    }}
                                </p>

                            </div>

                        @endif

                    @endif



                    {{-- Cancelled --}}
                    @if (
                        $latestWishlistStatus
                        ===
                        'cancelled'
                    )

                        <p
                            class="mt-3
                                   text-sm
                                   leading-6
                                   text-slate-600"
                        >
                            Your latest wishlist request
                            was cancelled.
                        </p>

                    @endif


                @else

                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               text-slate-600"
                    >
                        No wishlist request has been submitted.
                    </p>

                @endif



                <a
                    href="{{ route(
                        'parent.wishlist.index'
                    ) }}"
                    class="mt-5
                           inline-flex
                           rounded-xl
                           bg-purple-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:bg-purple-700"
                >
                    Open Wishlist
                </a>

            </section>



            {{-- =================================================
                LEAVE DETAIL CARD
            ================================================== --}}

            <section
                class="rounded-[26px]
                       border
                       border-cyan-100
                       bg-cyan-50
                       p-6
                       shadow-sm"
            >

                <p
                    class="text-sm
                           font-semibold
                           text-cyan-700"
                >
                    Leave Management
                </p>


                <h3
                    class="mt-2
                           text-xl
                           font-bold
                           text-slate-900"
                >
                    Student Leave
                </h3>



                @if ($currentLeave)

                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               text-slate-600"
                    >
                        Currently on approved leave until

                        <span
                            class="font-semibold
                                   text-slate-800"
                        >
                            {{
                                $currentLeave
                                    ->expected_return_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}
                        </span>.
                    </p>


                @elseif ($pendingLeaveCount > 0)

                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               text-slate-600"
                    >
                        You have

                        <span
                            class="font-bold
                                   text-amber-700"
                        >
                            {{ $pendingLeaveCount }}
                        </span>

                        pending leave

                        {{
                            $pendingLeaveCount === 1
                                ? 'request'
                                : 'requests'
                        }}.
                    </p>


                @elseif ($upcomingLeave)

                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               text-slate-600"
                    >
                        Next approved leave starts on

                        <span
                            class="font-semibold
                                   text-slate-800"
                        >
                            {{
                                $upcomingLeave
                                    ->start_date
                                    ->format(
                                        'd M Y'
                                    )
                            }}
                        </span>.
                    </p>


                @elseif (
                    $latestLeave
                    &&
                    $latestLeave->status
                        ===
                        'rejected'
                )

                    <p
                        class="mt-3
                               text-sm
                               font-semibold
                               leading-6
                               text-red-700"
                    >
                        Your latest leave request
                        was rejected.
                    </p>


                    @if ($latestLeave->review_note)

                        <p
                            class="mt-2
                                   text-xs
                                   leading-5
                                   text-slate-600"
                        >
                            {{
                                \Illuminate\Support\Str::limit(
                                    $latestLeave
                                        ->review_note,
                                    120
                                )
                            }}
                        </p>

                    @endif


                @elseif (
                    $latestLeave
                    &&
                    $latestLeave->status
                        ===
                        'cancelled'
                )

                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               text-slate-600"
                    >
                        Your latest leave request
                        was cancelled.
                    </p>


                @else

                    <p
                        class="mt-3
                               text-sm
                               leading-6
                               text-slate-600"
                    >
                        No current or upcoming leave.
                    </p>

                @endif



                <a
                    href="{{ route(
                        'parent.leave.index'
                    ) }}"
                    class="mt-5
                           inline-flex
                           rounded-xl
                           bg-cyan-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:bg-cyan-700"
                >
                    Open Leave
                </a>

            </section>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const slides =
            document.querySelectorAll(
                '.class-slide'
            );


        const dots =
            document.querySelectorAll(
                '.class-dot'
            );


        const prevButton =
            document.getElementById(
                'classPrev'
            );


        const nextButton =
            document.getElementById(
                'classNext'
            );


        const counter =
            document.getElementById(
                'classCounter'
            );


        /*
         * No slider required
         * for one or zero classes.
         */
        if (
            slides.length
            <=
            1
        ) {

            return;
        }


        let currentIndex =
            0;


        function showSlide(
            index
        ) {

            /*
             * Go from first
             * to last.
             */
            if (index < 0) {

                index =
                    slides.length - 1;
            }


            /*
             * Go from last
             * to first.
             */
            if (
                index
                >=
                slides.length
            ) {

                index =
                    0;
            }


            currentIndex =
                index;


            /*
             * Show current slide.
             */
            slides.forEach(
                function (
                    slide,
                    slideIndex
                ) {

                    slide.classList.toggle(
                        'hidden',
                        slideIndex
                        !==
                        currentIndex
                    );
                }
            );


            /*
             * Update dots.
             */
            dots.forEach(
                function (
                    dot,
                    dotIndex
                ) {

                    if (
                        dotIndex
                        ===
                        currentIndex
                    ) {

                        dot.classList.add(
                            'bg-violet-600'
                        );

                        dot.classList.remove(
                            'bg-violet-200'
                        );

                    } else {

                        dot.classList.remove(
                            'bg-violet-600'
                        );

                        dot.classList.add(
                            'bg-violet-200'
                        );
                    }
                }
            );


            /*
             * Update counter.
             */
            if (counter) {

                counter.textContent =
                    (currentIndex + 1)
                    +
                    ' / '
                    +
                    slides.length;
            }
        }


        /*
         * Previous.
         */
        if (prevButton) {

            prevButton.addEventListener(
                'click',
                function () {

                    showSlide(
                        currentIndex - 1
                    );
                }
            );
        }


        /*
         * Next.
         */
        if (nextButton) {

            nextButton.addEventListener(
                'click',
                function () {

                    showSlide(
                        currentIndex + 1
                    );
                }
            );
        }


        /*
         * Dot navigation.
         */
        dots.forEach(
            function (dot) {

                dot.addEventListener(
                    'click',
                    function () {

                        showSlide(
                            Number(
                                this.dataset.index
                            )
                        );
                    }
                );
            }
        );

    }
);

</script>

@endpush
