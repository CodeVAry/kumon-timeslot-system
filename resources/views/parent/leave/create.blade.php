@extends('layouts.parent')

@section('title', 'Create Leave Request')

@section('page-title', 'Create Leave Request')


@section('content')

    <div
        class="min-h-full
           rounded-[28px]
           bg-cyan-50/70
           p-5
           sm:p-6
           lg:p-8">

        {{-- =========================================================
        BACK
    ========================================================== --}}

        <div>

            <a href="{{ route('parent.leave.index') }}"
                class="inline-flex
                   items-center
                   gap-2
                   text-sm
                   font-semibold
                   text-violet-700
                   transition
                   hover:text-violet-900">
                ← Back to Leave Management
            </a>

        </div>



        {{-- =========================================================
        HEADER
    ========================================================== --}}

        <div class="mt-6">

            <p
                class="text-xs
                   font-bold
                   uppercase
                   tracking-[0.15em]
                   text-violet-600">
                Parent Portal
            </p>


            <h1
                class="mt-2
                   text-3xl
                   font-bold
                   tracking-tight
                   text-slate-900">
                Create Leave Request
            </h1>


            <p
                class="mt-2
                   max-w-2xl
                   text-sm
                   leading-6
                   text-slate-500">
                Submit a planned leave request for

                <span class="font-semibold
                       text-slate-700">
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </span>.

                The request will be sent to centre staff
                for approval.
            </p>

        </div>



        {{-- =========================================================
        ERRORS
    ========================================================== --}}

        @if ($errors->any())

            <div
                class="mt-6
                   rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   px-5 py-4">

                <p class="font-semibold
                       text-red-700">
                    Please correct the following:
                </p>


                <ul
                    class="mt-2
                       list-disc
                       space-y-1
                       pl-5
                       text-sm
                       text-red-600">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>

            </div>

        @endif



        {{-- =========================================================
        STUDENT INFORMATION
    ========================================================== --}}

        <section
            class="mt-7
               rounded-[26px]
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm">

            <div
                class="flex
                   flex-col
                   gap-5
                   lg:flex-row
                   lg:items-start
                   lg:justify-between">

                <div>

                    <p
                        class="text-xs
                           font-bold
                           uppercase
                           tracking-wide
                           text-blue-600">
                        Student Information
                    </p>


                    <h2
                        class="mt-2
                           text-2xl
                           font-bold
                           text-slate-900">
                        {{ $student->first_name }}
                        {{ $student->last_name }}
                    </h2>


                    <p class="mt-1
                           text-sm
                           text-slate-500">
                        Student ID:
                        {{ $student->external_id ?? '—' }}
                    </p>

                </div>


                <div
                    class="rounded-xl
                       border
                       border-blue-100
                       bg-blue-50
                       px-4 py-3">

                    <p class="text-sm
                           font-semibold
                           text-blue-700">
                        Approval Required
                    </p>


                    <p
                        class="mt-1
                           text-xs
                           leading-5
                           text-blue-600">
                        This leave will not become active
                        until centre staff approve the request.
                    </p>

                </div>

            </div>



            {{-- =====================================================
            ACTIVE CLASSES
        ====================================================== --}}

            <div class="mt-6
                   border-t
                   border-slate-100
                   pt-5">

                <p class="text-sm
                       font-bold
                       text-slate-800">
                    Active Enrolled Classes
                </p>


                <p class="mt-1
                       text-xs
                       text-slate-500">
                    If approved, this leave will apply
                    to all active enrolled classes.
                </p>


                <div
                    class="mt-4
                       grid
                       gap-3
                       md:grid-cols-2
                       xl:grid-cols-3">

                    @forelse ($enrolments as $enrolment)
                        @php

                            $offering = $enrolment->sectionOffering;

                        @endphp


                        <div
                            class="rounded-2xl
                               border
                               border-slate-200
                               bg-slate-50
                               p-4">

                            <p class="font-bold
                                   text-slate-900">
                                {{ $offering?->section?->section_name ?? 'Class' }}
                            </p>


                            @if ($offering)
                                <p
                                    class="mt-2
                                       text-sm
                                       text-slate-500">
                                    {{ $offering->day?->day_name ?? '—' }}

                                    <span class="mx-1
                                           text-slate-300">
                                        •
                                    </span>

                                    {{ \Carbon\Carbon::parse($offering->start_time)->format('g:i A') }}

                                    –

                                    {{ \Carbon\Carbon::parse($offering->end_time)->format('g:i A') }}
                                </p>
                            @endif

                        </div>


                    @empty

                        <div
                            class="rounded-xl
                               border
                               border-dashed
                               border-slate-300
                               p-5
                               text-sm
                               text-slate-500">
                            No active enrolled classes.
                        </div>
                    @endforelse

                </div>

            </div>

        </section>



        {{-- =========================================================
        REQUEST FORM
    ========================================================== --}}

        <section
            class="mt-6
               rounded-[26px]
               border
               border-slate-200
               bg-white
               p-6
               shadow-sm">

            <div>

                <p
                    class="text-xs
                       font-bold
                       uppercase
                       tracking-wide
                       text-violet-600">
                    Leave Request
                </p>


                <h2
                    class="mt-2
                       text-xl
                       font-bold
                       text-slate-900">
                    Leave Information
                </h2>


                <p class="mt-1
                       text-sm
                       text-slate-500">
                    Enter the leave dates and homework requirement.
                </p>

            </div>



            <form method="POST" action="{{ route('parent.leave.store') }}" class="mt-6">

                @csrf


                <div class="grid
                       gap-5
                       md:grid-cols-2">


                    {{-- =================================================
                    START DATE
                ================================================== --}}

                    <div>

                        <label for="start_date"
                            class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700">
                            Start Date

                            <span class="text-red-500">
                                *
                            </span>
                        </label>


                        <input type="date" name="start_date" id="start_date"
                            value="{{ old('start_date') }}" required
                            class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-4
                               text-sm
                               text-slate-800
                               outline-none
                               transition
                               focus:border-violet-500
                               focus:ring-4
                               focus:ring-violet-100">


                        @error('start_date')
                            <p
                                class="mt-2
                                   text-xs
                                   text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                    EXPECTED RETURN
                ================================================== --}}

                    <div>

                        <label for="expected_return_date"
                            class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700">
                            Expected Return Date

                            <span class="text-red-500">
                                *
                            </span>
                        </label>


                        <input type="date" name="expected_return_date" id="expected_return_date"
                            value="{{ old('expected_return_date') }}"
                            required
                            class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-4
                               text-sm
                               text-slate-800
                               outline-none
                               transition
                               focus:border-violet-500
                               focus:ring-4
                               focus:ring-violet-100">


                        @error('expected_return_date')
                            <p
                                class="mt-2
                                   text-xs
                                   text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                    HOMEWORK
                ================================================== --}}

                    <div>

                        <label for="homework_requirement"
                            class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700">
                            Homework Required

                            <span class="text-red-500">
                                *
                            </span>
                        </label>


                        <select name="homework_requirement" id="homework_requirement" required
                            class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-4
                               text-sm
                               text-slate-800
                               outline-none
                               transition
                               focus:border-violet-500
                               focus:ring-4
                               focus:ring-violet-100">

                            <option value="">
                                Select homework requirement
                            </option>

                            <option value="none_required" @selected(old('homework_requirement') === 'none_required')>
                                None required
                            </option>

                            <option value="same_as_normal" @selected(old('homework_requirement') === 'same_as_normal')>
                                Same as normal
                            </option>

                            <option value="increase" @selected(old('homework_requirement') === 'increase')>
                                Increase
                            </option>

                            <option value="decrease" @selected(old('homework_requirement') === 'decrease')>
                                Decrease
                            </option>

                        </select>


                        <p
                            class="mt-2
                               text-xs
                               text-slate-500">
                            “Same as normal” is selected by default.
                        </p>


                        @error('homework_requirement')
                            <p
                                class="mt-2
                                   text-xs
                                   text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- =================================================
                    REASON
                ================================================== --}}

                    <div>

                        <label for="reason"
                            class="mb-2
                               block
                               text-sm
                               font-semibold
                               text-slate-700">
                            Reason
                        </label>


                        <input type="text" name="reason" id="reason"
                            value="{{ old('reason') }}"
                            maxlength="255" placeholder="Optional"
                            class="h-12
                               w-full
                               rounded-xl
                               border
                               border-slate-300
                               bg-white
                               px-4
                               text-sm
                               text-slate-800
                               outline-none
                               transition
                               placeholder:text-slate-400
                               focus:border-violet-500
                               focus:ring-4
                               focus:ring-violet-100">


                        @error('reason')
                            <p
                                class="mt-2
                                   text-xs
                                   text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>



                {{-- =================================================
                NOTES
            ================================================== --}}

                <div class="mt-5">

                    <label for="notes"
                        class="mb-2
                           block
                           text-sm
                           font-semibold
                           text-slate-700">
                        Notes
                    </label>


                    <textarea name="notes" id="notes" rows="5" maxlength="2000"
                        placeholder="Add any extra information for centre staff..."
                        class="w-full
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           p-4
                           text-sm
                           text-slate-800
                           outline-none
                           transition
                           placeholder:text-slate-400
                           focus:border-violet-500
                           focus:ring-4
                           focus:ring-violet-100">{{ old('notes') }}</textarea>


                    @error('notes')
                        <p class="mt-2
                               text-xs
                               text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>



                {{-- =================================================
                INFORMATION
            ================================================== --}}

                <div
                    class="mt-6
                       rounded-2xl
                       border
                       border-amber-200
                       bg-amber-50
                       p-4">

                    <p class="text-sm
                           font-semibold
                           text-amber-800">
                        Leave request approval
                    </p>


                    <p
                        class="mt-1
                           text-xs
                           leading-5
                           text-amber-700">
                        After submitting, the request status will be
                        Pending Approval. Centre staff can approve or
                        reject the request. You can check the status
                        from Leave Management.
                    </p>

                </div>



                {{-- =================================================
                ACTIONS
            ================================================== --}}

                <div
                    class="mt-7
                       flex
                       flex-wrap
                       justify-end
                       gap-3">

                    <a href="{{ route('parent.leave.index') }}"
                        class="inline-flex
                           h-12
                           items-center
                           justify-center
                           rounded-xl
                           border
                           border-slate-300
                           bg-white
                           px-6
                           text-sm
                           font-semibold
                           text-slate-600
                           transition
                           hover:bg-slate-50">
                        Cancel
                    </a>


                    <button type="submit"
                        class="inline-flex
                           h-12
                           items-center
                           justify-center
                           rounded-xl
                           bg-violet-600
                           px-7
                           text-sm
                           font-bold
                           text-white
                           shadow-sm
                           transition
                           hover:bg-violet-700
                           focus:outline-none
                           focus:ring-4
                           focus:ring-violet-200">
                        Submit Leave Request
                    </button>

                </div>

            </form>

        </section>

    </div>

@endsection
