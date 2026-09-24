<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        Parent Sign In | Kumon Time Scheduling System
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>


<body class="font-sans antialiased">

    <div class="relative
           min-h-screen
           overflow-hidden
           bg-slate-900">


        {{-- =========================================================
        BACKGROUND IMAGE
    ========================================================== --}}

        <div class="absolute
               inset-0
               bg-cover
               bg-center
               bg-no-repeat"
            style="
            background-image:
                url('{{ asset('images/login.jpeg') }}');
        ">
        </div>



        {{-- =========================================================
        OVERLAY
    ========================================================== --}}

        <div
            class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/65
               via-slate-900/20
               to-slate-900/10">
        </div>


        <div class="absolute
               inset-0
               bg-white/5"></div>



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
               xl:px-20">

            <div
                class="mx-auto
                   grid
                   w-full
                   max-w-[1650px]
                   items-center
                   gap-12
                   lg:grid-cols-[1.05fr_0.95fr]
                   xl:gap-20">


                {{-- =================================================
                LEFT SIDE
            ================================================== --}}

                <section class="hidden
                       max-w-2xl
                       lg:block">


                    {{-- =============================================
                    BRAND
                ============================================== --}}

                    <div class="flex
                           items-center
                           gap-5">


                        {{-- Logo --}}
                        <div
                            class="flex
                               items-center
                               justify-center">

                            <img src="{{ asset('images/kumon-logo.png') }}" alt="Kumon Logo"
                                class="h-14
                                   w-auto
                                   object-contain
                                   xl:h-16">

                        </div>



                        {{-- Divider --}}
                        <div
                            class="h-12
                               w-px
                               bg-white/40">
                        </div>



                        {{-- Centre Name --}}
                        <div>

                            <p
                                class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-[0.20em]
                                   text-violet-200">
                                Kumon North Hobart
                            </p>


                            <p
                                class="mt-1
                                   text-lg
                                   font-semibold
                                   text-white">
                                Time Scheduling System
                            </p>

                        </div>

                    </div>



                    {{-- =============================================
                    MAIN HEADING
                ============================================== --}}

                    <h1
                        class="mt-12
                           max-w-[680px]
                           text-5xl
                           font-bold
                           leading-[1.08]
                           tracking-tight
                           text-white
                           xl:text-[60px]">
                        Stay connected with
                        <br>
                        your child's schedule.
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
                           xl:text-xl">
                        View classes, manage wishlist requests
                        and update leave information through
                        one simple Parent Portal.
                    </p>

                </section>



                {{-- =================================================
                LOGIN CARD
            ================================================== --}}

                <section class="mx-auto
                       w-full
                       max-w-[620px]">

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
                           xl:p-14">


                        {{-- =============================================
                        MOBILE LOGO
                    ============================================== --}}

                        <div class="mb-8
                               lg:hidden">

                            <div
                                class="flex
                                   items-center
                                   gap-4">


                                <div
                                    class="rounded-lg
                                       bg-white
                                       px-3
                                       py-2
                                       shadow-sm">

                                    <img src="{{ asset('images/kumon-logo.png') }}" alt="Kumon Logo"
                                        class="h-8
                                           w-auto
                                           object-contain">

                                </div>


                                <div
                                    class="h-9
                                       w-px
                                       bg-slate-300">
                                </div>


                                <div>

                                    <p
                                        class="text-[10px]
                                           font-bold
                                           uppercase
                                           tracking-[0.18em]
                                           text-violet-700">
                                        Kumon North Hobart
                                    </p>


                                    <p
                                        class="mt-1
                                           text-sm
                                           font-semibold
                                           text-slate-800">
                                        Time Scheduling System
                                    </p>

                                </div>

                            </div>

                        </div>



                        {{-- =============================================
                        CHANGE ACCOUNT TYPE
                    ============================================== --}}

                        <a href="{{ url('/') }}"
                            class="inline-flex
                               min-h-11
                               items-center
                               gap-2
                               rounded-full
                               border
                               border-violet-300
                               bg-white/70
                               px-6
                               text-sm
                               font-semibold
                               text-slate-700
                               transition
                               hover:border-violet-400
                               hover:bg-violet-50
                               hover:text-violet-800">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                class="h-4
                                   w-4">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />

                            </svg>

                            Change account type

                        </a>



                        {{-- =============================================
                        LOGIN TYPE
                    ============================================== --}}

                        <div class="mt-5">

                            <span
                                class="inline-flex
                                   rounded-full
                                   bg-violet-100
                                   px-5
                                   py-2
                                   text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-violet-800">
                                Parent Login
                            </span>

                        </div>



                        {{-- =============================================
                        HEADING
                    ============================================== --}}

                        <div class="mt-8">

                            <h2
                                class="text-4xl
                                   font-bold
                                   tracking-tight
                                   text-slate-800
                                   sm:text-5xl">
                                Parent sign in
                            </h2>


                            <p
                                class="mt-3
                                   text-base
                                   leading-relaxed
                                   text-slate-600
                                   sm:text-lg">
                                Enter your registered email address
                                and the date of birth of any one of
                                your linked students.
                            </p>

                        </div>



                        {{-- =============================================
                        STATUS
                    ============================================== --}}

                        @if (session('status'))
                            <div
                                class="mt-6
                                   rounded-xl
                                   border
                                   border-green-200
                                   bg-green-50
                                   px-4
                                   py-3
                                   text-sm
                                   text-green-700">
                                {{ session('status') }}
                            </div>
                        @endif



                        {{-- =============================================
                        GENERAL ERRORS
                    ============================================== --}}

                        @if ($errors->any())

                            <div
                                class="mt-6
                                   rounded-xl
                                   border
                                   border-red-200
                                   bg-red-50
                                   px-4
                                   py-3
                                   text-sm
                                   text-red-700">

                                <p class="font-semibold">
                                    Verification was unsuccessful.
                                </p>


                                <ul
                                    class="mt-1
                                       list-inside
                                       list-disc">

                                    @foreach ($errors->all() as $error)
                                        <li>
                                            {{ $error }}
                                        </li>
                                    @endforeach

                                </ul>

                            </div>

                        @endif



                        {{-- =============================================
                        FORM
                    ============================================== --}}

                        <form method="POST" action="{{ route('parent.login.check') }}"
                            class="mt-10
                               space-y-6">

                            @csrf



                            {{-- =========================================
                            EMAIL
                        ========================================== --}}

                            <div>

                                <label for="login"
                                    class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-slate-700">
                                    Email Address
                                </label>


                                <input id="login" type="email" name="login" value="{{ old('login') }}"
                                    placeholder="Enter your registered email" autocomplete="email" autofocus required
                                    class="block
                                       h-16
                                       w-full
                                       rounded-2xl
                                       border
                                       border-slate-300
                                       bg-white/70
                                       px-5
                                       text-base
                                       text-slate-800
                                       shadow-sm
                                       outline-none
                                       transition
                                       placeholder:text-slate-400
                                       focus:border-violet-500
                                       focus:ring-4
                                       focus:ring-violet-200/60">


                                @error('login')
                                    <p
                                        class="mt-2
                                           text-sm
                                           text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror


                                <p
                                    class="mt-2
                                       text-xs
                                       text-slate-500">
                                    Use the email address registered
                                    with the centre.
                                </p>

                            </div>



                            {{-- =========================================
                            STUDENT DOB
                        ========================================== --}}

                            <div>

                                <label for="date_of_birth"
                                    class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-slate-700">
                                    Student Date of Birth
                                </label>


                                <input id="date_of_birth" type="date" name="date_of_birth"
                                    value="{{ old('date_of_birth') }}" required
                                    class="block
                                       h-16
                                       w-full
                                       rounded-2xl
                                       border
                                       border-slate-300
                                       bg-white/70
                                       px-5
                                       text-base
                                       text-slate-800
                                       shadow-sm
                                       outline-none
                                       transition
                                       focus:border-violet-500
                                       focus:ring-4
                                       focus:ring-violet-200/60">


                                @error('date_of_birth')
                                    <p
                                        class="mt-2
                                           text-sm
                                           text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>



                            {{-- =========================================
                            SECURE VERIFICATION
                        ========================================== --}}

                            <div
                                class="rounded-2xl
                                   border
                                   border-violet-100
                                   bg-violet-50/70
                                   p-4">

                                <div
                                    class="flex
                                       items-start
                                       gap-3">

                                    <div
                                        class="flex
                                           h-10
                                           w-10
                                           shrink-0
                                           items-center
                                           justify-center
                                           rounded-xl
                                           bg-white
                                           text-violet-600
                                           shadow-sm">

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.8"
                                            class="h-5
                                               w-5">

                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5
                                               4.5 0 10-9 0v3.75" />

                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 10.5h13.5
                                               v9h-13.5z" />

                                        </svg>

                                    </div>


                                    <div>

                                        <p
                                            class="text-sm
                                               font-semibold
                                               text-violet-900">
                                            Secure verification
                                        </p>


                                        <p
                                            class="mt-1
                                               text-xs
                                               leading-relaxed
                                               text-violet-700">
                                            If the email is linked to a student
                                            with the entered date of birth,
                                            a 6-digit verification code will
                                            be sent to that email address.
                                        </p>

                                    </div>

                                </div>

                            </div>



                            {{-- =========================================
                            CONTINUE
                        ========================================== --}}

                            <button type="submit"
                                class="flex
                                   h-16
                                   w-full
                                   items-center
                                   justify-center
                                   gap-2
                                   rounded-2xl
                                   bg-violet-600
                                   px-6
                                   text-base
                                   font-bold
                                   text-white
                                   shadow-lg
                                   shadow-violet-500/20
                                   transition
                                   hover:bg-violet-700
                                   focus:outline-none
                                   focus:ring-4
                                   focus:ring-violet-300">

                                Continue


                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    class="h-4
                                       w-4">

                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />

                                </svg>

                            </button>

                        </form>

                    </div>

                </section>

            </div>

        </main>

    </div>

</body>

</html>
