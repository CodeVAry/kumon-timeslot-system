@extends('layouts.admin')

@section('title', 'Create Section')

@section('page-title', 'Create Section')

@php
    $breadcrumbs = [
        [
            'label' => 'Sections',
            'url' => route('admin.sections.index'),
        ],
        [
            'label' => 'Create Section',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    <div class="mx-auto max-w-3xl">

        <div class="rounded-xl border
                    border-gray-200 bg-white
                    shadow-sm">

            {{-- Header --}}
            <div class="border-b border-gray-200 p-6">

                <h2 class="text-xl font-bold text-gray-800">
                    Create New Section
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Add a new class section to the system.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('admin.sections.store') }}"
            >
                @csrf

                <div class="space-y-6 p-6">

                    {{-- Section name --}}
                    <div>

                        <label
                            for="section_name"
                            class="mb-2 block text-sm
                                   font-semibold text-gray-700"
                        >
                            Section Name

                            <span class="text-red-500">
                                *
                            </span>
                        </label>

                        <input
                            type="text"
                            name="section_name"
                            id="section_name"
                            value="{{ old('section_name') }}"
                            maxlength="100"
                            required
                            autofocus
                            placeholder="Example: English"
                            class="w-full rounded-lg
                                   border-gray-300 shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('section_name')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Description --}}
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
                            placeholder="Optional section description"
                            class="w-full rounded-lg
                                   border-gray-300 shadow-sm
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >{{ old('description') }}</textarea>

                        <p class="mt-1 text-sm text-gray-500">
                            Description is optional.
                        </p>

                        @error('description')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Active status --}}
                    <div>

                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <label class="inline-flex
                                      items-center gap-3">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="rounded
                                       border-gray-300
                                       text-blue-600
                                       focus:ring-blue-500"
                                @checked(
                                    old('is_active', true)
                                )
                            >

                            <span class="text-sm
                                         font-semibold
                                         text-gray-700">
                                Active Section
                            </span>

                        </label>

                        <p class="mt-1 text-sm text-gray-500">
                            Only active sections will appear
                            when creating section offerings.
                        </p>

                        @error('is_active')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="flex justify-end gap-3
                            border-t border-gray-200
                            bg-gray-50 p-6">

                    <a
                        href="{{ route(
                            'admin.sections.index'
                        ) }}"
                        class="rounded-lg border
                               border-gray-300 bg-white
                               px-4 py-2.5 text-sm
                               font-semibold text-gray-700
                               hover:bg-gray-100"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600
                               px-4 py-2.5 text-sm
                               font-semibold text-white
                               hover:bg-blue-700"
                    >
                        Save Section
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
