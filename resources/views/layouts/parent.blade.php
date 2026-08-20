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
        @yield('title', 'Parent Portal')
        | Kumon North Hobart
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body
    class="bg-slate-100
           font-sans
           antialiased"
>

    {{-- Sidebar --}}
    @include('parent.partials.sidebar')


    {{-- Main Area --}}
    <div class="min-h-screen pl-64">

        {{-- Header --}}
        <header
            class="sticky
                   top-0
                   z-30
                   flex
                   h-16
                   items-center
                   justify-between
                   border-b
                   border-slate-200
                   bg-white
                   px-6"
        >

            <div>

                <h1
                    class="text-lg
                           font-bold
                           text-slate-800"
                >
                    @yield('page-title', 'Parent Portal')
                </h1>

            </div>


            <div
                class="flex
                       items-center
                       gap-3"
            >

                @if (session('parent_student_id'))

                    <a
                        href="{{ route('parent.welcome') }}"
                        class="inline-flex
                               items-center
                               gap-2
                               rounded-xl
                               border
                               border-violet-200
                               bg-violet-50
                               px-4 py-2
                               text-sm
                               font-semibold
                               text-violet-700
                               transition
                               hover:bg-violet-100"
                    >
                        Switch Student
                    </a>

                @endif

            </div>

        </header>


        {{-- Page Content --}}
        <main class="p-6">

            @if (session('success'))

                <div
                    class="mb-5
                           rounded-xl
                           border
                           border-green-200
                           bg-green-50
                           px-4 py-3
                           text-sm
                           text-green-700"
                >
                    {{ session('success') }}
                </div>

            @endif


            @if ($errors->any())

                <div
                    class="mb-5
                           rounded-xl
                           border
                           border-red-200
                           bg-red-50
                           px-4 py-3
                           text-sm
                           text-red-700"
                >

                    @foreach ($errors->all() as $error)

                        <p>
                            {{ $error }}
                        </p>

                    @endforeach

                </div>

            @endif


            @yield('content')

        </main>

    </div>


    @stack('scripts')

</body>

</html>