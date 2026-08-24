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
        class="relative min-h-screen overflow-hidden
               bg-slate-900"
    >

        {{-- Background image --}}
        <div
            class="absolute inset-0 bg-cover bg-center
                   bg-no-repeat"
            style="
                background-image:
                    url('{{ asset('images/login.jpeg') }}');
            "
        ></div>

        {{-- Dark overlay for readable text --}}
        <div
            class="absolute inset-0
                   bg-gradient-to-r
                   from-slate-950/60
                   via-slate-900/20
                   to-slate-900/10"
        ></div>

        {{-- Light soft overlay --}}
        <div
            class="absolute inset-0 bg-white/5"
        ></div>

        <main
            class="relative z-10 flex min-h-screen
                   items-center px-5 py-10
                   sm:px-8 lg:px-14 xl:px-20"
        >

            <div
                class="mx-auto grid w-full max-w-[1650px]
                       items-center gap-12
                       lg:grid-cols-[1.05fr_0.95fr]
                       xl:gap-20"
            >

                {{-- Left information section --}}
                <section
                    class="hidden max-w-2xl
                           lg:block"
                >

                    <p
                        class="text-sm font-semibold
                               uppercase tracking-[0.15em]
                               text-cyan-200"
                    >
                        Kumon North Hobart
                    </p>

                    <h1
                        class="mt-8 max-w-2xl
                               text-5xl font-bold
                               leading-[1.15]
                               text-white
                               xl:text-6xl"
                    >
                        Smarter scheduling for every learning class.
                    </h1>

                    <p
                        class="mt-10 max-w-xl
                               text-xl leading-relaxed
                               text-white/90
                               xl:text-2xl"
                    >
                        Review capacity, classes and wishlists
                        from one clear dashboard.
                    </p>

                </section>

                {{-- Login card --}}
                <section
                    class="mx-auto w-full max-w-[620px]"
                >

                    <div
                        class="rounded-[34px]
                               border border-white/60
                               bg-white/75
                               p-7 shadow-2xl
                               shadow-slate-900/25
                               backdrop-blur-2xl
                               sm:p-10
                               xl:p-14"
                    >

                        {{-- Change account type --}}
                        <a
                            href="{{ url('/') }}"
                            class="inline-flex min-h-11
                                   items-center gap-2
                                   rounded-full
                                   border border-cyan-400
                                   bg-cyan-50/80
                                   px-6 text-sm
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

                            Change account type
                        </a>

                        {{-- Login type --}}
                        <div class="mt-4">
                            <span
                                class="inline-flex rounded-full
                                       bg-cyan-100/80
                                       px-5 py-2
                                       text-xs font-bold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Admin Login
                            </span>
                        </div>

                        {{-- Heading --}}
                        <div class="mt-8">

                            <h2
                                class="text-4xl font-bold
                                       tracking-tight
                                       text-slate-800
                                       sm:text-5xl"
                            >
                                Admin sign in
                            </h2>

                            <p
                                class="mt-3 text-base
                                       leading-relaxed
                                       text-slate-600
                                       sm:text-lg"
                            >
                                Manage students, classes, capacity
                                and centre operations.
                            </p>

                        </div>

                        {{-- Session message --}}
                        @if (session('status'))

                            <div
                                class="mt-6 rounded-xl
                                       border border-green-200
                                       bg-green-50
                                       px-4 py-3
                                       text-sm text-green-700"
                            >
                                {{ session('status') }}
                            </div>

                        @endif

                        {{-- General errors --}}
                        @if ($errors->any())

                            <div
                                class="mt-6 rounded-xl
                                       border border-red-200
                                       bg-red-50
                                       px-4 py-3
                                       text-sm text-red-700"
                            >
                                <p class="font-semibold">
                                    Sign in was unsuccessful.
                                </p>

                                <ul
                                    class="mt-1 list-inside
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

                        <form
                            method="POST"
                            action="{{ route('login') }}"
                            class="mt-10 space-y-6"
                        >
                            @csrf

                            {{-- Email --}}
                            <div>

                                <label
                                    for="email"
                                    class="mb-2 block
                                           text-sm font-semibold
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
                                    class="block h-16 w-full
                                           rounded-2xl
                                           border border-cyan-300
                                           bg-white/55
                                           px-5 text-base
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
                                        class="mt-2 text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            {{-- Password --}}
                            <div>

                                <label
                                    for="password"
                                    class="mb-2 block
                                           text-sm font-semibold
                                           text-slate-700"
                                >
                                    Password
                                </label>

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                    class="block h-16 w-full
                                           rounded-2xl
                                           border border-cyan-300
                                           bg-white/55
                                           px-5 text-base
                                           text-slate-800
                                           shadow-sm
                                           outline-none
                                           placeholder:text-slate-400
                                           focus:border-cyan-500
                                           focus:ring-4
                                           focus:ring-cyan-200/60"
                                >

                                @error('password')

                                    <p
                                        class="mt-2 text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            {{-- Remember and forgot password --}}
                            <div
                                class="flex flex-wrap
                                       items-center
                                       justify-between gap-3"
                            >

                                <label
                                    for="remember_me"
                                    class="inline-flex
                                           cursor-pointer
                                           items-center gap-2"
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

                                @if (
                                    Route::has(
                                        'password.request'
                                    )
                                )

                                    <a
                                        href="{{ route(
                                            'password.request'
                                        ) }}"
                                        class="text-sm
                                               font-semibold
                                               text-cyan-700
                                               hover:text-cyan-900"
                                    >
                                        Forgot password?
                                    </a>

                                @endif

                            </div>

                            {{-- Sign in button --}}
                            <button
                                type="submit"
                                class="flex h-16 w-full
                                       items-center
                                       justify-center
                                       rounded-2xl
                                       bg-gradient-to-r
                                       from-cyan-500
                                       to-sky-500
                                       px-6
                                       text-base font-bold
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
                        <div
                            class="mt-7 text-center
                                   text-sm text-slate-600"
                        >
                            Don't have an account?

                            <a
                                href="{{ route('register') }}"
                                class="font-bold text-cyan-700
                                       hover:text-cyan-900"
                            >
                                Sign up
                            </a>
                        </div>

                        {{-- Mobile system information --}}
                        <div
                            class="mt-8 rounded-2xl
                                   border border-white/70
                                   bg-white/35
                                   p-4 lg:hidden"
                        >

                            <p
                                class="text-xs font-bold
                                       uppercase
                                       tracking-wide
                                       text-cyan-800"
                            >
                                Kumon North Hobart
                            </p>

                            <p
                                class="mt-2 text-sm
                                       text-slate-600"
                            >
                                Manage students, classes,
                                capacity and wishlists.
                            </p>

                        </div>

                    </div>

                </section>

            </div>

        </main>

    </div>

</body>

</html>
