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
        Verify Code | Kumon Parent Portal
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
        BACKGROUND
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


    {{-- Dark overlay --}}
    <div
        class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/65
               via-slate-900/20
               to-slate-900/10"
    ></div>


    {{-- Soft overlay --}}
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



                    {{-- Centre Information --}}
                    <div>

                        <p
                            class="text-xs
                                   font-bold
                                   uppercase
                                   tracking-[0.20em]
                                   text-violet-200"
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
                    One more step to access
                    <br>
                    your Parent Portal.
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
                    Enter the verification code sent to
                    your registered email address.
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
                                class="rounded-lg
                                       bg-white
                                       px-3
                                       py-2
                                       shadow-sm"
                            >

                                <img
                                    src="{{ asset('images/kumon-logo.png') }}"
                                    alt="Kumon Logo"
                                    class="h-8
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
                                           text-violet-700"
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
                        BACK
                    ============================================== --}}

                    <a
                        href="{{ route('parent.login') }}"
                        class="inline-flex
                               min-h-11
                               items-center
                               gap-2
                               rounded-full
                               border
                               border-violet-300
                               bg-white/70
                               px-5
                               text-sm
                               font-semibold
                               text-slate-700
                               transition
                               hover:border-violet-400
                               hover:bg-violet-50
                               hover:text-violet-800"
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

                        Back to Parent Login

                    </a>



                    {{-- =============================================
                        TYPE
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
                                   text-violet-800"
                        >
                            Parent Verification
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
                                   text-slate-900
                                   sm:text-5xl"
                        >
                            Enter verification code
                        </h1>


                        <p
                            class="mt-3
                                   text-base
                                   leading-relaxed
                                   text-slate-600"
                        >
                            We sent a 6-digit verification
                            code to your registered email.
                        </p>

                    </div>



                    {{-- =============================================
                        DESTINATION
                    ============================================== --}}

                    <div
                        class="mt-6
                               rounded-2xl
                               border
                               border-violet-100
                               bg-violet-50
                               px-5
                               py-4"
                    >

                        <p
                            class="text-xs
                                   font-semibold
                                   uppercase
                                   tracking-wide
                                   text-violet-500"
                        >
                            Code sent to
                        </p>


                        <p
                            class="mt-1
                                   break-all
                                   text-sm
                                   font-bold
                                   text-violet-800"
                        >
                            {{ $maskedDestination }}
                        </p>

                    </div>



                    {{-- =============================================
                        SUCCESS
                    ============================================== --}}

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
                                   px-4
                                   py-3
                                   text-sm
                                   text-red-700"
                        >

                            <p class="font-semibold">
                                Verification was unsuccessful.
                            </p>


                            @foreach ($errors->all() as $error)

                                <p class="mt-1">
                                    {{ $error }}
                                </p>

                            @endforeach

                        </div>

                    @endif



                    {{-- =============================================
                        OTP FORM
                    ============================================== --}}

                    <form
                        method="POST"
                        action="{{ route('parent.otp.verify') }}"
                        class="mt-8"
                    >

                        @csrf


                        <div>

                            <label
                                for="otp"
                                class="block
                                       text-sm
                                       font-semibold
                                       text-slate-700"
                            >
                                Verification Code
                            </label>


                            <input
                                id="otp"
                                type="text"
                                name="otp"
                                value="{{ old('otp') }}"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                autocomplete="one-time-code"
                                autofocus
                                required
                                placeholder="000000"
                                class="mt-2
                                       block
                                       h-16
                                       w-full
                                       rounded-2xl
                                       border
                                       border-slate-300
                                       bg-white
                                       px-5
                                       text-center
                                       text-2xl
                                       font-bold
                                       tracking-[0.45em]
                                       text-slate-900
                                       shadow-sm
                                       outline-none
                                       transition
                                       placeholder:text-slate-300
                                       focus:border-violet-500
                                       focus:ring-4
                                       focus:ring-violet-200/60"
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

                        </div>



                        {{-- =========================================
                            EXPIRY
                        ========================================== --}}

                        <div
                            class="mt-5
                                   rounded-2xl
                                   border
                                   border-slate-200
                                   bg-white/50
                                   px-4
                                   py-3"
                        >

                            <p
                                class="text-xs
                                       leading-relaxed
                                       text-slate-600"
                            >
                                This verification code is valid
                                for 5 minutes and can only be used
                                for this login attempt.
                            </p>

                        </div>



                        {{-- =========================================
                            RESEND
                        ========================================== --}}

                        <div
                            class="mt-5
                                   flex
                                   flex-col
                                   items-center
                                   gap-2
                                   text-center"
                        >

                            <p
                                class="text-sm
                                       text-slate-600"
                            >
                                Didn't receive the code?
                            </p>


                            <button
                                type="submit"
                                id="resendOtpButton"
                                formaction="{{ route('parent.otp.resend') }}"
                                formmethod="POST"
                                formnovalidate
                                data-available-at="{{ $resendAvailableAt }}"
                                class="text-sm
                                       font-bold
                                       text-violet-700
                                       transition
                                       hover:text-violet-900
                                       disabled:cursor-not-allowed
                                       disabled:text-slate-400"
                            >

                                <span id="resendOtpText">
                                    Resend code
                                </span>

                            </button>


                            @error('resend')

                                <p
                                    class="text-xs
                                           text-red-600"
                                >
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>



                        {{-- =========================================
                            VERIFY
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
                                   focus:ring-violet-300"
                        >

                            Verify & Continue


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

                </div>

            </section>

        </div>

    </main>

</div>



{{-- =========================================================
    RESEND TIMER
========================================================== --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const button =
            document.getElementById(
                'resendOtpButton'
            );

        const text =
            document.getElementById(
                'resendOtpText'
            );


        if (!button || !text) {
            return;
        }


        const availableAt =
            Number(
                button.dataset.availableAt
            ) * 1000;


        function updateResendButton() {

            const remaining =
                Math.ceil(
                    (
                        availableAt
                        -
                        Date.now()
                    )
                    /
                    1000
                );


            if (remaining > 0) {

                button.disabled = true;

                text.textContent =
                    'Resend code in '
                    + remaining
                    + 's';

            } else {

                button.disabled = false;

                text.textContent =
                    'Resend code';

            }

        }


        updateResendButton();


        const timer =
            setInterval(
                function () {

                    updateResendButton();


                    if (
                        Date.now()
                        >=
                        availableAt
                    ) {

                        clearInterval(
                            timer
                        );

                    }

                },
                1000
            );

    }
);

</script>

</body>

</html>
