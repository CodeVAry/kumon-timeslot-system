@extends('layouts.admin')

@section('title', 'Add Student')

@section('page-title', 'Add Student')

@section('content')

    <div class="mx-auto max-w-4xl">

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

                <span class="text-gray-400">→</span>

                <span class="text-gray-500">
                    Guardian Details
                </span>

                <span class="text-gray-400">→</span>

                <span class="text-gray-500">
                    Class Enrolment
                </span>

            </div>

        </div>

        <form method="POST" action="{{ route('admin.student-registration.student.store') }}">
            @csrf

            <div class="rounded-xl border border-gray-200
                    bg-white shadow-sm">

                <div class="border-b border-gray-200 p-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Student Details
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Enter the student information.
                        Nothing will be saved until registration is completed.
                    </p>

                </div>

                <div class="grid gap-6 p-6 md:grid-cols-2">

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Student ID *
                        </label>

                        <input type="text" name="external_id"
                            value="{{ old('external_id', $studentData['external_id'] ?? '') }}"
                            required class="w-full rounded-lg border-gray-300">

                        @error('external_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            First Name *
                        </label>

                        <input type="text" name="first_name"
                            value="{{ old('first_name', $studentData['first_name'] ?? '') }}"
                            required class="w-full rounded-lg border-gray-300">

                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Last Name *
                        </label>

                        <input type="text" name="last_name"
                            value="{{ old('last_name', $studentData['last_name'] ?? '') }}"
                            required class="w-full rounded-lg border-gray-300">

                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- <div>
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Email *
                        </label>

                        <input type="email" name="email"
                            value="{{ old('email', $studentData['email'] ?? '') }}"
                            required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Phone *
                        </label>

                        <input type="text" name="phone"
                            value="{{ old('phone', $studentData['phone'] ?? '') }}"
                            required class="w-full rounded-lg border-gray-300">
                    </div> --}}

                    <div>
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Date of Birth *
                        </label>

                        <input type="date" name="date_of_birth"
                            value="{{ old('date_of_birth', $studentData['date_of_birth'] ?? '') }}"
                            max="{{ now()->toDateString() }}" required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Student Status *
                        </label>

                        <select name="student_status_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Status</option>

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

                    {{-- <div class="md:col-span-2">
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Address *
                        </label>

                        <textarea name="address" rows="3" required class="w-full rounded-lg border-gray-300">{{ old('address', $studentData['address'] ?? '') }}</textarea>
                    </div>

                    <div class="md:col-span-2">

                        <input type="hidden" name="can_leave_alone" value="0">

                        <label class="inline-flex items-center gap-3">

                            <input type="checkbox" name="can_leave_alone" value="1"
                                class="rounded border-gray-300
                                   text-blue-600"
                                @checked(old('can_leave_alone', $studentData['can_leave_alone'] ?? false))>

                            <span class="text-sm font-semibold
                                     text-gray-700">
                                Student can leave alone
                            </span>

                        </label>

                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm
                                  font-semibold text-gray-700">
                            Notes
                        </label>

                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300">{{ old('notes', $studentData['notes'] ?? '') }}</textarea>
                    </div> --}}

                </div>

                <div class="flex justify-end gap-3 border-t
                        border-gray-200 bg-gray-50 p-6">

                    <form method="POST"
                        action="{{ route('admin.student-registration.cancel') }}">
                        @csrf

                        <button type="submit"
                            class="rounded-lg border border-gray-300
                               bg-white px-4 py-2.5 text-sm
                               font-semibold text-gray-700">
                            Cancel
                        </button>
                    </form>

                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700">
                        Next: Guardian Details →
                    </button>

                </div>

            </div>

        </form>

    </div>

@endsection
