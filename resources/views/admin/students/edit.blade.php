@extends('layouts.admin')

@section('title', 'Edit Student')

@section('page-title', 'Edit Student')

@section('content')

<div class="mx-auto max-w-4xl">


    {{-- Back --}}
    <a
        href="{{ route(
            'admin.students.show',
            $student
        ) }}"
        class="mb-5 inline-flex
               text-sm font-semibold
               text-blue-600"
    >
        ← Back to Student Profile
    </a>


    <div
        class="rounded-2xl
               border border-slate-200
               bg-white shadow-sm"
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
                                        ?->format(
                                            'Y-m-d'
                                        )
                                )
                            }}"
                            class="w-full
                                   rounded-xl
                                   border-slate-300"
                            required
                        >

                    </div>


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

                    </div>


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

                    </div>


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

                    </div>


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

                    </div>


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

                    </div>


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
                                        $student
                                            ->can_leave_alone
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
            GUARDIAN
        ================================================== --}}
        @if ($section === 'guardian')

            <div
                class="border-b
                       border-slate-200
                       px-6 py-5"
            >
                <h2
                    class="text-xl
                           font-bold"
                >
                    Edit Guardian
                </h2>
            </div>


            @if ($primaryGuardian)

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

                    <input
                        type="hidden"
                        name="guardian_id"
                        value="{{
                            $primaryGuardian->id
                        }}"
                    >


                    <div
                        class="grid gap-5
                               p-6
                               md:grid-cols-2"
                    >


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
                                    $primaryGuardian
                                        ->first_name
                                ) }}"
                                class="w-full
                                       rounded-xl
                                       border-slate-300"
                                required
                            >

                        </div>


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
                                    $primaryGuardian
                                        ->last_name
                                ) }}"
                                class="w-full
                                       rounded-xl
                                       border-slate-300"
                                required
                            >

                        </div>


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
                                    $primaryGuardian
                                        ->phone
                                ) }}"
                                class="w-full
                                       rounded-xl
                                       border-slate-300"
                                required
                            >

                        </div>


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
                                    $primaryGuardian
                                        ->email
                                ) }}"
                                class="w-full
                                       rounded-xl
                                       border-slate-300"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-2 block
                                       text-sm font-semibold"
                            >
                                Relationship
                            </label>

                            <input
                                type="text"
                                name="relationship"
                                value="{{ old(
                                    'relationship',
                                    $primaryGuardian
                                        ->pivot
                                        ->relationship
                                ) }}"
                                class="w-full
                                       rounded-xl
                                       border-slate-300"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-2 block
                                       text-sm font-semibold"
                            >
                                Address
                            </label>

                            <textarea
                                name="address"
                                rows="2"
                                class="w-full
                                       rounded-xl
                                       border-slate-300"
                            >{{ old(
                                'address',
                                $primaryGuardian
                                    ->address
                            ) }}</textarea>

                        </div>


                        <div class="md:col-span-2">

                            <input
                                type="hidden"
                                name="is_emergency_contact"
                                value="0"
                            >

                            <label
                                class="inline-flex
                                       items-center gap-2"
                            >

                                <input
                                    type="checkbox"
                                    name="is_emergency_contact"
                                    value="1"
                                    class="rounded"
                                    @checked(
                                        old(
                                            'is_emergency_contact',
                                            $primaryGuardian
                                                ->pivot
                                                ->is_emergency_contact
                                        )
                                    )
                                >

                                Emergency Contact

                            </label>

                        </div>

                    </div>


                    @include(
                        'admin.students.partials.edit-buttons'
                    )

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
                    class="space-y-5 p-6"
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
                                        $student
                                            ->is_active
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

                </div>


                @include(
                    'admin.students.partials.edit-buttons'
                )

            </form>

        @endif

    </div>

</div>

@endsection
