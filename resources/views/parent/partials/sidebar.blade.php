@php

    $parent = Auth::guard('parent')->user();

    $selectedStudent = null;

    if ($parent && session('parent_student_id')) {
        $selectedStudent = $parent->students()->where('students.id', session('parent_student_id'))->first();
    }

    $parentInitials = $parent
        ? strtoupper(mb_substr($parent->first_name, 0, 1) . mb_substr($parent->last_name, 0, 1))
        : 'P';

@endphp


<aside
    class="fixed
           left-0
           top-0
           z-40
           h-screen
           w-64
           bg-slate-900
           text-white">

    {{-- =====================================================
        LOGO
    ====================================================== --}}

    <div
        class="flex
               h-16
               items-center
               border-b
               border-slate-700
               px-5">

        <a href="{{ route('parent.dashboard') }}" class="flex
                   items-center
                   gap-3">

            <div
                class="flex
                       h-10 w-10
                       items-center
                       justify-center
                       rounded-lg
                       bg-violet-600
                       text-lg
                       font-bold">
                K
            </div>


            <div>

                <h1 class="text-sm
                           font-bold">
                    Kumon
                </h1>


                <p class="text-xs
                           text-slate-400">
                    Parent Portal
                </p>

            </div>

        </a>

    </div>



    {{-- =====================================================
        SELECTED STUDENT
    ====================================================== --}}

    @if ($selectedStudent)
        <div class="border-b
                   border-slate-700
                   px-4 py-4">

            <p
                class="px-2
                       text-[10px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-slate-500">
                Viewing Student
            </p>


            <div
                class="mt-2
                       flex
                       items-center
                       gap-3
                       rounded-xl
                       bg-slate-800
                       px-3 py-3">

                <div
                    class="flex
                           h-9 w-9
                           shrink-0
                           items-center
                           justify-center
                           rounded-full
                           bg-violet-600
                           text-xs
                           font-bold">

                    {{ strtoupper(mb_substr($selectedStudent->first_name, 0, 1) . mb_substr($selectedStudent->last_name, 0, 1)) }}

                </div>


                <div class="min-w-0">

                    <p
                        class="truncate
                               text-sm
                               font-semibold">
                        {{ $selectedStudent->first_name }}
                        {{ $selectedStudent->last_name }}
                    </p>


                    <p
                        class="truncate
                               text-xs
                               text-slate-400">
                        {{ $selectedStudent->external_id }}
                    </p>

                </div>

            </div>


            <a href="{{ route('parent.welcome') }}"
                class="mt-2
                       flex
                       w-full
                       items-center
                       justify-center
                       rounded-lg
                       px-3 py-2
                       text-xs
                       font-semibold
                       text-violet-300
                       transition
                       hover:bg-slate-800
                       hover:text-white">
                Switch Student
            </a>

        </div>
    @endif



    {{-- =====================================================
        NAVIGATION
    ====================================================== --}}

    <nav class="h-[calc(100vh-270px)]
               overflow-y-auto
               px-4 py-5">

        <p
            class="mb-2
                   px-3
                   text-xs
                   font-semibold
                   uppercase
                   tracking-wider
                   text-slate-500">
            Parent Menu
        </p>


        <div class="space-y-1">


            {{-- Dashboard --}}
            <a href="{{ route('parent.dashboard') }}"
                class="flex
                       items-center
                       gap-3
                       rounded-xl
                       px-4 py-3
                       text-sm
                       font-medium
                       transition
                    {{ request()->routeIs('parent.dashboard')
                        ? 'bg-violet-600 text-white'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9
                           M5 10v10h14V10
                           M9 20v-6h6v6" />
                </svg>

                <span>
                    Dashboard
                </span>

            </a>



            {{-- Wishlist --}}
            <a href="{{ route('parent.wishlist.index') }}"
                class="flex
           items-center
           gap-3
           rounded-xl
           px-4 py-3
           text-sm
           font-medium
           transition
           {{ request()->routeIs('parent.wishlist.*')
               ? 'bg-violet-600 text-white'
               : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.35-7-10
                           a4 4 0 0 1 7-2.65
                           A4 4 0 0 1 19 11
                           c0 5.65-7 10-7 10Z" />
                </svg>

                <span>
                    Wishlist
                </span>

            </a>



            {{-- Leave --}}
            <a href="{{ route('parent.leave.index') }}"
                class="flex
           items-center
           gap-3
           rounded-xl
           px-4 py-3
           text-sm
           font-medium
           transition
           {{ request()->routeIs('parent.leave.*')
               ? 'bg-violet-600 text-white'
               : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25
                           M17.25 3v2.25
                           M3.75 9.75h16.5
                           M5.25 5.25h13.5
                           A1.5 1.5 0 0 1
                           20.25 6.75v12
                           a1.5 1.5 0 0 1
                           -1.5 1.5H5.25
                           a1.5 1.5 0 0 1
                           -1.5-1.5v-12
                           a1.5 1.5 0 0 1
                           1.5-1.5Z" />
                </svg>

                <span>
                    Leave Management
                </span>

            </a>

        </div>

    </nav>



    {{-- =====================================================
        PARENT ACCOUNT
    ====================================================== --}}

    <div
        class="absolute
               bottom-0
               left-0
               w-full
               border-t
               border-slate-700
               bg-slate-900
               p-4">

        <div class="mb-3
                   flex
                   items-center
                   gap-3">

            <div
                class="flex
                       h-9 w-9
                       shrink-0
                       items-center
                       justify-center
                       rounded-full
                       bg-violet-600
                       text-sm
                       font-bold">
                {{ $parentInitials }}
            </div>


            <div class="min-w-0">

                <p class="truncate
                           text-sm
                           font-semibold">
                    {{ $parent?->first_name }}
                    {{ $parent?->last_name }}
                </p>


                <p class="truncate
                           text-xs
                           text-slate-400">
                    {{ $parent?->email ?? $parent?->phone }}
                </p>


                <p class="truncate
                           text-xs
                           text-violet-300">
                    Parent
                </p>

            </div>

        </div>


        {{-- Logout route will be added next --}}
        <form method="POST" action="{{ route('parent.logout') }}">

            @csrf


            <button type="submit"
                class="flex
                       w-full
                       items-center
                       gap-3
                       rounded-lg
                       px-3 py-2
                       text-sm
                       font-medium
                       text-slate-300
                       transition
                       hover:bg-red-600
                       hover:text-white">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0
                           -4-4m4 4H7
                           m6 4v1a3 3 0 0 1
                           -3 3H6a3 3 0 0 1
                           -3-3V7a3 3 0 0 1
                           3-3h4a3 3 0 0 1
                           3 3v1" />

                </svg>

                Logout

            </button>

        </form>

    </div>

</aside>
