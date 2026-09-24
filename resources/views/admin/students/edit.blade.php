@extends('layouts.admin')

@section('title', 'Edit Student')

@section('page-title', 'Edit Student')

@section('content')

<div class="mx-auto max-w-5xl">


    {{-- =========================================================
        BACK
    ========================================================== --}}

    <a
        href="{{ route(
            'admin.students.show',
            $student
        ) }}"
        class="mb-5 inline-flex
               text-sm font-semibold
               text-blue-600
               hover:text-blue-700"
    >
        ← Back to Student Profile
    </a>


    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if ($errors->any())

        <div
            class="mb-5 rounded-xl
                   border border-red-200
                   bg-red-50
                   px-5 py-4"
        >

            <p class="font-semibold text-red-700">
                Please correct the following:
            </p>

            <ul
                class="mt-2 list-disc
                       space-y-1 pl-5
                       text-sm text-red-600"
            >

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div
        class="rounded-2xl
               border border-slate-200
               bg-white
               shadow-sm"
    >


        {{-- =================================================
            STUDENT INFORMATION
        ================================================== --}}

        @if ($section === 'information')

            <div
                class="border-b
                       border-slate-200
                       px-6 py-5"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Edit Student Information
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Update the same student details collected during registration.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'admin.students.update',
                    $student
                ) }}"
            >

                @csrf
                @method('PATCH')


                <input
                    type="hidden"
                    name="section"
                    value="information"
                >


                <div
                    class="grid gap-5
                           p-6
                           md:grid-cols-2"
                >

                    {{-- Student ID --}}
                    <div class="md:col-span-2">

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            Student ID
                            <span class="font-normal text-slate-400">
                                (Optional)
                            </span>
                        </label>

                        <input
                            type="text"
                            name="external_id"
                            value="{{ old(
                                'external_id',
                                $student->external_id
                            ) }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                        >

                        @error('external_id')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- First Name --}}
                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            First Name
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            value="{{ old(
                                'first_name',
                                $student->first_name
                            ) }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                        @error('first_name')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Last Name --}}
                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            Last Name
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="{{ old(
                                'last_name',
                                $student->last_name
                            ) }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                        @error('last_name')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Date of Birth --}}
                    {{-- Nickname --}}
                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold"
                        >
                            Nickname
                            <span class="font-normal text-slate-400">(Optional)</span>
                        </label>

                        <input
                            type="text"
                            name="nickname"
                            value="{{ old('nickname', $student->nickname) }}"
                            maxlength="100"
                            class="w-full rounded-xl border-slate-300"
                        >

                        @error('nickname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- Date of Birth --}}
                    <div class="md:col-span-2">

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            name="date_of_birth"
                            value="{{
                                old(
                                    'date_of_birth',
                                    $student
                                        ->date_of_birth
                                        ?->format('Y-m-d')
                                )
                            }}"
                            max="{{ now()->toDateString() }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                        @error('date_of_birth')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                @include(
                    'admin.students.partials.edit-buttons'
                )

            </form>

        @endif



        {{-- =================================================
            GUARDIANS
        ================================================== --}}

        @if ($section === 'guardian')

            <div
                class="border-b
                       border-slate-200
                       px-6 py-5"
            >

                <h2
                    class="text-xl
                           font-bold
                           text-slate-900"
                >
                    Edit Parent / Guardians
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-slate-500"
                >
                    Update the guardian contact details collected during registration.
                </p>

            </div>


            @if ($guardians->isNotEmpty())

                <form
                    method="POST"
                    action="{{ route(
                        'admin.students.update',
                        $student
                    ) }}"
                >

                    @csrf
                    @method('PATCH')


                    <input
                        type="hidden"
                        name="section"
                        value="guardian"
                    >


                    <div class="space-y-6 p-6">

                        @foreach ($guardians as $guardian)

                            <input
                                type="hidden"
                                name="guardians[{{ $guardian->id }}][id]"
                                value="{{ $guardian->id }}"
                            >


                            <div
                                class="overflow-hidden
                                       rounded-2xl
                                       border border-slate-200
                                       bg-slate-50"
                            >

                                <div
                                    class="border-b
                                           border-slate-200
                                           bg-white
                                           px-5 py-4"
                                >

                                    <h3
                                        class="text-lg
                                               font-bold
                                               text-slate-900"
                                    >
                                        Guardian {{ $loop->iteration }}
                                    </h3>

                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500"
                                    >
                                        {{ $guardian->first_name }}
                                        {{ $guardian->last_name }}
                                    </p>

                                </div>


                                <div
                                    class="grid gap-5
                                           p-5
                                           md:grid-cols-2"
                                >

                                    {{-- First Name --}}
                                    <div>

                                        <label
                                            class="mb-2 block
                                                   text-sm font-semibold"
                                        >
                                            First Name
                                        </label>

                                        <input
                                            type="text"
                                            name="guardians[{{ $guardian->id }}][first_name]"
                                            value="{{ old(
                                                'guardians.'
                                                . $guardian->id
                                                . '.first_name',
                                                $guardian->first_name
                                            ) }}"
                                            class="w-full
                                                   rounded-xl
                                                   border-slate-300
                                                   bg-white"
                                            required
                                        >

                                        @error(
                                            'guardians.'
                                            . $guardian->id
                                            . '.first_name'
                                        )

                                            <p
                                                class="mt-1
                                                       text-sm
                                                       text-red-600"
                                            >
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Last Name --}}
                                    <div>

                                        <label
                                            class="mb-2 block
                                                   text-sm font-semibold"
                                        >
                                            Last Name
                                        </label>

                                        <input
                                            type="text"
                                            name="guardians[{{ $guardian->id }}][last_name]"
                                            value="{{ old(
                                                'guardians.'
                                                . $guardian->id
                                                . '.last_name',
                                                $guardian->last_name
                                            ) }}"
                                            class="w-full
                                                   rounded-xl
                                                   border-slate-300
                                                   bg-white"
                                            required
                                        >

                                        @error(
                                            'guardians.'
                                            . $guardian->id
                                            . '.last_name'
                                        )

                                            <p
                                                class="mt-1
                                                       text-sm
                                                       text-red-600"
                                            >
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Email --}}
                                    <div>

                                        <label
                                            class="mb-2 block
                                                   text-sm font-semibold"
                                        >
                                            Email
                                        </label>

                                        <input
                                            type="email"
                                            name="guardians[{{ $guardian->id }}][email]"
                                            value="{{ old(
                                                'guardians.'
                                                . $guardian->id
                                                . '.email',
                                                $guardian->email
                                            ) }}"
                                            class="w-full
                                                   rounded-xl
                                                   border-slate-300
                                                   bg-white"
                                            required
                                        >

                                        @error(
                                            'guardians.'
                                            . $guardian->id
                                            . '.email'
                                        )

                                            <p
                                                class="mt-1
                                                       text-sm
                                                       text-red-600"
                                            >
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>


                                    {{-- Phone --}}
                                    <div>

                                        <label
                                            class="mb-2 block
                                                   text-sm font-semibold"
                                        >
                                            Phone
                                        </label>

                                        <input
                                            type="text"
                                            name="guardians[{{ $guardian->id }}][phone]"
                                            value="{{ old(
                                                'guardians.'
                                                . $guardian->id
                                                . '.phone',
                                                $guardian->phone
                                            ) }}"
                                            class="w-full
                                                   rounded-xl
                                                   border-slate-300
                                                   bg-white"
                                            required
                                        >

                                        @error(
                                            'guardians.'
                                            . $guardian->id
                                            . '.phone'
                                        )

                                            <p
                                                class="mt-1
                                                       text-sm
                                                       text-red-600"
                                            >
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    <div
                        class="flex items-center
                               justify-end
                               gap-3
                               border-t
                               border-slate-200
                               bg-slate-50
                               px-6 py-4"
                    >

                        <a
                            href="{{ route(
                                'admin.students.show',
                                $student
                            ) }}"
                            class="inline-flex
                                   h-11 items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-slate-300
                                   bg-white
                                   px-5
                                   text-sm
                                   font-semibold
                                   text-slate-600
                                   hover:bg-slate-100"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="inline-flex
                                   h-11 items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-600
                                   px-6
                                   text-sm
                                   font-semibold
                                   text-white
                                   hover:bg-blue-700"
                        >
                            Save Guardian Changes
                        </button>

                    </div>

                </form>


            @else

                <div class="p-6">

                    <p class="text-slate-500">
                        No guardian is linked to this student.
                    </p>

                </div>

            @endif

        @endif



        {{-- =================================================
            STATUS
        ================================================== --}}

        @if ($section === 'status')

            <div
                class="border-b
                       border-slate-200
                       px-6 py-5"
            >

                <h2
                    class="text-xl
                           font-bold"
                >
                    Edit Student Status
                </h2>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'admin.students.update',
                    $student
                ) }}"
            >

                @csrf
                @method('PATCH')


                <input
                    type="hidden"
                    name="section"
                    value="status"
                >


                <div
                    class="space-y-5
                           p-6"
                >

                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            Student Status
                        </label>

                        <select
                            name="student_status_id"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                            @foreach (
                                $studentStatuses
                                as $status
                            )

                                <option
                                    value="{{ $status->id }}"
                                    @selected(
                                        old(
                                            'student_status_id',
                                            $student
                                                ->student_status_id
                                        )
                                        ==
                                        $status->id
                                    )
                                >
                                    {{
                                        $status
                                            ->status_name
                                    }}
                                </option>

                            @endforeach

                        </select>

                        @error('student_status_id')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <div>

                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <label
                            class="inline-flex
                                   items-center gap-2"
                        >

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="rounded"
                                @checked(
                                    old(
                                        'is_active',
                                        $student->is_active
                                    )
                                )
                            >

                            Active Student

                        </label>

                    </div>

                </div>


                @include(
                    'admin.students.partials.edit-buttons'
                )

            </form>

        @endif



    </div>

</div>

@endsection
