@extends('layouts.admin')

@section('title', 'Create Student Status')

@section('page-title', 'Create Student Status')

@php
    $breadcrumbs = [
        [
            'label' => 'Student Statuses',
            'url' => route(
                'admin.student-statuses.index'
            ),
        ],
        [
            'label' => 'Create Status',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    <div class="mx-auto max-w-3xl">

        <div class="rounded-xl border border-gray-200
                    bg-white shadow-sm">

            <div class="border-b border-gray-200 p-6">

                <h2 class="text-xl font-bold text-gray-800">
                    Create Student Status
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Add a status that employees can select
                    for students.
                </p>

            </div>

            <form
                method="POST"
                action="{{ route(
                    'admin.student-statuses.store'
                ) }}"
            >
                @csrf

                <div class="space-y-6 p-6">

                    <div>
                        <label
                            for="status_name"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Status Name
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="status_name"
                            id="status_name"
                            value="{{ old('status_name') }}"
                            maxlength="50"
                            required
                            autofocus
                            placeholder="Example: Active"
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('status_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="color_code"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Status Colour
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="flex items-center gap-4">

                            <input
                                type="color"
                                name="color_code"
                                id="color_code"
                                value="{{ old(
                                    'color_code',
                                    '#2563EB'
                                ) }}"
                                required
                                class="h-12 w-20 cursor-pointer
                                       rounded-lg border
                                       border-gray-300 bg-white p-1"
                            >

                            <span class="text-sm text-gray-500">
                                Select the badge colour for this status.
                            </span>

                        </div>

                        @error('color_code')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="description"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            maxlength="1000"
                            placeholder="Optional status description"
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >{{ old('description') }}</textarea>

                        @error('description')
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

                        <label class="inline-flex items-center gap-3">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="rounded border-gray-300
                                       text-blue-600 focus:ring-blue-500"
                                @checked(old('is_active', true))
                            >

                            <span class="text-sm font-semibold
                                         text-gray-700">
                                Available for Selection
                            </span>

                        </label>

                        <p class="mt-1 text-sm text-gray-500">
                            Inactive statuses will not appear when
                            registering or updating students.
                        </p>

                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 border-t
                            border-gray-200 bg-gray-50 p-6">

                    <a
                        href="{{ route(
                            'admin.student-statuses.index'
                        ) }}"
                        class="rounded-lg border border-gray-300
                               bg-white px-4 py-2.5 text-sm
                               font-semibold text-gray-700
                               hover:bg-gray-100"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-blue-700"
                    >
                        Save Status
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
