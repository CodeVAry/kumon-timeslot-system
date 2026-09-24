@extends('layouts.admin')

@section('title', 'Add Student')

@section('page-title', 'Add Student')

@section('content')

    <div class="mx-auto max-w-4xl">

        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-200
                   bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif


        @if (session('error'))
            <div class="mb-5 rounded-lg border border-red-200
                   bg-red-50 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif


        <div class="mb-6 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <span
                    class="flex h-9 w-9 items-center
                       justify-center rounded-full
                       bg-blue-600 font-bold text-white">
                    1
                </span>

                <span class="font-semibold text-blue-700">
                    Student Details
                </span>

                <span class="text-gray-400">
                    →
                </span>

                <span class="text-gray-500">
                    Guardian Details
                </span>

                <span class="text-gray-400">
                    →
                </span>

                <span class="text-gray-500">
                    Class Enrolment
                </span>

            </div>

        </div>


        <form id="studentDetailsForm" method="POST" action="{{ route('admin.student-registration.student.store') }}">

            @csrf


            <div class="rounded-xl border border-gray-200
                   bg-white shadow-sm">

                <div class="border-b border-gray-200 p-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Student Details
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Enter the student information.
                    </p>

                </div>


                <div class="grid gap-6 p-6 md:grid-cols-2">

                    {{-- Student ID --}}
                    <div class="md:col-span-2">

                        <label for="external_id"
                            class="mb-2 block text-sm
                               font-semibold text-gray-700">
                            Student ID
                            <span class="font-normal text-gray-400">
                                (Optional)
                            </span>
                        </label>


                        <input id="external_id" type="text" name="external_id"
                            value="{{ old('external_id', $studentData['external_id'] ?? '') }}"
                            placeholder="Can be added later" class="w-full rounded-lg border-gray-300">


                        @error('external_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- First Name --}}
                    <div>

                        <label for="first_name"
                            class="mb-2 block text-sm
                               font-semibold text-gray-700">
                            First Name *
                        </label>


                        <input id="first_name" type="text" name="first_name"
                            value="{{ old('first_name', $studentData['first_name'] ?? '') }}" required
                            class="w-full rounded-lg border-gray-300">


                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Last Name --}}
                    <div>

                        <label for="last_name"
                            class="mb-2 block text-sm
                               font-semibold text-gray-700">
                            Last Name *
                        </label>


                        <input id="last_name" type="text" name="last_name"
                            value="{{ old('last_name', $studentData['last_name'] ?? '') }}" required
                            class="w-full rounded-lg border-gray-300">


                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>
                        <label for="nickname" class="mb-2 block text-sm font-semibold text-gray-700">
                            Nickname
                            <span class="font-normal text-gray-400">(Optional)</span>
                        </label>

                        <input id="nickname" type="text" name="nickname"
                            value="{{ old('nickname', $studentData['nickname'] ?? '') }}" maxlength="100"
                            class="w-full rounded-lg border-gray-300">

                        @error('nickname')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Date of Birth --}}
                    <div>

                        <label for="date_of_birth"
                            class="mb-2 block text-sm
                               font-semibold text-gray-700">
                            Date of Birth *
                        </label>


                        <input id="date_of_birth" type="date" name="date_of_birth"
                            value="{{ old('date_of_birth', $studentData['date_of_birth'] ?? '') }}"
                            max="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300">


                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Student Status --}}
                    <div>

                        <label for="student_status_id"
                            class="mb-2 block text-sm
                               font-semibold text-gray-700">
                            Student Status *
                        </label>


                        <select id="student_status_id" name="student_status_id" required
                            class="w-full rounded-lg border-gray-300">

                            <option value="">
                                Select Status
                            </option>


                            @foreach ($studentStatuses as $status)
                                <option value="{{ $status->id }}" @selected(old('student_status_id', $studentData['student_status_id'] ?? '') == $status->id)>
                                    {{ $status->status_name }}
                                </option>
                            @endforeach

                        </select>


                        @error('student_status_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                <div class="flex justify-end gap-3 border-t
                       border-gray-200 bg-gray-50 p-6">

                    <button type="submit" form="clearStudentForm"
                        class="rounded-lg border border-gray-300
                           bg-white px-4 py-2.5 text-sm
                           font-semibold text-gray-700
                           hover:bg-gray-100">
                        Clear
                    </button>


                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700">
                        Next: Guardian Details →
                    </button>

                </div>

            </div>

        </form>


        {{-- Separate form avoids invalid nested forms --}}
        <form id="clearStudentForm" method="POST" action="{{ route('admin.student-registration.cancel') }}"
            class="hidden">
            @csrf
        </form>

    </div>

@endsection
