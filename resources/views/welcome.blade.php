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
        Kumon Time Scheduling System
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body
    class="bg-slate-100
           font-sans
           antialiased"
>

    <main class="min-h-screen">

        <div
            class="mx-auto
                   flex
                   min-h-screen
                   w-full
                   max-w-[1500px]
                   flex-col
                   bg-white"
        >

            {{-- =====================================================
                HEADER
            ====================================================== --}}

            <header
                class="flex
                       items-center
                       justify-between
                       border-b
                       border-slate-200
                       px-6 py-5
                       sm:px-10"
            >

                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-[0.18em]
                               text-cyan-600"
                    >
                        Kumon North Hobart
                    </p>


                    <h1
                        class="mt-1
                               text-lg
                               font-bold
                               text-slate-800
                               sm:text-xl"
                    >
                        Time Scheduling System
                    </h1>

                </div>


                <a
                    href="{{ route('login') }}"
                    class="rounded-xl
                           border
                           border-cyan-500
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-cyan-700
                           transition
                           hover:bg-cyan-50"
                >
                    Admin Sign In
                </a>

            </header>



            {{-- =====================================================
                MAIN CONTENT
            ====================================================== --}}

            <section
                class="flex
                       flex-1
                       flex-col
                       items-center
                       justify-between
                       px-5 py-10
                       sm:px-10
                       sm:py-14"
            >

                <div class="w-full">


                    {{-- =================================================
                        HEADING
                    ================================================== --}}

                    <div class="text-center">

                        <h2
                            class="text-3xl
                                   font-semibold
                                   tracking-tight
                                   text-cyan-950
                                   sm:text-4xl"
                        >
                            Tell us who you are
                        </h2>


                        <p
                            class="mx-auto
                                   mt-3
                                   max-w-xl
                                   text-sm
                                   text-slate-500
                                   sm:text-base"
                        >
                            Select your account type to continue.
                        </p>

                    </div>



                    {{-- =================================================
                        ACCOUNT CARDS
                    ================================================== --}}

                    <div
                        class="mx-auto
                               mt-12
                               grid
                               max-w-3xl
                               gap-8
                               sm:grid-cols-2
                               sm:gap-12"
                    >


                        {{-- =================================================
                            ADMIN
                        ================================================== --}}

                        <a
                            href="{{ route('login') }}"
                            class="group
                                   flex
                                   min-h-[335px]
                                   flex-col
                                   items-center
                                   justify-center
                                   rounded-3xl
                                   border-2
                                   border-transparent
                                   bg-slate-50
                                   p-8
                                   text-center
                                   transition
                                   duration-200
                                   hover:-translate-y-1
                                   hover:border-cyan-400
                                   hover:bg-cyan-50
                                   hover:shadow-xl"
                        >

                            <div
                                class="flex
                                       h-48 w-48
                                       items-center
                                       justify-center"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 200 220"
                                    class="h-full w-full"
                                >

                                    {{-- Hair --}}
                                    <ellipse
                                        cx="100"
                                        cy="47"
                                        rx="45"
                                        ry="25"
                                        fill="#343A43"
                                    />


                                    {{-- Face --}}
                                    <circle
                                        cx="100"
                                        cy="77"
                                        r="34"
                                        fill="#FDBA74"
                                    />


                                    {{-- Neck --}}
                                    <rect
                                        x="87"
                                        y="103"
                                        width="26"
                                        height="25"
                                        fill="#FDBA74"
                                    />


                                    {{-- Body --}}
                                    <rect
                                        x="52"
                                        y="122"
                                        width="96"
                                        height="80"
                                        rx="34"
                                        fill="#FFFFFF"
                                    />


                                    {{-- Badge --}}
                                    <rect
                                        x="72"
                                        y="142"
                                        width="56"
                                        height="55"
                                        rx="11"
                                        fill="#28A9D3"
                                    />


                                    <path
                                        d="M100 154 L113 184 H87 Z"
                                        fill="#123B4A"
                                    />

                                </svg>

                            </div>


                            <h3
                                class="mt-3
                                       text-3xl
                                       font-medium
                                       text-cyan-950
                                       transition
                                       group-hover:text-cyan-700"
                            >
                                Admin
                            </h3>


                            <p
                                class="mt-2
                                       text-sm
                                       text-slate-500"
                            >
                                Centre staff access
                            </p>

                        </a>



                        {{-- =================================================
                            PARENT
                        ================================================== --}}

                        <a
                            href="{{ route('parent.login') }}"
                            class="group
                                   flex
                                   min-h-[335px]
                                   flex-col
                                   items-center
                                   justify-center
                                   rounded-3xl
                                   border-2
                                   border-transparent
                                   bg-slate-50
                                   p-8
                                   text-center
                                   transition
                                   duration-200
                                   hover:-translate-y-1
                                   hover:border-violet-400
                                   hover:bg-violet-50
                                   hover:shadow-xl"
                        >

                            <div
                                class="flex
                                       h-48 w-48
                                       items-center
                                       justify-center"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 200 220"
                                    class="h-full w-full"
                                >

                                    {{-- Parent head --}}
                                    <circle
                                        cx="120"
                                        cy="62"
                                        r="28"
                                        fill="#FDBA74"
                                    />


                                    {{-- Parent hair --}}
                                    <ellipse
                                        cx="120"
                                        cy="45"
                                        rx="37"
                                        ry="22"
                                        fill="#6F60B5"
                                    />


                                    {{-- Parent body --}}
                                    <rect
                                        x="80"
                                        y="92"
                                        width="82"
                                        height="105"
                                        rx="38"
                                        fill="#2DAAD3"
                                    />


                                    {{-- Child head --}}
                                    <circle
                                        cx="60"
                                        cy="121"
                                        r="22"
                                        fill="#FDBA74"
                                    />


                                    {{-- Child body --}}
                                    <rect
                                        x="34"
                                        y="142"
                                        width="58"
                                        height="57"
                                        rx="25"
                                        fill="#FA8550"
                                    />

                                </svg>

                            </div>


                            <h3
                                class="mt-3
                                       text-3xl
                                       font-medium
                                       text-cyan-950
                                       transition
                                       group-hover:text-violet-700"
                            >
                                Parent
                            </h3>


                            <p
                                class="mt-2
                                       text-sm
                                       text-slate-500"
                            >
                                Parent Portal access
                            </p>

                        </a>

                    </div>

                </div>



                {{-- =================================================
                    FOOTER
                ================================================== --}}

                <footer
                    class="mt-14
                           text-center
                           text-sm
                           text-slate-500"
                >

                    Need help? Contact

                    <a
                        href="mailto:admin@kumonhobart.com"
                        class="font-medium
                               text-cyan-500
                               hover:text-cyan-700"
                    >
                        Kumon North Hobart.
                    </a>

                </footer>

            </section>

        </div>

    </main>

</body>

</html>
