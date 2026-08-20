<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Welcome | Kumon Parent Portal
    </title>


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body
    class="min-h-screen
           bg-gradient-to-br
           from-cyan-50
           via-white
           to-violet-50
           font-sans
           antialiased"
>

    <main
        class="min-h-screen
               px-5 py-10
               sm:px-8
               lg:px-12"
    >

        <div
            class="mx-auto
                   max-w-5xl"
        >


            {{-- =====================================================
                HEADER
            ====================================================== --}}

            <div
                class="flex
                       flex-col
                       gap-4
                       sm:flex-row
                       sm:items-center
                       sm:justify-between"
            >

                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-[0.16em]
                               text-violet-600"
                    >
                        Kumon North Hobart
                    </p>


                    <h1
                        class="mt-2
                               text-3xl
                               font-bold
                               text-slate-900
                               sm:text-4xl"
                    >
                        Welcome,
                        {{ $guardian->first_name }}
                    </h1>


                    <p
                        class="mt-2
                               text-sm
                               text-slate-500"
                    >
                        Select a student to continue
                        to the Parent Portal.
                    </p>

                </div>


                <div
                    class="rounded-2xl
                           border
                           border-violet-100
                           bg-white
                           px-5 py-3
                           shadow-sm"
                >

                    <p
                        class="text-xs
                               text-slate-400"
                    >
                        Signed in as
                    </p>


                    <p
                        class="mt-1
                               text-sm
                               font-semibold
                               text-slate-700"
                    >
                        {{ $guardian->first_name }}
                        {{ $guardian->last_name }}
                    </p>

                </div>

            </div>



            {{-- =====================================================
                STUDENT CARD AREA
            ====================================================== --}}

            <section
                class="mt-10
                       rounded-[30px]
                       border
                       border-slate-200
                       bg-white/80
                       p-6
                       shadow-xl
                       shadow-slate-200/50
                       backdrop-blur
                       sm:p-8"
            >

                <div>

                    <h2
                        class="text-xl
                               font-bold
                               text-slate-900"
                    >
                        Your Students
                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-slate-500"
                    >
                        You have access to
                        {{ $students->count() }}

                        {{
                            $students->count() === 1
                                ? 'student'
                                : 'students'
                        }}.
                    </p>

                </div>



                <div
                    class="mt-7
                           grid
                           gap-4
                           md:grid-cols-2"
                >

                    @forelse (
                        $students
                        as $student
                    )

                        @php

                            $initials =
                                strtoupper(
                                    mb_substr(
                                        $student->first_name,
                                        0,
                                        1
                                    )
                                    .
                                    mb_substr(
                                        $student->last_name,
                                        0,
                                        1
                                    )
                                );

                        @endphp


                        <form
                            method="POST"
                            action="{{ route(
                                'parent.students.select',
                                $student
                            ) }}"
                        >

                            @csrf


                            <button
                                type="submit"
                                class="group
                                       flex
                                       w-full
                                       items-center
                                       gap-4
                                       rounded-2xl
                                       border
                                       border-slate-200
                                       bg-white
                                       p-5
                                       text-left
                                       shadow-sm
                                       transition
                                       hover:-translate-y-0.5
                                       hover:border-violet-300
                                       hover:bg-violet-50
                                       hover:shadow-md"
                            >

                                {{-- Initials --}}
                                <div
                                    class="flex
                                           h-14 w-14
                                           shrink-0
                                           items-center
                                           justify-center
                                           rounded-full
                                           bg-gradient-to-br
                                           from-cyan-500
                                           to-violet-500
                                           text-lg
                                           font-bold
                                           text-white"
                                >
                                    {{ $initials }}
                                </div>



                                {{-- Student --}}
                                <div
                                    class="min-w-0
                                           flex-1"
                                >

                                    <p
                                        class="truncate
                                               text-lg
                                               font-bold
                                               text-slate-900"
                                    >
                                        {{ $student->first_name }}
                                        {{ $student->last_name }}
                                    </p>


                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-500"
                                    >
                                        Student ID:
                                        {{ $student->external_id }}
                                    </p>


                                    <span
                                        class="mt-2
                                               inline-flex
                                               rounded-full
                                               bg-green-100
                                               px-2.5 py-1
                                               text-xs
                                               font-semibold
                                               text-green-700"
                                    >
                                        Active
                                    </span>

                                </div>



                                {{-- Arrow --}}
                                <div
                                    class="text-xl
                                           text-violet-500
                                           transition
                                           group-hover:translate-x-1"
                                >
                                    →
                                </div>

                            </button>

                        </form>


                    @empty

                        <div
                            class="md:col-span-2
                                   rounded-2xl
                                   border
                                   border-dashed
                                   border-slate-300
                                   px-6 py-12
                                   text-center"
                        >

                            <p
                                class="font-semibold
                                       text-slate-700"
                            >
                                No students are linked
                                to this guardian.
                            </p>


                            <p
                                class="mt-2
                                       text-sm
                                       text-slate-500"
                            >
                                Please contact Kumon North Hobart.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>



            {{-- =====================================================
                INFORMATION
            ====================================================== --}}

            <div
                class="mt-6
                       rounded-2xl
                       border
                       border-cyan-100
                       bg-cyan-50
                       px-5 py-4"
            >

                <p
                    class="text-sm
                           text-cyan-800"
                >
                    If you have multiple students,
                    you can switch between them later
                    without signing in again.
                </p>

            </div>

        </div>

    </main>

</body>

</html>