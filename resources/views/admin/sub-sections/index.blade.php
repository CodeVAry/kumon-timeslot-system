@extends('layouts.admin')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Sub-sections
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage sub-sections under each section.
            </p>
        </div>

        <a
            href="{{ route('admin.sub-sections.create') }}"
            class="inline-flex items-center px-4 py-2
                   bg-blue-600 text-white text-sm font-medium
                   rounded-lg hover:bg-blue-700"
        >
            Add Sub-section
        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="mb-4 px-4 py-3 rounded-lg
                    bg-green-50 border border-green-200
                    text-green-700">

            {{ session('success') }}

        </div>

    @endif


    {{-- Error Message --}}
    @if(session('error'))

        <div class="mb-4 px-4 py-3 rounded-lg
                    bg-red-50 border border-red-200
                    text-red-700">

            {{ session('error') }}

        </div>

    @endif


    {{-- Table --}}
    <div class="bg-white border border-gray-200
                rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase"
                        >
                            Sub-section
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase"
                        >
                            Section
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase"
                        >
                            Description
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase"
                        >
                            Status
                        </th>

                        <th
                            class="px-6 py-3 text-right
                                   text-xs font-semibold
                                   text-gray-500 uppercase"
                        >
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse($subSections as $subSection)

                        <tr class="hover:bg-gray-50">

                            {{-- Sub-section Name --}}
                            <td class="px-6 py-4 whitespace-nowrap">

                                <div class="font-medium text-gray-900">

                                    {{ $subSection->sub_section_name }}

                                </div>

                            </td>


                            {{-- Parent Section --}}
                            <td class="px-6 py-4 whitespace-nowrap">

                                <span class="text-sm text-gray-700">

                                    {{ $subSection->section?->section_name ?? '-' }}

                                </span>

                            </td>


                            {{-- Description --}}
                            <td class="px-6 py-4">

                                <span class="text-sm text-gray-600">

                                    {{ $subSection->description ?: '-' }}

                                </span>

                            </td>


                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">

                                @if($subSection->is_active)

                                    <span
                                        class="inline-flex items-center
                                               px-2.5 py-1 rounded-full
                                               text-xs font-medium
                                               bg-green-100 text-green-700"
                                    >
                                        Active
                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center
                                               px-2.5 py-1 rounded-full
                                               text-xs font-medium
                                               bg-gray-100 text-gray-700"
                                    >
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">

                                <div class="inline-flex items-center gap-3">

                                    <a
                                        href="{{ route(
                                            'admin.sub-sections.edit',
                                            $subSection
                                        ) }}"
                                        class="text-blue-600
                                               hover:text-blue-800
                                               text-sm font-medium"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.sub-sections.destroy',
                                            $subSection
                                        ) }}"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this sub-section?'
                                        )"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-red-600
                                                   hover:text-red-800
                                                   text-sm font-medium"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-12 text-center"
                            >

                                <div class="text-gray-500">

                                    No sub-sections found.

                                </div>

                                <a
                                    href="{{ route(
                                        'admin.sub-sections.create'
                                    ) }}"
                                    class="inline-block mt-3
                                           text-blue-600
                                           hover:text-blue-800
                                           text-sm font-medium"
                                >
                                    Add your first sub-section
                                </a>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    @if($subSections->hasPages())

        <div class="mt-6">

            {{ $subSections->links() }}

        </div>

    @endif

</div>

@endsection
