@extends('layouts.parent')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')


@section('content')

<div
    class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8"
>


    {{-- =========================================================
        PAGE HEADING
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
                   px-5 py-2.5
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
        SUMMARY
    ========================================================== --}}

    <div
        class="mt-7
               grid
               gap-5
               md:grid-cols-3"
    >

        {{-- Classes --}}
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
                Active classes
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



        {{-- Wishlist --}}
        <div
            class="rounded-[24px]
                   border
                   border-purple-100
                   bg-purple-50
                   p-6"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Wishlist
            </p>


            <p
                class="mt-4
                       text-2xl
                       font-bold
                       text-purple-700"
            >
                Manage
            </p>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                Preferred class day and time
            </p>

        </div>



        {{-- Leave --}}
        <div
            class="rounded-[24px]
                   border
                   border-cyan-100
                   bg-cyan-100/60
                   p-6"
        >

            <p
                class="text-sm
                       font-semibold
                       text-slate-600"
            >
                Leave Management
            </p>


            <p
                class="mt-4
                       text-2xl
                       font-bold
                       text-cyan-700"
            >
                Manage
            </p>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                Add or update student leave
            </p>

        </div>

    </div>



    {{-- =========================================================
        MAIN AREA
    ========================================================== --}}

    <div
        class="mt-7
               grid
               gap-6
               xl:grid-cols-3"
    >


        {{-- =====================================================
            CLASS SLIDER
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
                                                ?? '—'
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
                                                ?? '—'
                                            }}
                                        </p>

                                    </div>



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

                                        </p>

                                    </div>



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
                                                    ? $offering->duration_minutes . ' min'
                                                    : '—'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach



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
                                       text-violet-700
                                       hover:bg-violet-50"
                            >
                                ←
                            </button>


                            <div class="flex gap-2">

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
                                       text-violet-700
                                       hover:bg-violet-50"
                            >
                                →
                            </button>

                        </div>

                    @endif

                </div>


            @else

                <div
                    class="px-6 py-14
                           text-center"
                >

                    <p
                        class="font-semibold
                               text-slate-700"
                    >
                        No active classes
                    </p>

                </div>

            @endif

        </section>



        {{-- =====================================================
            RIGHT SIDE
        ====================================================== --}}

        <div class="space-y-6">


            {{-- Wishlist --}}
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
                    Preferred class times
                </h3>


                <p
                    class="mt-2
                           text-sm
                           text-slate-600"
                >
                    Add or update a preferred
                    day and time for an enrolled class.
                </p>


                <button
                    type="button"
                    class="mt-5
                           rounded-xl
                           bg-purple-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-purple-700"
                >
                    Open Wishlist
                </button>

            </section>



            {{-- Leave --}}
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
                    Student leave
                </h3>


                <p
                    class="mt-2
                           text-sm
                           text-slate-600"
                >
                    Submit leave, update the expected
                    return date or return early.
                </p>


                <button
                    type="button"
                    class="mt-5
                           rounded-xl
                           bg-cyan-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-cyan-700"
                >
                    Open Leave
                </button>

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

        const prev =
            document.getElementById(
                'classPrev'
            );

        const next =
            document.getElementById(
                'classNext'
            );

        const counter =
            document.getElementById(
                'classCounter'
            );


        if (slides.length <= 1) {
            return;
        }


        let currentIndex = 0;


        function showSlide(index) {

            if (index < 0) {
                index = slides.length - 1;
            }

            if (index >= slides.length) {
                index = 0;
            }

            currentIndex = index;


            slides.forEach(
                function (slide, slideIndex) {

                    slide.classList.toggle(
                        'hidden',
                        slideIndex !== currentIndex
                    );
                }
            );


            dots.forEach(
                function (dot, dotIndex) {

                    dot.classList.toggle(
                        'bg-violet-600',
                        dotIndex === currentIndex
                    );

                    dot.classList.toggle(
                        'bg-violet-200',
                        dotIndex !== currentIndex
                    );
                }
            );


            if (counter) {

                counter.textContent =
                    (currentIndex + 1)
                    +
                    ' / '
                    +
                    slides.length;
            }
        }


        prev?.addEventListener(
            'click',
            function () {
                showSlide(currentIndex - 1);
            }
        );


        next?.addEventListener(
            'click',
            function () {
                showSlide(currentIndex + 1);
            }
        );


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