@extends('layouts.parent')

@section('title', 'Edit Wishlist Request')

@section('page-title', 'Edit Wishlist Request')


@section('content')

@php

    $current =
        $sourceEnrolment
            ->sectionOffering;

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
            'parent.wishlist.show',
            $wishlist
        ) }}"
        class="text-sm
               font-semibold
               text-violet-700"
    >
        ← Back to Wishlist Details
    </a>


    <div class="mt-6">

        <p
            class="text-xs
                   font-bold
                   uppercase
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
            Edit Wishlist Request
        </h1>

    </div>



    @if ($errors->any())

        <div
            class="mt-6
                   rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   p-4"
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
               p-6"
    >

        <div
            class="rounded-xl
                   bg-blue-50
                   p-5"
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
                       text-xl
                       font-bold
                       text-slate-900"
            >
                {{
                    $current
                        ?->section
                        ?->section_name
                }}
            </p>


            <p class="mt-1 text-sm text-slate-600">

                {{
                    $current
                        ?->day
                        ?->day_name
                }}

                ·

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

        </div>



        <form
            method="POST"
            action="{{ route(
                'parent.wishlist.update',
                $wishlist
            ) }}"
            class="mt-6"
        >

            @csrf
            @method('PATCH')


            <p
                class="text-sm
                       font-bold
                       text-slate-800"
            >
                Preferred Class Time
            </p>


            <div
                class="mt-4
                       grid
                       gap-4
                       md:grid-cols-2
                       xl:grid-cols-3"
            >

                @foreach (
                    $availableOfferings
                    as $offering
                )

                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="section_offering_id"
                            value="{{ $offering->id }}"
                            class="peer sr-only"
                            required
                            @checked(
                                old(
                                    'section_offering_id',
                                    $wishlist
                                        ->section_offering_id
                                )
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
                                   peer-checked:border-violet-600
                                   peer-checked:bg-violet-50
                                   peer-checked:ring-2
                                   peer-checked:ring-violet-500"
                        >

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
                                    )->format('g:i A')
                                }}

                                –

                                {{
                                    \Carbon\Carbon::parse(
                                        $offering
                                            ->end_time
                                    )->format('g:i A')
                                }}
                            </p>

                        </div>

                    </label>

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
                        'parent.wishlist.show',
                        $wishlist
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
                           text-white"
                >
                    Update Request
                </button>

            </div>

        </form>

    </section>

</div>

@endsection