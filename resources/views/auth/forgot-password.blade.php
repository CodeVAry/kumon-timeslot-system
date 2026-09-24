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
        Forgot Password | Kumon Time Scheduling System
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="font-sans antialiased">

<div
    class="relative
           min-h-screen
           overflow-hidden
           bg-slate-900"
>


    {{-- =========================================================
        BACKGROUND IMAGE
    ========================================================== --}}

    <div
        class="absolute
               inset-0
               bg-cover
               bg-center
               bg-no-repeat"
        style="
            background-image:
                url('{{ asset('images/login.jpeg') }}');
        "
    ></div>



    {{-- =========================================================
        BACKGROUND OVERLAY
    ========================================================== --}}

    <div
        class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/70
               via-slate-900/30
               to-slate-900/10"
    ></div>


    <div
        class="absolute
               inset-0
               bg-white/5"
    ></div>



    {{-- =========================================================
        PAGE
    ========================================================== --}}

    <main
        class="relative
               z-10
               flex
               min-h-screen
               items-center
               px-5
               py-10
               sm:px-8
               lg:px-14
               xl:px-20"
    >

        <div
            class="mx-auto
                   grid
                   w-full
                   max-w-[1650px]
                   items-center
                   gap-12
                   lg:grid-cols-[1.05fr_0.95fr]
                   xl:gap-20"
        >


            {{-- =================================================
                LEFT SIDE
            ================================================== --}}

            <section
                class="hidden
                       max-w-2xl
                       lg:block"
            >


                {{-- =============================================
                    BRAND
                ============================================== --}}

                <div
                    class="flex
                           items-center
                           gap-5"
                >


                    {{-- Logo --}}
                    <div
                        class="flex
                               items-center
                               justify-center"
                    >

                        <img
                            src="{{ asset('images/kumon-logo.png') }}"
                            alt="Kumon Logo"
                            class="h-14
                                   w-auto
                                   object-contain
                                   xl:h-16"
                        >

                    </div>



                    {{-- Divider --}}
                    <div
                        class="h-12
                               w-px
                               bg-white/40"
                    ></div>



                    {{-- System Name --}}
                    <div>

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-[0.20em]
                                   text-cyan-200"
                        >
                            Kumon North Hobart
                        </p>


                        <p
                            class="mt-1
                                   text-lg
                                   font-semibold
                                   text-white"
                        >
                            Time Scheduling System
                        </p>

                    </div>

                </div>



                {{-- =============================================
                    HEADING
                ============================================== --}}

                <h1
                    class="mt-12
                           max-w-[680px]
                           text-5xl
                           font-bold
                           leading-[1.08]
                           tracking-tight
                           text-white
                           xl:text-[60px]"
                >
                    Forgot your
                    <br>
                    password?
                </h1>



                {{-- =============================================
                    DESCRIPTION
                ============================================== --}}

                <p
                    class="mt-7
                           max-w-xl
                           text-lg
                           leading-relaxed
                           text-white/90
                           xl:text-xl"
                >
                    Enter your registered email address and
                    we'll send you a secure link to create
                    a new password.
                </p>


                {{-- =============================================
                    SMALL INFORMATION
                ============================================== --}}

                <div
                    class="mt-8
                           flex
                           max-w-md
                           items-start
                           gap-3
                           rounded-2xl
                           border
                           border-white/20
                           bg-white/10
                           p-4
                           backdrop-blur-sm"
                >

                    <div
                        class="flex
                               h-10
                               w-10
                               shrink-0
                               items-center
                               justify-center
                               rounded-xl
                               bg-white/10
                               text-cyan-200"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-5
                                   w-5"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 5.25a3.75 3.75
                                   0 1 1-5.89 4.95L3.75
                                   16.31v3.94h3.94v-2.63
                                   h2.63V15h2.63l1.69-1.69"
                            />

                        </svg>

                    </div>


                    <div>

                        <p
                            class="text-sm
                                   font-semibold
                                   text-white"
                        >
                            Secure password recovery
                        </p>


                        <p
                            class="mt-1
                                   text-sm
                                   leading-relaxed
                                   text-white/70"
                        >
                            The reset link will only be sent
                            to an email registered in the system.
                        </p>

                    </div>

                </div>

            </section>



            {{-- =================================================
                RESET PASSWORD CARD
            ================================================== --}}

            <section
                class="mx-auto
                       w-full
                       max-w-[620px]"
            >

                <div
                    class="rounded-[34px]
                           border
                           border-white/60
                           bg-white/85
                           p-7
                           shadow-2xl
                           shadow-slate-900/25
                           backdrop-blur-2xl
                           sm:p-10
                           xl:p-14"
                >


                    {{-- =============================================
                        MOBILE BRAND
                    ============================================== --}}

                    <div
                        class="mb-8
                               lg:hidden"
                    >

                        <div
                            class="flex
                                   items-center
                                   gap-4"
                        >

                            <div
                                class="flex
                                       items-center
                                       justify-center"
                            >

                                <img
                                    src="{{ asset('images/kumon-logo.png') }}"
                                    alt="Kumon Logo"
                                    class="h-10
                                           w-auto
                                           object-contain"
                                >

                            </div>


                            <div
                                class="h-9
                                       w-px
                                       bg-slate-300"
                            ></div>


                            <div>

                                <p
                                    class="text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-[0.18em]
                                           text-cyan-700"
                                >
                                    Kumon North Hobart
                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           font-semibold
                                           text-slate-800"
                                >
                                    Time Scheduling System
                                </p>

                            </div>

                        </div>

                    </div>



                    {{-- =============================================
                        BACK TO LOGIN
                    ============================================== --}}

                    <a
                        href="{{ route('login') }}"
                        class="inline-flex
                               min-h-11
                               items-center
                               gap-2
                               rounded-full
                               border
                               border-cyan-400
                               bg-cyan-50/80
                               px-5
                               text-sm
                               font-semibold
                               text-cyan-800
                               transition
                               hover:bg-cyan-100"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-4
                                   w-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 18l-6-6 6-6"
                            />
                        </svg>

                        Back to sign in

                    </a>



                    {{-- =============================================
                        PAGE TYPE
                    ============================================== --}}

                    <div class="mt-5">

                        <span
                            class="inline-flex
                                   rounded-full
                                   bg-cyan-100/80
                                   px-5
                                   py-2
                                   text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-cyan-800"
                        >
                            Password Recovery
                        </span>

                    </div>



                    {{-- =============================================
                        HEADING
                    ============================================== --}}

                    <div class="mt-8">

                        <h1
                            class="text-4xl
                                   font-bold
                                   tracking-tight
                                   text-slate-800
                                   sm:text-5xl"
                        >
                            Reset your password
                        </h1>


                        <p
                            class="mt-4
                                   text-base
                                   leading-relaxed
                                   text-slate-600
                                   sm:text-lg"
                        >
                            Enter the email address associated
                            with your account. We'll email you
                            a password reset link.
                        </p>

                    </div>



                    {{-- =============================================
                        SESSION STATUS
                    ============================================== --}}

                    @if (session('status'))

                        <div
                            class="mt-6
                                   flex
                                   items-start
                                   gap-3
                                   rounded-2xl
                                   border
                                   border-green-200
                                   bg-green-50
                                   px-4
                                   py-4
                                   text-green-700"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="mt-0.5
                                       h-5
                                       w-5
                                       shrink-0"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m4.5 12.75 6 6
                                       9-13.5"
                                />

                            </svg>


                            <p
                                class="text-sm
                                       leading-relaxed"
                            >
                                {{ session('status') }}
                            </p>

                        </div>

                    @endif



                    {{-- =============================================
                        FORM
                    ============================================== --}}

                    <form
                        method="POST"
                        action="{{ route('password.email') }}"
                        class="mt-9"
                    >

                        @csrf



                        {{-- =========================================
                            EMAIL
                        ========================================== --}}

                        <div>

                            <label
                                for="email"
                                class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                Email address
                            </label>


                            <div class="relative">

                                <div
                                    class="pointer-events-none
                                           absolute
                                           inset-y-0
                                           left-0
                                           flex
                                           items-center
                                           pl-5
                                           text-slate-400"
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-5
                                               w-5"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M21.75 6.75v10.5
                                               a2.25 2.25 0 0 1
                                               -2.25 2.25h-15
                                               a2.25 2.25 0 0 1
                                               -2.25-2.25V6.75"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m3 6 9 6 9-6"
                                        />

                                    </svg>

                                </div>


                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your registered email"
                                    autocomplete="email"
                                    autofocus
                                    required
                                    class="block
                                           h-16
                                           w-full
                                           rounded-2xl
                                           border
                                           border-cyan-300
                                           bg-white/70
                                           pl-14
                                           pr-5
                                           text-base
                                           text-slate-800
                                           shadow-sm
                                           outline-none
                                           transition
                                           placeholder:text-slate-400
                                           focus:border-cyan-500
                                           focus:ring-4
                                           focus:ring-cyan-200/60"
                                >

                            </div>


                            @error('email')

                                <p
                                    class="mt-2
                                           text-sm
                                           text-red-600"
                                >
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>



                        {{-- =========================================
                            RESET BUTTON
                        ========================================== --}}

                        <button
                            type="submit"
                            class="mt-7
                                   flex
                                   h-16
                                   w-full
                                   items-center
                                   justify-center
                                   gap-2
                                   rounded-2xl
                                   bg-gradient-to-r
                                   from-cyan-500
                                   to-sky-500
                                   px-6
                                   text-base
                                   font-bold
                                   text-white
                                   shadow-lg
                                   shadow-cyan-500/25
                                   transition
                                   hover:from-cyan-600
                                   hover:to-sky-600
                                   focus:outline-none
                                   focus:ring-4
                                   focus:ring-cyan-300"
                        >

                            Email Password Reset Link


                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="h-4
                                       w-4"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m9 18 6-6-6-6"
                                />

                            </svg>

                        </button>

                    </form>



                    {{-- =============================================
                        HELP TEXT
                    ============================================== --}}

                    <div
                        class="mt-7
                               rounded-2xl
                               border
                               border-slate-200
                               bg-white/50
                               p-4"
                    >

                        <div
                            class="flex
                                   items-start
                                   gap-3"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="mt-0.5
                                       h-5
                                       w-5
                                       shrink-0
                                       text-cyan-600"
                            >

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 10v6"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 7.25h.01"
                                />

                            </svg>


                            <p
                                class="text-sm
                                       leading-relaxed
                                       text-slate-600"
                            >
                                Check your inbox and spam folder after
                                submitting the form. Follow the link in
                                the email to choose a new password.
                            </p>

                        </div>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

</body>

</html>
