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


    <div
        class="absolute
               inset-0
               bg-gradient-to-r
               from-slate-950/55
               via-slate-900/15
               to-slate-900/10"
    ></div>


    <main
        class="relative
               z-10
               flex
               min-h-screen
               items-center
               justify-center
               px-5 py-10"
    >

        <section
            class="w-full
                   max-w-[620px]"
        >

            <div
                class="rounded-[34px]
                       border
                       border-white/60
                       bg-white/85
                       p-8
                       shadow-2xl
                       backdrop-blur-2xl
                       sm:p-12"
            >


                {{-- Back --}}
                <a
                    href="{{ route('parent.login') }}"
                    class="inline-flex
                           items-center
                           gap-2
                           rounded-full
                           border
                           border-slate-300
                           bg-white/70
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-slate-700
                           hover:bg-white"
                >
                    ← Back
                </a>



                <div class="mt-8">

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-violet-700"
                    >
                        Parent Verification
                    </p>


                    <h1
                        class="mt-4
                               text-4xl
                               font-bold
                               text-slate-900"
                    >
                        Enter verification code
                    </h1>


                    <p
                        class="mt-3
                               text-base
                               leading-relaxed
                               text-slate-600"
                    >
                        We sent a 6-digit code to your
                        registered email address.
                    </p>


                    <p
                        class="mt-2
                               font-semibold
                               text-slate-800"
                    >
                        {{ $destination }}
                    </p>

                </div>



                {{-- Errors --}}
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

                        @foreach (
                            $errors->all()
                            as $error
                        )

                            <p>
                                {{ $error }}
                            </p>

                        @endforeach

                    </div>

                @endif



                <form
                    method="POST"
                    action="{{ route(
                        'parent.otp.verify'
                    ) }}"
                    class="mt-8"
                >

                    @csrf


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
                        maxlength="6"
                        inputmode="numeric"
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
                               tracking-[0.5em]
                               text-slate-900
                               outline-none
                               focus:border-violet-500
                               focus:ring-4
                               focus:ring-violet-200"
                    >


                    <p
                        class="mt-3
                               text-xs
                               text-slate-500"
                    >
                        The code expires after 5 minutes.
                    </p>



                    <button
                        type="submit"
                        class="mt-7
                               flex
                               h-16
                               w-full
                               items-center
                               justify-center
                               rounded-2xl
                               bg-violet-600
                               px-6
                               text-base
                               font-bold
                               text-white
                               shadow-lg
                               transition
                               hover:bg-violet-700"
                    >
                        Verify & Continue
                    </button>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>
