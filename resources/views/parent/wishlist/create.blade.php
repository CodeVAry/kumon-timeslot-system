@extends('layouts.parent')

@section('title', 'Create Wishlist Request')

@section('page-title', 'Create Wishlist Request')


@section('content')

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


    <div class="mt-6">

        <p
            class="text-xs
                   font-bold
                   uppercase
                   tracking-wide
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
            Create Wishlist Request
        </h1>


        <p
            class="mt-2
                   text-sm
                   text-slate-500"
        >
            Select another preferred class time for

            <span class="font-semibold text-slate-700">
                {{ $student->first_name }}
                {{ $student->last_name }}
            </span>.
        </p>

    </div>



    @if ($errors->any())

        <div
            class="mt-6
                   rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            @foreach ($errors->all() as $error)

                <p class="text-sm text-red-700">
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif



    <section
        class="mt-7
               rounded-[26px]
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <form
            method="POST"
            action="{{ route(
                'parent.wishlist.store'
            ) }}"
        >

            @csrf


            {{-- Class Selection --}}
            <div>

                <label
                    for="enrolment_id"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700"
                >
                    Select Class
                </label>


                <select
                    name="enrolment_id"
                    id="enrolment_id"
                    required
                    class="mt-2
                           h-12
                           w-full
                           max-w-xl
                           rounded-xl
                           border-slate-300"
                >

                    <option value="">
                        Select a class
                    </option>


                    @foreach ($enrolments as $enrolment)

                        <option
                            value="{{ $enrolment->id }}"
                            @selected(
                                old('enrolment_id')
                                ==
                                $enrolment->id
                            )
                        >
                            {{
                                $enrolment
                                    ->sectionOffering
                                    ?->section
                                    ?->section_name
                                ?? 'Class'
                            }}

                            —

                            {{
                                $enrolment
                                    ->sectionOffering
                                    ?->day
                                    ?->day_name
                                ?? ''
                            }}

                            {{
                                $enrolment
                                    ->sectionOffering
                                ? \Carbon\Carbon::parse(
                                    $enrolment
                                        ->sectionOffering
                                        ->start_time
                                )->format('g:i A')
                                : ''
                            }}
                        </option>

                    @endforeach

                </select>

            </div>



            {{-- Offering groups --}}
            <div class="mt-7">

                @foreach ($enrolments as $enrolment)

                    <div
                        data-enrolment-options="{{ $enrolment->id }}"
                        class="{{
                            old('enrolment_id') == $enrolment->id
                                ? ''
                                : 'hidden'
                        }}"
                    >

                        @php
                            $current =
                                $enrolment
                                    ->sectionOffering;
                        @endphp


                        <div
                            class="rounded-xl
                                   bg-blue-50
                                   p-4"
                        >

                            <p
                                class="text-xs
                                       font-bold
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
                                }}
                            </p>


                            <p
                                class="mt-1
                                       text-sm
                                       text-slate-600"
                            >
                                {{
                                    $current
                                        ?->day
                                        ?->day_name
                                }}

                                ·

                                {{
                                    $current
                                    ? \Carbon\Carbon::parse(
                                        $current->start_time
                                    )->format('g:i A')
                                    : ''
                                }}

                                –

                                {{
                                    $current
                                    ? \Carbon\Carbon::parse(
                                        $current->end_time
                                    )->format('g:i A')
                                    : ''
                                }}
                            </p>

                        </div>


                        <p
                            class="mt-6
                                   text-sm
                                   font-bold
                                   text-slate-800"
                        >
                            Preferred Class Time
                        </p>


                        <div
                            class="mt-3
                                   grid
                                   gap-4
                                   md:grid-cols-2
                                   xl:grid-cols-3"
                        >

                            @forelse (
                                $enrolment
                                    ->available_offerings
                                as $offering
                            )

                                <label
                                    class="relative
                                           cursor-pointer"
                                >

                                    <input
                                        type="radio"
                                        name="section_offering_id"
                                        value="{{ $offering->id }}"
                                        class="peer sr-only"
                                        @checked(
                                            old('section_offering_id')
                                            ==
                                            $offering->id
                                        )
                                    >


                                    <div
                                        class="rounded-2xl
                                               border
                                               border-slate-300
                                               bg-slate-50
                                               p-5
                                               transition
                                               peer-checked:border-violet-600
                                               peer-checked:bg-violet-50
                                               peer-checked:ring-2
                                               peer-checked:ring-violet-500"
                                    >

                                        <div
                                            class="flex
                                                   justify-between"
                                        >

                                            <div>

                                                <p
                                                    class="font-bold
                                                           text-slate-900"
                                                >
                                                    {{
                                                        $offering
                                                            ->day
                                                            ?->day_name
                                                    }}
                                                </p>


                                                <p
                                                    class="mt-2
                                                           text-sm
                                                           text-slate-500"
                                                >
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
                                                </p>

                                            </div>


                                            <span
                                                class="h-5 w-5
                                                       rounded-full
                                                       border-2
                                                       border-slate-300
                                                       peer-checked:border-violet-600"
                                            ></span>

                                        </div>

                                    </div>

                                </label>


                            @empty

                                <p class="text-sm text-slate-500">
                                    No alternative class times available.
                                </p>

                            @endforelse

                        </div>

                    </div>

                @endforeach

            </div>



            <div
                class="mt-7
                       flex
                       justify-end
                       gap-3"
            >

                <a
                    href="{{ route(
                        'parent.wishlist.index'
                    ) }}"
                    class="inline-flex
                           h-12
                           items-center
                           rounded-xl
                           border
                           border-slate-300
                           px-6
                           text-sm
                           font-semibold
                           text-slate-600"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="h-12
                           rounded-xl
                           bg-violet-600
                           px-7
                           text-sm
                           font-bold
                           text-white
                           hover:bg-violet-700"
                >
                    Submit Wishlist Request
                </button>

            </div>

        </form>

    </section>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const classSelect =
            document.getElementById(
                'enrolment_id'
            );


        const groups =
            document.querySelectorAll(
                '[data-enrolment-options]'
            );


        function showOptions()
        {
            groups.forEach(
                function (group) {

                    if (
                        group.dataset
                            .enrolmentOptions
                        ===
                        classSelect.value
                    ) {

                        group.classList.remove(
                            'hidden'
                        );

                    } else {

                        group.classList.add(
                            'hidden'
                        );


                        group
                            .querySelectorAll(
                                'input[type="radio"]'
                            )
                            .forEach(
                                function (radio) {

                                    radio.checked =
                                        false;
                                }
                            );
                    }
                }
            );
        }


        classSelect.addEventListener(
            'change',
            showOptions
        );

    }
);

</script>

@endpush