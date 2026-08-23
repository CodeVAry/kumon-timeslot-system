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

    {{-- Background --}}
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


    {{-- Overlay --}}
    <div
        class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/60
               via-slate-900/20
               to-slate-900/10"
    ></div>


    <div
        class="absolute
               inset-0
               bg-white/5"
    ></div>


    <main
        class="relative
               z-10
               flex
               min-h-screen
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


            {{-- Left --}}
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
                    Verify your email to finish creating your account.
                </h1>


                <p
                    class="mt-10
                           max-w-xl
                           text-xl
                           leading-relaxed
                           text-white/90
                           xl:text-2xl"
                >
                    We sent a secure 6-digit verification
                    code to your email address.
                </p>

            </section>



            {{-- OTP Card --}}
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


                    {{-- Badge --}}
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
                        Email Verification
                    </span>



                    {{-- Heading --}}
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



                    {{-- Success --}}
                    @if (
                        session(
                            'success'
                        )
                    )

                        <div
                            class="mt-6
                                   rounded-xl
                                   border
                                   border-green-200
                                   bg-green-50
                                   px-4 py-3
                                   text-sm
                                   text-green-700"
                        >
                            {{
                                session(
                                    'success'
                                )
                            }}
                        </div>

                    @endif



                    {{-- Error --}}
                    @if (
                        session(
                            'error'
                        )
                    )

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
                            {{
                                session(
                                    'error'
                                )
                            }}
                        </div>

                    @endif



                    {{-- Verify --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'register.otp.verify'
                        ) }}"
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
                                   bg-white/55
                                   px-5
                                   text-center
                                   text-3xl
                                   font-bold
                                   tracking-[0.35em]
                                   text-slate-800
                                   shadow-sm
                                   outline-none
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



                    {{-- Resend --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'register.otp.resend'
                        ) }}"
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



                    {{-- Cancel --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'register.otp.cancel'
                        ) }}"
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


        /*
         * Allow digits only.
         */
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
