@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            Add Sub-section
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Select a section and create a
            sub-section under it.
        </p>
    </div>


    <div class="bg-white border border-gray-200 rounded-xl p-6">

        <form
            method="POST"
            action="{{ route('admin.sub-sections.store') }}"
        >

            @csrf


            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Section *
                </label>

                <select
                    name="section_id"
                    required
                    class="w-full rounded-lg border-gray-300"
                >

                    <option value="">
                        Select Section
                    </option>

                    @foreach($sections as $section)

                        <option
                            value="{{ $section->id }}"
                            @selected(
                                old('section_id')
                                == $section->id
                            )
                        >
                            {{ $section->section_name }}
                        </option>

                    @endforeach

                </select>

                @error('section_id')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sub-section Name *
                </label>

                <input
                    type="text"
                    name="sub_section_name"
                    value="{{ old('sub_section_name') }}"
                    required
                    placeholder="Example: 3A"
                    class="w-full rounded-lg border-gray-300"
                >

                @error('sub_section_name')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>

                <textarea
                    name="description"
                    rows="3"
                    class="w-full rounded-lg border-gray-300"
                >{{ old('description') }}</textarea>

            </div>


            <div class="mb-6">

                <label class="inline-flex items-center">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(
                            old('is_active', true)
                        )
                    >

                    <span class="ml-2 text-sm text-gray-700">
                        Active
                    </span>

                </label>

            </div>


            <div class="flex justify-end gap-3">

                <a
                    href="{{ route('admin.sub-sections.index') }}"
                    class="px-4 py-2 border rounded-lg"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg"
                >
                    Save Sub-section
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
