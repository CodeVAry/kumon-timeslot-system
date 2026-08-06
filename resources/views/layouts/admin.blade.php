<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kumon Timeslot System')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>
@stack('scripts')
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen">

        {{-- Sidebar --}}
        @include('panel.sidebar')

        {{-- Main content area --}}
        <div class="min-h-screen ml-64">

            {{-- Header --}}
            @include('panel.header')

            <main class="p-6">

                {{-- Breadcrumb --}}
                @include('panel.breadcrumb')

                {{-- Success message --}}
                @if (session('success'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-100 px-4 py-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Error message --}}
                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-100 px-4 py-3 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Page content --}}
                @yield('content')

            </main>
        </div>

    </div>

</body>
</html>
