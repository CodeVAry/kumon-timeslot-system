@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Edit Sub-section
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Update the sub-section details.
        </p>

    </div>


    {{-- Validation Summary --}}
    @if($errors->any())

        <div class="mb-5 px-4 py-3 rounded-lg
                    bg-red-50 border border-red-200">

            <p class="font-medium text-red-700 mb-2">
                Please correct the following:
            </p>

            <ul class="list-disc list-inside
                       text-sm text-red-600">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Form Card --}}
    <div class="bg-white border border-gray-200
                rounded-xl shadow-sm">

        <form
            method="POST"
            action="{{ route(
                'admin.sub-sections.update',
                $subSection
            ) }}"
        >

            @csrf
            @method('PUT')


            <div class="p-6 space-y-6">

                {{-- Section --}}
                <div>

                    <label
                        for="section_id"
                        class="block text-sm font-medium
                               text-gray-700 mb-1"
                    >
                        Section
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="section_id"
                        name="section_id"
                        required
                        class="w-full rounded-lg
                               border-gray-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option value="">
                            Select Section
                        </option>

                        @foreach($sections as $section)

                            <option
                                value="{{ $section->id }}"
                                @selected(
                                    old(
                                        'section_id',
                                        $subSection->section_id
                                    ) == $section->id
                                )
                            >

                                {{ $section->section_name }}

                            </option>

                        @endforeach

                    </select>


                    @error('section_id')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Sub-section Name --}}
                <div>

                    <label
                        for="sub_section_name"
                        class="block text-sm font-medium
                               text-gray-700 mb-1"
                    >
                        Sub-section Name
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="sub_section_name"
                        name="sub_section_name"
                        value="{{ old(
                            'sub_section_name',
                            $subSection->sub_section_name
                        ) }}"
                        required
                        maxlength="100"
                        class="w-full rounded-lg
                               border-gray-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >


                    @error('sub_section_name')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Description --}}
                <div>

                    <label
                        for="description"
                        class="block text-sm font-medium
                               text-gray-700 mb-1"
                    >
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full rounded-lg
                               border-gray-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >{{ old(
                        'description',
                        $subSection->description
                    ) }}</textarea>


                    @error('description')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Active --}}
                <div>

                    <label class="inline-flex items-center">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(
                                old(
                                    'is_active',
                                    $subSection->is_active
                                )
                            )
                            class="rounded border-gray-300
                                   text-blue-600
                                   focus:ring-blue-500"
                        >

                        <span class="ml-2 text-sm text-gray-700">
                            Active Sub-section
                        </span>

                    </label>

                </div>

            </div>


            {{-- Footer Buttons --}}
            <div
                class="px-6 py-4 bg-gray-50
                       border-t border-gray-200
                       flex justify-end gap-3"
            >

                <a
                    href="{{ route(
                        'admin.sub-sections.index'
                    ) }}"
                    class="px-4 py-2
                           border border-gray-300
                           rounded-lg
                           text-sm font-medium
                           text-gray-700
                           bg-white
                           hover:bg-gray-50"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="px-4 py-2
                           bg-blue-600
                           text-white
                           text-sm font-medium
                           rounded-lg
                           hover:bg-blue-700"
                >
                    Update Sub-section
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
