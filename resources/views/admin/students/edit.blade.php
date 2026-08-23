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
                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            Student ID
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
                            required
                        >

                        @error('external_id')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Date of Birth --}}
                    <div>

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
                            name="email"
                            value="{{ old(
                                'email',
                                $student->email
                            ) }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                        @error('email')

                            <p class="mt-1 text-sm text-red-600">
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
                            name="phone"
                            value="{{ old(
                                'phone',
                                $student->phone
                            ) }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                        @error('phone')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Address --}}
                    <div class="md:col-span-2">

                        <label
                            class="mb-2 block
                                   text-sm font-semibold"
                        >
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >{{ old(
                            'address',
                            $student->address
                        ) }}</textarea>

                        @error('address')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Can Leave Alone --}}
                    <div class="md:col-span-2">

                        <input
                            type="hidden"
                            name="can_leave_alone"
                            value="0"
                        >

                        <label
                            class="inline-flex
                                   items-center gap-2"
                        >

                            <input
                                type="checkbox"
                                name="can_leave_alone"
                                value="1"
                                class="rounded
                                       border-slate-300"
                                @checked(
                                    old(
                                        'can_leave_alone',
                                        $student->can_leave_alone
                                    )
                                )
                            >

                            Student can leave alone

                        </label>

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
                    Edit all guardians linked to
                    {{ $student->first_name }}
                    {{ $student->last_name }}.
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


                                {{-- Guardian Header --}}
                                <div
                                    class="flex flex-col
                                           gap-3
                                           border-b
                                           border-slate-200
                                           bg-white
                                           px-5 py-4
                                           sm:flex-row
                                           sm:items-center
                                           sm:justify-between"
                                >

                                    <div>

                                        <div
                                            class="flex flex-wrap
                                                   items-center
                                                   gap-2"
                                        >

                                            <h3
                                                class="text-lg
                                                       font-bold
                                                       text-slate-900"
                                            >
                                                Guardian
                                                {{ $loop->iteration }}
                                            </h3>


                                            @if (
                                                $guardian
                                                    ->pivot
                                                    ->is_primary
                                            )

                                                <span
                                                    class="rounded-full
                                                           bg-blue-100
                                                           px-2.5 py-1
                                                           text-xs
                                                           font-semibold
                                                           text-blue-700"
                                                >
                                                    Primary
                                                </span>

                                            @endif


                                            @if (
                                                $guardian
                                                    ->pivot
                                                    ->is_emergency_contact
                                            )

                                                <span
                                                    class="rounded-full
                                                           bg-red-100
                                                           px-2.5 py-1
                                                           text-xs
                                                           font-semibold
                                                           text-red-700"
                                                >
                                                    Emergency Contact
                                                </span>

                                            @endif

                                        </div>


                                        <p
                                            class="mt-1
                                                   text-sm
                                                   text-slate-500"
                                        >
                                            {{ $guardian->first_name }}
                                            {{ $guardian->last_name }}
                                        </p>

                                    </div>


                                    {{-- Primary Guardian --}}
                                    <label
                                        class="inline-flex
                                               cursor-pointer
                                               items-center
                                               gap-2
                                               rounded-xl
                                               border
                                               border-blue-200
                                               bg-blue-50
                                               px-4 py-2"
                                    >

                                        <input
                                            type="radio"
                                            name="primary_guardian_id"
                                            value="{{ $guardian->id }}"
                                            class="border-slate-300
                                                   text-blue-600
                                                   focus:ring-blue-500"
                                            @checked(
                                                old(
                                                    'primary_guardian_id',
                                                    $guardians
                                                        ->first(
                                                            function ($item) {
                                                                return
                                                                    (bool)
                                                                    $item
                                                                        ->pivot
                                                                        ->is_primary;
                                                            }
                                                        )
                                                        ?->id
                                                )
                                                ==
                                                $guardian->id
                                            )
                                        >

                                        <span
                                            class="text-sm
                                                   font-semibold
                                                   text-blue-700"
                                        >
                                            Primary Guardian
                                        </span>

                                    </label>

                                </div>


                                {{-- Guardian Fields --}}
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


                                    {{-- Relationship --}}
                                    <div>

                                        <label
                                            class="mb-2 block
                                                   text-sm font-semibold"
                                        >
                                            Relationship
                                        </label>

                                        <input
                                            type="text"
                                            name="guardians[{{ $guardian->id }}][relationship]"
                                            value="{{ old(
                                                'guardians.'
                                                . $guardian->id
                                                . '.relationship',
                                                $guardian
                                                    ->pivot
                                                    ->relationship
                                            ) }}"
                                            placeholder="e.g. Mother, Father, Carer"
                                            class="w-full
                                                   rounded-xl
                                                   border-slate-300
                                                   bg-white"
                                        >

                                        @error(
                                            'guardians.'
                                            . $guardian->id
                                            . '.relationship'
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


                                    {{-- Address --}}
                                    <div>

                                        <label
                                            class="mb-2 block
                                                   text-sm font-semibold"
                                        >
                                            Address
                                        </label>

                                        <textarea
                                            name="guardians[{{ $guardian->id }}][address]"
                                            rows="2"
                                            class="w-full
                                                   rounded-xl
                                                   border-slate-300
                                                   bg-white"
                                        >{{ old(
                                            'guardians.'
                                            . $guardian->id
                                            . '.address',
                                            $guardian->address
                                        ) }}</textarea>

                                        @error(
                                            'guardians.'
                                            . $guardian->id
                                            . '.address'
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


                                    {{-- Emergency Contact --}}
                                    <div class="md:col-span-2">

                                        <input
                                            type="hidden"
                                            name="guardians[{{ $guardian->id }}][is_emergency_contact]"
                                            value="0"
                                        >

                                        <label
                                            class="inline-flex
                                                   items-center
                                                   gap-2"
                                        >

                                            <input
                                                type="checkbox"
                                                name="guardians[{{ $guardian->id }}][is_emergency_contact]"
                                                value="1"
                                                class="rounded
                                                       border-slate-300"
                                                @checked(
                                                    old(
                                                        'guardians.'
                                                        . $guardian->id
                                                        . '.is_emergency_contact',
                                                        $guardian
                                                            ->pivot
                                                            ->is_emergency_contact
                                                    )
                                                )
                                            >

                                            Emergency Contact

                                        </label>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    {{-- Guardian Buttons --}}
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
                        No guardian is linked
                        to this student.
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



        {{-- =================================================
            NOTES
        ================================================== --}}

        @if ($section === 'notes')

            <div
                class="border-b
                       border-slate-200
                       px-6 py-5"
            >

                <h2
                    class="text-xl
                           font-bold"
                >
                    Edit Student Notes
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
                    value="notes"
                >


                <div class="p-6">

                    <label
                        class="mb-2 block
                               text-sm font-semibold"
                    >
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        rows="6"
                        maxlength="2000"
                        class="w-full
                               rounded-xl
                               border-slate-300"
                    >{{ old(
                        'notes',
                        $student->notes
                    ) }}</textarea>

                    @error('notes')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                @include(
                    'admin.students.partials.edit-buttons'
                )

            </form>

        @endif

    </div>

</div>

@endsection