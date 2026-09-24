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
                class="border-b
                       border-slate-200
                       bg-white
                       px-6 py-4
                       sm:px-10"
            >

                <div
                    class="flex
                           items-center
                           gap-4
                           sm:gap-5"
                >


                    {{-- =================================================
                        KUMON LOGO
                    ================================================== --}}

                    <div
                        class="flex
                               flex-shrink-0
                               items-center"
                    >

                        <img
                            src="{{ asset('images/kumon-logo.png') }}"
                            alt="Kumon Logo"
                            class="h-10
                                   w-auto
                                   object-contain
                                   sm:h-12"
                        >

                    </div>


                    {{-- =================================================
                        VERTICAL DIVIDER
                    ================================================== --}}

                    <div
                        class="hidden
                               h-11
                               w-px
                               bg-slate-300
                               sm:block"
                    ></div>


                    {{-- =================================================
                        SYSTEM NAME
                    ================================================== --}}

                    <div>

                        <p
                            class="text-[11px]
                                   font-bold
                                   uppercase
                                   tracking-[0.20em]
                                   text-cyan-600
                                   sm:text-xs"
                        >
                            Kumon North Hobart
                        </p>


                        <h1
                            class="mt-1
                                   text-base
                                   font-bold
                                   text-slate-900
                                   sm:text-xl"
                        >
                            Time Scheduling System
                        </h1>

                    </div>

                </div>

            </header>



            {{-- =====================================================
                MAIN CONTENT
            ====================================================== --}}

            <section
                class="relative
                       flex
                       flex-1
                       flex-col
                       items-center
                       justify-between
                       overflow-hidden
                       px-5
                       py-10
                       sm:px-10
                       sm:py-14"
            >


                {{-- =================================================
                    SOFT BACKGROUND DECORATION
                ================================================== --}}

                <div
                    class="pointer-events-none
                           absolute
                           -left-28
                           top-28
                           h-72
                           w-72
                           rounded-full
                           bg-cyan-50"
                ></div>


                <div
                    class="pointer-events-none
                           absolute
                           -right-24
                           top-20
                           h-72
                           w-72
                           rounded-full
                           bg-sky-50"
                ></div>


                <div
                    class="pointer-events-none
                           absolute
                           bottom-24
                           left-[8%]
                           h-10
                           w-10
                           rounded-full
                           bg-cyan-50"
                ></div>


                <div
                    class="pointer-events-none
                           absolute
                           right-[8%]
                           top-[45%]
                           h-12
                           w-12
                           rounded-full
                           bg-sky-50"
                ></div>



                <div
                    class="relative
                           z-10
                           w-full"
                >


                    {{-- =================================================
                        HEADING
                    ================================================== --}}

                    <div class="text-center">

                        <h2
                            class="text-3xl
                                   font-bold
                                   tracking-tight
                                   text-cyan-950
                                   sm:text-4xl
                                   lg:text-5xl"
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


                        {{-- Small decorative line --}}

                        <div
                            class="mx-auto
                                   mt-5
                                   flex
                                   items-center
                                   justify-center
                                   gap-1"
                        >

                            <span
                                class="h-1.5
                                       w-12
                                       rounded-full
                                       bg-cyan-400"
                            ></span>

                            <span
                                class="h-1.5
                                       w-4
                                       rounded-full
                                       bg-cyan-200"
                            ></span>

                            <span
                                class="h-1.5
                                       w-2
                                       rounded-full
                                       bg-cyan-100"
                            ></span>

                        </div>

                    </div>



                    {{-- =================================================
                        ACCOUNT CARDS
                    ================================================== --}}

                    <div
                        class="mx-auto
                               mt-10
                               grid
                               max-w-3xl
                               gap-7
                               sm:grid-cols-2
                               sm:gap-8
                               lg:mt-12"
                    >


                        {{-- =================================================
                            ADMIN
                        ================================================== --}}

                        <a
                            href="{{ route('login') }}"
                            class="group
                                   flex
                                   min-h-[380px]
                                   flex-col
                                   items-center
                                   justify-center
                                   rounded-3xl
                                   border
                                   border-slate-200
                                   bg-white
                                   p-8
                                   text-center
                                   shadow-sm
                                   transition
                                   duration-300
                                   hover:-translate-y-1
                                   hover:border-cyan-300
                                   hover:shadow-xl"
                        >


                            {{-- Admin Icon Background --}}

                            <div
                                class="flex
                                       h-44
                                       w-44
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-cyan-50
                                       transition
                                       duration-300
                                       group-hover:bg-cyan-100"
                            >


                                {{-- Admin SVG --}}

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 200 220"
                                    class="h-36
                                           w-36"
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


                                    {{-- Badge triangle --}}
                                    <path
                                        d="M100 154 L113 184 H87 Z"
                                        fill="#123B4A"
                                    />

                                </svg>

                            </div>



                            <h3
                                class="mt-5
                                       text-3xl
                                       font-bold
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


                            {{-- Arrow Button --}}

                            <div
                                class="mt-5
                                       flex
                                       h-11
                                       w-11
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-cyan-50
                                       text-cyan-600
                                       transition
                                       duration-300
                                       group-hover:bg-cyan-500
                                       group-hover:text-white"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5"
                                    />
                                </svg>

                            </div>

                        </a>



                        {{-- =================================================
                            PARENT
                        ================================================== --}}

                        <a
                            href="{{ route('parent.login') }}"
                            class="group
                                   flex
                                   min-h-[380px]
                                   flex-col
                                   items-center
                                   justify-center
                                   rounded-3xl
                                   border
                                   border-slate-200
                                   bg-white
                                   p-8
                                   text-center
                                   shadow-sm
                                   transition
                                   duration-300
                                   hover:-translate-y-1
                                   hover:border-violet-300
                                   hover:shadow-xl"
                        >


                            {{-- Parent Icon Background --}}

                            <div
                                class="flex
                                       h-44
                                       w-44
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-violet-50
                                       transition
                                       duration-300
                                       group-hover:bg-violet-100"
                            >


                                {{-- Parent SVG --}}

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 200 220"
                                    class="h-36
                                           w-36"
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
                                class="mt-5
                                       text-3xl
                                       font-bold
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


                            {{-- Arrow Button --}}

                            <div
                                class="mt-5
                                       flex
                                       h-11
                                       w-11
                                       items-center
                                       justify-center
                                       rounded-full
                                       bg-violet-50
                                       text-violet-600
                                       transition
                                       duration-300
                                       group-hover:bg-violet-500
                                       group-hover:text-white"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5"
                                    />
                                </svg>

                            </div>

                        </a>

                    </div>

                </div>



                {{-- =================================================
                    FOOTER
                ================================================== --}}

                <footer
                    class="relative
                           z-10
                           mt-14
                           text-center
                           text-sm
                           text-slate-500"
                >

                    Need help? Contact

                    <a
                        href="mailto:admin@kumonhobart.com"
                        class="font-semibold
                               text-cyan-500
                               transition
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
