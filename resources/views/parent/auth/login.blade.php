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


<body class="font-sans
           antialiased">

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


        <div
            class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/55
               via-slate-900/15
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
               px-5 py-10
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

                    <p
                        class="text-sm
                           font-semibold
                           uppercase
                           tracking-[0.15em]
                           text-white/90">
                        Kumon North Hobart
                    </p>


                    <h1
                        class="mt-8
                           max-w-2xl
                           text-5xl
                           font-bold
                           leading-[1.15]
                           text-white
                           xl:text-6xl">
                        Stay connected with your child's learning schedule.
                    </h1>


                    <p
                        class="mt-10
                           max-w-xl
                           text-xl
                           leading-relaxed
                           text-white/90
                           xl:text-2xl">
                        View classes, manage wishlist requests
                        and update leave information from one
                        simple Parent Portal.
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
                           bg-white/80
                           p-7
                           shadow-2xl
                           shadow-slate-900/25
                           backdrop-blur-2xl
                           sm:p-10
                           xl:p-14">


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
                               border-slate-300
                               bg-white/60
                               px-6
                               text-sm
                               font-semibold
                               text-slate-700
                               transition
                               hover:bg-white">

                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                            </svg>

                            Change account type

                        </a>



                        {{-- =============================================
                        LOGIN TYPE
                    ============================================== --}}

                        <div class="mt-5">

                            <span
                                class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-wide
                                   text-slate-800">
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
                                Enter your registered email or mobile number
                                and your student's date of birth.
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
                                   px-4 py-3
                                   text-sm
                                   text-green-700">
                                {{ session('status') }}
                            </div>
                        @endif



                        {{-- =============================================
                        ERRORS
                    ============================================== --}}

                        @if ($errors->any())

                            <div
                                class="mt-6
                                   rounded-xl
                                   border
                                   border-red-200
                                   bg-red-50
                                   px-4 py-3
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
                            EMAIL OR MOBILE
                        ========================================== --}}

                            <div>

                                <label for="login"
                                    class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-slate-700">
                                    Email or Mobile Number
                                </label>


                                <input id="login" type="text" name="login" value="{{ old('login') }}"
                                    placeholder="Email or mobile number" autocomplete="username" autofocus required
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
                                    Use the email address or mobile number
                                    registered with the centre.
                                </p>

                            </div>



                            {{-- =========================================
                            STUDENT DATE OF BIRTH
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
                            SECURE VERIFICATION INFO
                        ========================================== --}}

                            <div
                                class="rounded-2xl
                                   border
                                   border-slate-200
                                   bg-white/40
                                   p-4">

                                <p
                                    class="text-sm
                                       font-semibold
                                       text-slate-800">
                                    Secure verification
                                </p>


                                <p
                                    class="mt-1
                                       text-xs
                                       leading-relaxed
                                       text-slate-600">
                                    After your details are confirmed,
                                    a one-time verification code will be
                                    sent to your registered email or mobile number.
                                </p>

                            </div>



                            {{-- =========================================
                            CONTINUE
                        ========================================== --}}

                            <button
    type="submit"
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
           transition
           hover:bg-violet-700
           focus:outline-none
           focus:ring-4
           focus:ring-violet-300"
>
    Continue

    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        class="h-4 w-4"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m9 18 6-6-6-6"
        />
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
