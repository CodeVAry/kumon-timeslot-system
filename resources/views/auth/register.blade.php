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
        Create Account | Kumon Time Scheduling System
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>


<body class="font-sans antialiased">

    <div
        class="relative min-h-screen overflow-hidden
               bg-slate-900"
    >

        {{-- =====================================================
            BACKGROUND
        ====================================================== --}}

        <div
            class="absolute inset-0
                   bg-cover
                   bg-center
                   bg-no-repeat"
            style="
                background-image:
                    url('{{ asset('images/login.jpeg') }}');
            "
        ></div>


        <div
            class="absolute inset-0
                   bg-gradient-to-r
                   from-slate-950/60
                   via-slate-900/20
                   to-slate-900/10"
        ></div>


        <div
            class="absolute inset-0
                   bg-white/5"
        ></div>



        <main
            class="relative z-10
                   flex min-h-screen
                   items-center
                   px-5 py-8
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
                    LEFT INFORMATION
                ================================================== --}}

                <section
                    class="hidden
                           max-w-2xl
                           lg:block"
                >

                    <p
                        class="text-sm
                               font-semibold
                               uppercase
                               tracking-[0.15em]
                               text-cyan-200"
                    >
                        Kumon North Hobart
                    </p>


                    <h1
                        class="mt-8
                               max-w-2xl
                               text-5xl
                               font-bold
                               leading-[1.15]
                               text-white
                               xl:text-6xl"
                    >
                        Smarter scheduling for every learning class.
                    </h1>


                    <p
                        class="mt-10
                               max-w-xl
                               text-xl
                               leading-relaxed
                               text-white/90
                               xl:text-2xl"
                    >
                        Create a staff account to manage students,
                        guardians, classes, capacity and wishlists.
                    </p>

                </section>



                {{-- =================================================
                    REGISTER CARD
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
                               bg-white/75
                               p-7
                               shadow-2xl
                               shadow-slate-900/25
                               backdrop-blur-2xl
                               sm:p-10
                               xl:p-12"
                    >


                        {{-- =========================================
                            BACK BUTTON
                        ========================================== --}}

                        <a
                            href="{{ route('welcome') }}"
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
                                   hover:bg-cyan-100"
                        >

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
                                    d="M15 18l-6-6 6-6"
                                />
                            </svg>

                            Back
                        </a>



                        {{-- =========================================
                            ACCOUNT TYPE
                        ========================================== --}}

                        <div class="mt-4">

                            <span
                                class="inline-flex
                                       rounded-full
                                       bg-cyan-100/80
                                       px-5 py-2
                                       text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Account Registration
                            </span>

                        </div>



                        {{-- =========================================
                            HEADING
                        ========================================== --}}

                        <div class="mt-7">

                            <h2
                                class="text-4xl
                                       font-bold
                                       tracking-tight
                                       text-slate-800
                                       sm:text-5xl"
                            >
                                Create account
                            </h2>


                            <p
                                class="mt-3
                                       text-base
                                       leading-relaxed
                                       text-slate-600
                                       sm:text-lg"
                            >
                                Enter your details.
                                We will send a verification code
                                to your email before creating the account.
                            </p>

                        </div>



                        {{-- =========================================
                            SESSION ERROR
                        ========================================== --}}

                        @if (session('error'))

                            <div
                                class="mt-6
                                       rounded-xl
                                       border
                                       border-red-200
                                       bg-red-50
                                       px-4 py-3
                                       text-sm
                                       text-red-700"
                            >
                                {{ session('error') }}
                            </div>

                        @endif



                        {{-- =========================================
                            VALIDATION ERRORS
                        ========================================== --}}

                        @if ($errors->any())

                            <div
                                class="mt-6
                                       rounded-xl
                                       border
                                       border-red-200
                                       bg-red-50
                                       px-4 py-3
                                       text-sm
                                       text-red-700"
                            >

                                <p class="font-semibold">
                                    Registration was unsuccessful.
                                </p>


                                <ul
                                    class="mt-2
                                           list-inside
                                           list-disc"
                                >

                                    @foreach (
                                        $errors->all()
                                        as $error
                                    )

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif



                        {{-- =========================================
                            REGISTRATION FORM
                        ========================================== --}}

                        <form
                            method="POST"
                            action="{{ route('register') }}"
                            class="mt-8 space-y-5"
                        >

                            @csrf



                            {{-- =====================================
                                NAME
                            ====================================== --}}

                            <div>

                                <label
                                    for="name"
                                    class="mb-2
                                           block
                                           text-sm
                                           font-semibold
                                           text-slate-700"
                                >
                                    Full name
                                </label>


                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Enter your full name"
                                    autocomplete="name"
                                    autofocus
                                    required
                                    class="block
                                           h-14
                                           w-full
                                           rounded-2xl
                                           border
                                           border-cyan-300
                                           bg-white/55
                                           px-5
                                           text-base
                                           text-slate-800
                                           shadow-sm
                                           outline-none
                                           placeholder:text-slate-400
                                           focus:border-cyan-500
                                           focus:ring-4
                                           focus:ring-cyan-200/60"
                                >


                                @error('name')

                                    <p
                                        class="mt-2
                                               text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            {{-- =====================================
                                EMAIL
                            ====================================== --}}

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
                                    placeholder="name@example.com"
                                    autocomplete="username"
                                    required
                                    class="block
                                           h-14
                                           w-full
                                           rounded-2xl
                                           border
                                           border-cyan-300
                                           bg-white/55
                                           px-5
                                           text-base
                                           text-slate-800
                                           shadow-sm
                                           outline-none
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


                                <p
                                    class="mt-2
                                           text-xs
                                           text-slate-500"
                                >
                                    A 6-digit verification code will
                                    be sent to this email address.
                                </p>

                            </div>



                            {{-- =====================================
                                PHONE
                            ====================================== --}}

                            <div>

                                <label
                                    for="phone"
                                    class="mb-2
                                           block
                                           text-sm
                                           font-semibold
                                           text-slate-700"
                                >
                                    Phone
                                    <span
                                        class="font-normal
                                               text-slate-400"
                                    >
                                        (optional)
                                    </span>
                                </label>


                                <input
                                    id="phone"
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="Enter phone number"
                                    autocomplete="tel"
                                    class="block
                                           h-14
                                           w-full
                                           rounded-2xl
                                           border
                                           border-cyan-300
                                           bg-white/55
                                           px-5
                                           text-base
                                           text-slate-800
                                           shadow-sm
                                           outline-none
                                           placeholder:text-slate-400
                                           focus:border-cyan-500
                                           focus:ring-4
                                           focus:ring-cyan-200/60"
                                >


                                @error('phone')

                                    <p
                                        class="mt-2
                                               text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            {{-- =====================================
                                PASSWORD
                            ====================================== --}}

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
                                        placeholder="Create a password"
                                        autocomplete="new-password"
                                        required
                                        class="block
                                               h-14
                                               w-full
                                               rounded-2xl
                                               border
                                               border-cyan-300
                                               bg-white/55
                                               px-5
                                               pr-14
                                               text-base
                                               text-slate-800
                                               shadow-sm
                                               outline-none
                                               placeholder:text-slate-400
                                               focus:border-cyan-500
                                               focus:ring-4
                                               focus:ring-cyan-200/60"
                                    >


                                    <button
                                        type="button"
                                        class="password-toggle
                                               absolute
                                               right-4
                                               top-1/2
                                               -translate-y-1/2
                                               text-slate-500
                                               hover:text-cyan-700"
                                        data-target="password"
                                        aria-label="Show password"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.036 12.322a1.012
                                                   1.012 0 010-.639C3.423
                                                   7.51 7.36 4.5 12
                                                   4.5c4.638 0 8.573
                                                   3.007 9.963 7.178.07.207.07
                                                   .431 0 .639C20.577 16.49
                                                   16.64 19.5 12 19.5c-4.638
                                                   0-8.573-3.007-9.963-7.178z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0
                                                   3 3 0 016 0z"
                                            />
                                        </svg>

                                    </button>

                                </div>


                                <p
                                    class="mt-2
                                           text-xs
                                           text-slate-500"
                                >
                                    Use at least eight characters.
                                </p>


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



                            {{-- =====================================
                                CONFIRM PASSWORD
                            ====================================== --}}

                            <div>

                                <label
                                    for="password_confirmation"
                                    class="mb-2
                                           block
                                           text-sm
                                           font-semibold
                                           text-slate-700"
                                >
                                    Confirm password
                                </label>


                                <div class="relative">

                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        placeholder="Enter the password again"
                                        autocomplete="new-password"
                                        required
                                        class="block
                                               h-14
                                               w-full
                                               rounded-2xl
                                               border
                                               border-cyan-300
                                               bg-white/55
                                               px-5
                                               pr-14
                                               text-base
                                               text-slate-800
                                               shadow-sm
                                               outline-none
                                               placeholder:text-slate-400
                                               focus:border-cyan-500
                                               focus:ring-4
                                               focus:ring-cyan-200/60"
                                    >


                                    <button
                                        type="button"
                                        class="password-toggle
                                               absolute
                                               right-4
                                               top-1/2
                                               -translate-y-1/2
                                               text-slate-500
                                               hover:text-cyan-700"
                                        data-target="password_confirmation"
                                        aria-label="Show password confirmation"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.036 12.322a1.012
                                                   1.012 0 010-.639C3.423
                                                   7.51 7.36 4.5 12
                                                   4.5c4.638 0 8.573
                                                   3.007 9.963 7.178.07.207.07
                                                   .431 0 .639C20.577 16.49
                                                   16.64 19.5 12 19.5c-4.638
                                                   0-8.573-3.007-9.963-7.178z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0
                                                   3 3 0 016 0z"
                                            />
                                        </svg>

                                    </button>

                                </div>


                                @error('password_confirmation')

                                    <p
                                        class="mt-2
                                               text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            {{-- =====================================
                                VERIFICATION INFORMATION
                            ====================================== --}}

                            <div
                                class="rounded-2xl
                                       border
                                       border-cyan-200
                                       bg-cyan-50/70
                                       px-4 py-4"
                            >

                                <div
                                    class="flex
                                           items-start
                                           gap-3"
                                >

                                    <div
                                        class="flex
                                               h-9 w-9
                                               shrink-0
                                               items-center
                                               justify-center
                                               rounded-xl
                                               bg-white
                                               text-cyan-600"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M21.75 6.75v10.5a2.25
                                                   2.25 0 0 1-2.25
                                                   2.25h-15a2.25 2.25
                                                   0 0 1-2.25-2.25V6.75"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m3 6 9 6 9-6"
                                            />
                                        </svg>

                                    </div>


                                    <div>

                                        <p
                                            class="text-sm
                                                   font-semibold
                                                   text-cyan-900"
                                        >
                                            Email verification required
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-xs
                                                   leading-relaxed
                                                   text-cyan-700"
                                        >
                                            After continuing, we will send
                                            a 6-digit OTP to your email.
                                            Your account will only be created
                                            after the OTP is successfully verified.
                                        </p>

                                    </div>

                                </div>

                            </div>



                            {{-- =====================================
                                SUBMIT
                            ====================================== --}}

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
                                Continue to Verification
                            </button>

                        </form>



                        {{-- =========================================
                            LOGIN
                        ========================================== --}}

                        <div
                            class="mt-7
                                   text-center
                                   text-sm
                                   text-slate-600"
                        >
                            Already have an account?

                            <a
                                href="{{ route('login') }}"
                                class="font-bold
                                       text-cyan-700
                                       hover:text-cyan-900"
                            >
                                Sign in
                            </a>

                        </div>



                        {{-- =========================================
                            MOBILE INFORMATION
                        ========================================== --}}

                        <div
                            class="mt-7
                                   rounded-2xl
                                   border
                                   border-white/70
                                   bg-white/35
                                   p-4
                                   lg:hidden"
                        >

                            <p
                                class="text-xs
                                       font-bold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Kumon North Hobart
                            </p>


                            <p
                                class="mt-2
                                       text-sm
                                       text-slate-600"
                            >
                                Manage students, guardians,
                                classes and capacity.
                            </p>

                        </div>

                    </div>

                </section>

            </div>

        </main>

    </div>



    {{-- =========================================================
        PASSWORD TOGGLE
    ========================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const toggleButtons =
                    document.querySelectorAll(
                        '.password-toggle'
                    );


                toggleButtons.forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                const targetId =
                                    button.dataset.target;


                                const input =
                                    document.getElementById(
                                        targetId
                                    );


                                if (!input) {

                                    return;
                                }


                                if (
                                    input.type
                                    ===
                                    'password'
                                ) {

                                    input.type =
                                        'text';


                                    button.setAttribute(
                                        'aria-label',
                                        'Hide password'
                                    );

                                } else {

                                    input.type =
                                        'password';


                                    button.setAttribute(
                                        'aria-label',
                                        'Show password'
                                    );
                                }
                            }
                        );
                    }
                );
            }
        );

    </script>

</body>

</html>
