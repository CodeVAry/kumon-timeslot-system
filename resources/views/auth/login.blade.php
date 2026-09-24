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
        Admin Sign In | Kumon Time Scheduling System
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
        PAGE CONTENT
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


            {{-- =====================================================
                LEFT SIDE
            ====================================================== --}}

            <section
                class="hidden
                       max-w-2xl
                       lg:block"
            >


                {{-- =================================================
                    BRAND
                ================================================== --}}

                <div
                    class="flex
                           items-center
                           gap-5"
                >


                    {{-- Kumon Logo --}}

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
                                   tracking-[0.22em]
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



                {{-- =================================================
                    MAIN HEADING
                ================================================== --}}

                <h1
                    class="mt-12
                           max-w-[680px]
                           text-5xl
                           font-bold
                           leading-[1.08]
                           tracking-tight
                           text-white
                           xl:text-[64px]"
                >
                    Smarter scheduling
                    <br>
                    for every class.
                </h1>



                {{-- =================================================
                    DESCRIPTION
                ================================================== --}}

                <p
                    class="mt-7
                           max-w-xl
                           text-lg
                           leading-relaxed
                           text-white/90
                           xl:text-xl"
                >
                    Manage students, classes, capacity and scheduling
                    from one clear and simple dashboard.
                </p>

            </section>



            {{-- =====================================================
                LOGIN CARD
            ====================================================== --}}

            <section
                class="mx-auto
                       w-full
                       max-w-[620px]"
            >

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
                           xl:p-14"
                >


                    {{-- =================================================
                        MOBILE BRAND
                    ================================================== --}}

                    <div
                        class="mb-8
                               lg:hidden"
                    >

                        <div
                            class="flex
                                   items-center
                                   gap-4"
                        >


                            <img
                                src="{{ asset('images/kumon-logo-transparent.png') }}"
                                alt="Kumon Logo"
                                class="h-10
                                       w-auto
                                       object-contain"
                            >


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



                    {{-- =================================================
                        CHANGE ACCOUNT TYPE
                    ================================================== --}}

                    <a
                        href="{{ url('/') }}"
                        class="inline-flex
                               min-h-11
                               items-center
                               gap-2
                               rounded-full
                               border
                               border-cyan-400
                               bg-cyan-50/80
                               px-6
                               text-sm
                               font-semibold
                               text-cyan-800
                               transition
                               duration-200
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

                        Change account type

                    </a>



                    {{-- =================================================
                        LOGIN TYPE
                    ================================================== --}}

                    <div class="mt-4">

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
                            Admin Login
                        </span>

                    </div>



                    {{-- =================================================
                        HEADING
                    ================================================== --}}

                    <div class="mt-8">

                        <h2
                            class="text-4xl
                                   font-bold
                                   tracking-tight
                                   text-slate-800
                                   sm:text-5xl"
                        >
                            Admin sign in
                        </h2>


                        <p
                            class="mt-3
                                   text-base
                                   leading-relaxed
                                   text-slate-600
                                   sm:text-lg"
                        >
                            Manage students, classes, capacity
                            and centre operations.
                        </p>

                    </div>



                    {{-- =================================================
                        SESSION STATUS
                    ================================================== --}}

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
                                   text-green-700"
                        >
                            {{ session('status') }}
                        </div>

                    @endif



                    {{-- =================================================
                        VALIDATION ERRORS
                    ================================================== --}}

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
                                   text-red-700"
                        >

                            <p class="font-semibold">
                                Sign in was unsuccessful.
                            </p>


                            <ul
                                class="mt-1
                                       list-inside
                                       list-disc"
                            >

                                @foreach ($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif



                    {{-- =================================================
                        LOGIN FORM
                    ================================================== --}}

                    <form
                        method="POST"
                        action="{{ route('login') }}"
                        class="mt-10
                               space-y-6"
                    >

                        @csrf



                        {{-- =================================================
                            EMAIL
                        ================================================== --}}

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


                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="admin@kumonhobart.com"
                                autocomplete="username"
                                autofocus
                                required
                                class="block
                                       h-16
                                       w-full
                                       rounded-2xl
                                       border
                                       border-cyan-300
                                       bg-white/60
                                       px-5
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



                        {{-- =================================================
                            PASSWORD
                        ================================================== --}}

                        <div>

                            <label
                                for="password"
                                class="mb-2
                                       block
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                Password
                            </label>


                            <div class="relative">

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                    class="block
                                           h-16
                                           w-full
                                           rounded-2xl
                                           border
                                           border-cyan-300
                                           bg-white/60
                                           px-5
                                           pr-20
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



                                {{-- =================================================
                                    PASSWORD TOGGLE
                                ================================================== --}}

                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute
                                           right-4
                                           top-1/2
                                           inline-flex
                                           h-10
                                           w-10
                                           -translate-y-1/2
                                           items-center
                                           justify-center
                                           rounded-full
                                           text-slate-500
                                           transition
                                           hover:bg-cyan-50
                                           hover:text-cyan-700
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-cyan-300"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >


                                    {{-- Eye Open --}}

                                    <svg
                                        id="eyeOpenIcon"
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
                                            d="M2.25 12s3.75-6 9.75-6
                                               9.75 6 9.75 6
                                               -3.75 6-9.75 6
                                               S2.25 12 2.25 12Z"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="3"
                                        />

                                    </svg>



                                    {{-- Eye Closed --}}

                                    <svg
                                        id="eyeClosedIcon"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="hidden
                                               h-5
                                               w-5"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18
                                               M10.6 10.6A2 2 0 0 0
                                               13.4 13.4
                                               M9.9 4.24A10.7 10.7 0 0 1
                                               12 4c6 0 9.75 8 9.75 8
                                               a17.7 17.7 0 0 1-2.03 2.8
                                               M6.61 6.61C3.88 8.46
                                               2.25 12 2.25 12s3.75 8
                                               9.75 8c1.56 0 2.94-.41
                                               4.12-1.04"
                                        />

                                    </svg>

                                </button>

                            </div>


                            @error('password')

                                <p
                                    class="mt-2
                                           text-sm
                                           text-red-600"
                                >
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>



                        {{-- =================================================
                            REMEMBER / FORGOT PASSWORD
                        ================================================== --}}

                        <div
                            class="flex
                                   flex-wrap
                                   items-center
                                   justify-between
                                   gap-3"
                        >


                            <label
                                for="remember_me"
                                class="inline-flex
                                       cursor-pointer
                                       items-center
                                       gap-2"
                            >

                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="rounded
                                           border-slate-300
                                           text-cyan-600
                                           focus:ring-cyan-500"
                                >


                                <span
                                    class="text-sm
                                           text-slate-600"
                                >
                                    Remember me
                                </span>

                            </label>



                            @if (Route::has('password.request'))

                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-sm
                                           font-semibold
                                           text-cyan-700
                                           transition
                                           hover:text-cyan-900"
                                >
                                    Forgot password?
                                </a>

                            @endif

                        </div>



                        {{-- =================================================
                            SIGN IN
                        ================================================== --}}

                        <button
                            type="submit"
                            class="flex
                                   h-16
                                   w-full
                                   items-center
                                   justify-center
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
                            Sign in
                        </button>

                    </form>



                    {{-- =================================================
                        REGISTER
                    ================================================== --}}

                    <div
                        class="mt-7
                               text-center
                               text-sm
                               text-slate-600"
                    >

                        Don't have an account?

                        <a
                            href="{{ route('register') }}"
                            class="font-bold
                                   text-cyan-700
                                   transition
                                   hover:text-cyan-900"
                        >
                            Sign up
                        </a>

                    </div>



                    {{-- =================================================
                        MOBILE INFORMATION
                    ================================================== --}}

                    <div
                        class="mt-8
                               rounded-2xl
                               border
                               border-cyan-100
                               bg-cyan-50/50
                               p-4
                               lg:hidden"
                    >

                        <p
                            class="text-sm
                                   leading-relaxed
                                   text-slate-600"
                        >
                            Manage students, classes, capacity
                            and scheduling from one simple dashboard.
                        </p>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>



{{-- =============================================================
    PASSWORD TOGGLE SCRIPT
============================================================== --}}

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const passwordInput =
                document.getElementById('password');

            const toggleButton =
                document.getElementById('togglePassword');

            const eyeOpenIcon =
                document.getElementById('eyeOpenIcon');

            const eyeClosedIcon =
                document.getElementById('eyeClosedIcon');


            if (
                !passwordInput
                ||
                !toggleButton
                ||
                !eyeOpenIcon
                ||
                !eyeClosedIcon
            ) {
                return;
            }


            toggleButton.addEventListener(
                'click',
                function () {

                    const showPassword =
                        passwordInput.type === 'password';


                    passwordInput.type =
                        showPassword
                            ? 'text'
                            : 'password';


                    eyeOpenIcon.classList.toggle(
                        'hidden',
                        showPassword
                    );


                    eyeClosedIcon.classList.toggle(
                        'hidden',
                        !showPassword
                    );


                    toggleButton.setAttribute(
                        'aria-label',
                        showPassword
                            ? 'Hide password'
                            : 'Show password'
                    );


                    toggleButton.setAttribute(
                        'aria-pressed',
                        showPassword
                            ? 'true'
                            : 'false'
                    );

                }
            );

        }
    );

</script>

</body>

</html>
