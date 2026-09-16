@extends('layouts.admin')

@section('title', 'Import Students')

@section('page-title', 'Import Students')

@section('content')

<div class="space-y-6">


    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div
        class="flex
               flex-col
               gap-4
               lg:flex-row
               lg:items-center
               lg:justify-between"
    >

        <div>

            <h1
                class="text-3xl
                       font-bold
                       text-slate-900"
            >
                Import Students
            </h1>


            <p
                class="mt-2
                       text-sm
                       text-slate-500"
            >
                Upload the Student Profile Report first,
                then upload the Centre Timeslots workbook.
            </p>

        </div>


        <a
            href="{{ route('admin.students.index') }}"
            class="inline-flex
                   h-11
                   items-center
                   justify-center
                   rounded-xl
                   border
                   border-cyan-300
                   bg-white
                   px-5
                   text-sm
                   font-semibold
                   text-cyan-700
                   transition
                   hover:bg-cyan-50"
        >
            ← Back to Students
        </a>

    </div>



    {{-- =========================================================
        SUCCESS
    ========================================================== --}}

    @if (session('success'))

        <div
            class="rounded-xl
                   border
                   border-green-200
                   bg-green-50
                   px-5
                   py-4
                   text-sm
                   font-semibold
                   text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif



    {{-- =========================================================
        ERROR
    ========================================================== --}}

    @if (session('error'))

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5
                   py-4
                   text-sm
                   font-semibold
                   text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif



    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if ($errors->any())

        <div
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5
                   py-4"
        >

            <p
                class="text-sm
                       font-semibold
                       text-red-700"
            >
                Please check the following:
            </p>


            <ul
                class="mt-2
                       list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600"
            >

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- =========================================================
        UPLOAD CARDS
    ========================================================== --}}

    <div
        class="grid
               gap-6
               xl:grid-cols-2"
    >


        {{-- =====================================================
            STEP 1 - STUDENT PROFILE
        ====================================================== --}}

        <div
            class="rounded-2xl
                   border
                   border-cyan-300
                   bg-white
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       items-start
                       justify-between
                       gap-4"
            >

                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-cyan-600"
                    >
                        Step 1
                    </p>


                    <h2
                        class="mt-2
                               text-xl
                               font-bold
                               text-slate-900"
                    >
                        Student Details
                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               text-slate-500"
                    >
                        Student Profile Report.xlsx
                    </p>

                </div>


                @if ($profileUploaded)

                    <span
                        class="inline-flex
                               rounded-full
                               bg-green-100
                               px-3
                               py-1
                               text-xs
                               font-semibold
                               text-green-700"
                    >
                        Uploaded
                    </span>

                @else

                    <span
                        class="inline-flex
                               rounded-full
                               bg-slate-100
                               px-3
                               py-1
                               text-xs
                               font-semibold
                               text-slate-500"
                    >
                        Required
                    </span>

                @endif

            </div>



            {{-- Current Uploaded File --}}

            @if ($profileUploaded && $profileName)

                <div
                    class="mt-5
                           rounded-xl
                           bg-green-50
                           px-4
                           py-3
                           text-sm
                           font-medium
                           text-green-700"
                >
                    {{ $profileName }}
                </div>

            @endif



            {{-- Upload Form --}}

            <form
                method="POST"
                action="{{ route('admin.student-import.profile.upload') }}"
                enctype="multipart/form-data"
                class="mt-5
                       space-y-4"
            >

                @csrf


                <div>

                    <input
                        type="file"
                        name="profile_file"
                        accept=".xlsx,.xls,.csv"
                        required
                        class="block
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-3
                               py-3
                               text-sm
                               text-slate-600
                               file:mr-4
                               file:rounded-lg
                               file:border-0
                               file:bg-slate-100
                               file:px-4
                               file:py-2
                               file:text-sm
                               file:font-semibold
                               file:text-slate-700
                               hover:file:bg-slate-200"
                    >

                </div>


                <button
                    type="submit"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           bg-cyan-600
                           px-5
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:bg-cyan-700"
                >

                    @if ($profileUploaded)

                        Replace File

                    @else

                        Upload Student Profile

                    @endif

                </button>

            </form>

        </div>



        {{-- =====================================================
            STEP 2 - TIMESLOT
        ====================================================== --}}

        <div
            class="rounded-2xl
                   border
                   border-violet-300
                   bg-white
                   p-6
                   shadow-sm"
        >

            <div
                class="flex
                       items-start
                       justify-between
                       gap-4"
            >

                <div>

                    <p
                        class="text-xs
                               font-bold
                               uppercase
                               tracking-wide
                               text-violet-600"
                    >
                        Step 2
                    </p>


                    <h2
                        class="mt-2
                               text-xl
                               font-bold
                               text-slate-900"
                    >
                        Timeslot Allocation
                    </h2>


                    <p
                        class="mt-2
                               text-sm
                               text-slate-500"
                    >
                        Centre Timeslots.xlsx
                    </p>

                </div>


                @if ($timeslotUploaded)

                    <span
                        class="inline-flex
                               rounded-full
                               bg-green-100
                               px-3
                               py-1
                               text-xs
                               font-semibold
                               text-green-700"
                    >
                        Uploaded
                    </span>

                @else

                    <span
                        class="inline-flex
                               rounded-full
                               bg-slate-100
                               px-3
                               py-1
                               text-xs
                               font-semibold
                               text-slate-500"
                    >
                        Required
                    </span>

                @endif

            </div>



            {{-- Current Uploaded File --}}

            @if ($timeslotUploaded && $timeslotName)

                <div
                    class="mt-5
                           rounded-xl
                           bg-green-50
                           px-4
                           py-3
                           text-sm
                           font-medium
                           text-green-700"
                >
                    {{ $timeslotName }}
                </div>

            @endif



            {{-- Upload Form --}}

            <form
                method="POST"
                action="{{ route('admin.student-import.timeslot.upload') }}"
                enctype="multipart/form-data"
                class="mt-5
                       space-y-4"
            >

                @csrf


                <div>

                    <input
                        type="file"
                        name="timeslot_file"
                        accept=".xlsx,.xls,.csv"
                        required
                        class="block
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-3
                               py-3
                               text-sm
                               text-slate-600
                               file:mr-4
                               file:rounded-lg
                               file:border-0
                               file:bg-slate-100
                               file:px-4
                               file:py-2
                               file:text-sm
                               file:font-semibold
                               file:text-slate-700
                               hover:file:bg-slate-200"
                    >

                </div>


                <button
                    type="submit"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           bg-violet-600
                           px-5
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:bg-violet-700"
                >

                    @if ($timeslotUploaded)

                        Replace File

                    @else

                        Upload Timeslots

                    @endif

                </button>

            </form>

        </div>

    </div>



    {{-- =========================================================
        PREVIEW SECTION
    ========================================================== --}}

    <div
        class="rounded-2xl
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm"
    >

        <h2
            class="text-xl
                   font-bold
                   text-slate-900"
        >
            Preview Before Import
        </h2>


        <p
            class="mt-2
                   text-sm
                   text-slate-500"
        >
            Nothing is saved until you review the preview
            and click Confirm Import.
        </p>



        {{-- File Status --}}

        <div
            class="mt-5
                   grid
                   gap-3
                   md:grid-cols-2"
        >

            <div
                class="rounded-xl
                       border
                       px-4
                       py-3
                       {{
                           $profileUploaded
                               ? 'border-green-200 bg-green-50'
                               : 'border-slate-200 bg-slate-50'
                       }}"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Student Profile
                </p>


                <p
                    class="mt-1
                           text-sm
                           font-semibold
                           {{
                               $profileUploaded
                                   ? 'text-green-700'
                                   : 'text-slate-500'
                           }}"
                >

                    @if ($profileUploaded)

                        ✓ Ready

                    @else

                        Not uploaded

                    @endif

                </p>

            </div>


            <div
                class="rounded-xl
                       border
                       px-4
                       py-3
                       {{
                           $timeslotUploaded
                               ? 'border-green-200 bg-green-50'
                               : 'border-slate-200 bg-slate-50'
                       }}"
            >

                <p
                    class="text-xs
                           font-semibold
                           uppercase
                           text-slate-400"
                >
                    Timeslot Workbook
                </p>


                <p
                    class="mt-1
                           text-sm
                           font-semibold
                           {{
                               $timeslotUploaded
                                   ? 'text-green-700'
                                   : 'text-slate-500'
                           }}"
                >

                    @if ($timeslotUploaded)

                        ✓ Ready

                    @else

                        Not uploaded

                    @endif

                </p>

            </div>

        </div>



        {{-- Actions --}}

        <div
            class="mt-6
                   flex
                   flex-wrap
                   gap-3"
        >


            @if ($profileUploaded && $timeslotUploaded)

                <a
                    href="{{ route('admin.student-import.preview') }}"
                    class="inline-flex
                           h-11
                           items-center
                           justify-center
                           rounded-xl
                           bg-green-600
                           px-6
                           text-sm
                           font-bold
                           text-white
                           transition
                           hover:bg-green-700"
                >
                    Preview Import
                </a>

            @else

                <button
                    type="button"
                    disabled
                    class="inline-flex
                           h-11
                           cursor-not-allowed
                           items-center
                           justify-center
                           rounded-xl
                           bg-slate-300
                           px-6
                           text-sm
                           font-bold
                           text-white"
                >
                    Preview Import
                </button>

            @endif



            @if ($profileUploaded || $timeslotUploaded)

                <form
                    method="POST"
                    action="{{ route('admin.student-import.reset') }}"
                >

                    @csrf


                    <button
                        type="submit"
                        onclick="
                            return confirm(
                                'Clear the uploaded import files?'
                            );
                        "
                        class="inline-flex
                               h-11
                               items-center
                               justify-center
                               rounded-xl
                               border
                               border-red-300
                               bg-white
                               px-5
                               text-sm
                               font-semibold
                               text-red-600
                               transition
                               hover:bg-red-50"
                    >
                        Clear Files
                    </button>

                </form>

            @endif

        </div>

    </div>

</div>

@endsection
