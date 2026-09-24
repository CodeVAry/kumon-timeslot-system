<!DOCTYPE html>

<html
    lang="{{ str_replace(
        '_',
        '-',
        app()->getLocale()
    ) }}"
>

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
        Verify Account | Kumon Time Scheduling System
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

    {{-- =====================================================
        BACKGROUND
    ====================================================== --}}

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


    {{-- =====================================================
        OVERLAY
    ====================================================== --}}

    <div
        class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/65
               via-slate-900/20
               to-slate-900/10"
    ></div>


    <div
        class="absolute
               inset-0
               bg-white/5"
    ></div>



    {{-- =====================================================
        PAGE
    ====================================================== --}}

    <main
        class="relative
               z-10
               flex
               min-h-screen
               items-center
               px-5
               py-8
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


                    {{-- EXACT LOGO STYLE --}}
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



                    {{-- Centre Name --}}
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
                    Verify your email
                    <br>
                    to finish creating
                    <br>
                    your account.
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
                    We sent a secure 6-digit verification
                    code to your email address.
                </p>

            </section>



            {{-- =================================================
                OTP CARD
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
                           bg-white/80
                           p-7
                           shadow-2xl
                           shadow-slate-900/25
                           backdrop-blur-2xl
                           sm:p-10
                           xl:p-12"
                >


                    {{-- =========================================
                        MOBILE BRAND
                    ========================================== --}}

                    <div
                        class="mb-7
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



                    {{-- =========================================
                        BADGE
                    ========================================== --}}

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
                        Email Verification
                    </span>



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
                            Enter verification code
                        </h2>


                        <p
                            class="mt-4
                                   text-base
                                   leading-relaxed
                                   text-slate-600"
                        >
                            Enter the 6-digit code sent to
                        </p>


                        <p
                            class="mt-1
                                   break-all
                                   font-bold
                                   text-cyan-700"
                        >
                            {{ $email }}
                        </p>


                        <p
                            class="mt-3
                                   text-sm
                                   text-slate-500"
                        >
                            The verification code expires
                            after 10 minutes.
                        </p>

                    </div>



                    {{-- =========================================
                        SUCCESS
                    ========================================== --}}

                    @if (session('success'))

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
                            {{ session('success') }}
                        </div>

                    @endif



                    {{-- =========================================
                        ERROR
                    ========================================== --}}

                    @if (session('error'))

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
                            {{ session('error') }}
                        </div>

                    @endif



                    {{-- =========================================
                        VERIFY FORM
                    ========================================== --}}

                    <form
                        method="POST"
                        action="{{ route('register.otp.verify') }}"
                        class="mt-8"
                    >

                        @csrf


                        <label
                            for="otp"
                            class="mb-2
                                   block
                                   text-sm
                                   font-semibold
                                   text-slate-700"
                        >
                            Verification code
                        </label>


                        <input
                            id="otp"
                            type="text"
                            name="otp"
                            value="{{ old('otp') }}"
                            inputmode="numeric"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            autofocus
                            required
                            class="block
                                   h-16
                                   w-full
                                   rounded-2xl
                                   border
                                   border-cyan-300
                                   bg-white/70
                                   px-5
                                   text-center
                                   text-3xl
                                   font-bold
                                   tracking-[0.35em]
                                   text-slate-800
                                   shadow-sm
                                   outline-none
                                   transition
                                   placeholder:text-slate-300
                                   focus:border-cyan-500
                                   focus:ring-4
                                   focus:ring-cyan-200/60"
                        >


                        @error('otp')

                            <p
                                class="mt-2
                                       text-sm
                                       text-red-600"
                            >
                                {{ $message }}
                            </p>

                        @enderror



                        {{-- =====================================
                            VERIFY BUTTON
                        ====================================== --}}

                        <button
                            type="submit"
                            class="mt-6
                                   flex
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
                            Verify and Create Account
                        </button>

                    </form>



                    {{-- =========================================
                        RESEND
                    ========================================== --}}

                    <form
                        method="POST"
                        action="{{ route('register.otp.resend') }}"
                        class="mt-4"
                    >

                        @csrf


                        <button
                            type="submit"
                            class="flex
                                   h-12
                                   w-full
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-cyan-300
                                   bg-cyan-50
                                   text-sm
                                   font-semibold
                                   text-cyan-800
                                   transition
                                   hover:bg-cyan-100"
                        >
                            Resend verification code
                        </button>

                    </form>



                    {{-- =========================================
                        CANCEL
                    ========================================== --}}

                    <form
                        method="POST"
                        action="{{ route('register.otp.cancel') }}"
                        class="mt-3"
                    >

                        @csrf


                        <button
                            type="submit"
                            class="flex
                                   h-11
                                   w-full
                                   items-center
                                   justify-center
                                   text-sm
                                   font-semibold
                                   text-slate-500
                                   transition
                                   hover:text-slate-800"
                        >
                            ← Change registration details
                        </button>

                    </form>

                </div>

            </section>

        </div>

    </main>

</div>



{{-- =========================================================
    OTP DIGITS ONLY
========================================================== --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const otpInput =
            document.getElementById(
                'otp'
            );


        if (!otpInput) {
            return;
        }


        otpInput.addEventListener(
            'input',
            function () {

                otpInput.value =
                    otpInput.value
                        .replace(
                            /\D/g,
                            ''
                        )
                        .slice(
                            0,
                            6
                        );

            }
        );

    }
);

</script>

</body>

</html>
