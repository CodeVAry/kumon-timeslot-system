@extends('layouts.admin')

@section('title', 'Sections')

@section('page-title', 'Section Management')

@php
    $breadcrumbs = [
        [
            'label' => 'Class Setup',
            'url' => null,
        ],
        [
            'label' => 'Sections',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    {{-- Success message --}}
    @if (session('success'))

        <div class="mb-5 rounded-lg border border-green-200
                    bg-green-50 px-4 py-3 text-sm text-green-700">

            {{ session('success') }}

        </div>

    @endif


    {{-- Error message --}}
    @if (session('error'))

        <div class="mb-5 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700">

            {{ session('error') }}

        </div>

    @endif


    <div class="rounded-xl border border-gray-200
                bg-white shadow-sm">

        {{-- Header --}}
        <div class="flex flex-col gap-4 border-b
                    border-gray-200 p-6
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <div>

                <h2 class="text-xl font-bold text-gray-800">
                    Class Sections
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage the sections available in the centre.
                </p>

            </div>

            @if (
                auth()->user()
                    ->hasPermission('sections.create')
            )

                <a
                    href="{{ route('admin.sections.create') }}"
                    class="inline-flex items-center
                           justify-center rounded-lg
                           bg-blue-600 px-4 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700"
                >
                    + Create Section
                </a>

            @endif

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y
                          divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500">
                            ID
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500">
                            Section Name
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500">
                            Description
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500">
                            Created
                        </th>

                        <th class="px-6 py-3 text-right
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200
                              bg-white">

                    @forelse ($sections as $section)

                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-gray-500">

                                {{ $section->id }}

                            </td>


                            <td class="px-6 py-4">

                                <p class="text-sm font-semibold
                                          text-gray-800">

                                    {{ $section->section_name }}

                                </p>

                            </td>


                            <td class="max-w-md px-6 py-4
                                       text-sm text-gray-600">

                                @if ($section->description)

                                    <p class="line-clamp-2">
                                        {{ $section->description }}
                                    </p>

                                @else

                                    <span class="text-gray-400">
                                        No description
                                    </span>

                                @endif

                            </td>


                            <td class="whitespace-nowrap
                                       px-6 py-4">

                                @if ($section->is_active)

                                    <span class="inline-flex
                                                 rounded-full
                                                 bg-green-100
                                                 px-3 py-1
                                                 text-xs
                                                 font-semibold
                                                 text-green-700">
                                        Active
                                    </span>

                                @else

                                    <span class="inline-flex
                                                 rounded-full
                                                 bg-red-100
                                                 px-3 py-1
                                                 text-xs
                                                 font-semibold
                                                 text-red-700">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-gray-500">

                                {{ $section->created_at?->format(
                                    'd M Y'
                                ) }}

                            </td>


                            <td class="whitespace-nowrap
                                       px-6 py-4 text-right">

                                <div class="flex justify-end
                                            gap-2">

                                    @if (
                                        auth()->user()
                                            ->hasPermission(
                                                'sections.edit'
                                            )
                                    )

                                        <a
                                            href="{{ route(
                                                'admin.sections.edit',
                                                $section
                                            ) }}"
                                            class="rounded-lg
                                                   bg-amber-500
                                                   px-3 py-2
                                                   text-xs
                                                   font-semibold
                                                   text-white
                                                   hover:bg-amber-600"
                                        >
                                            Edit
                                        </a>

                                    @endif


                                    @if (
                                        auth()->user()
                                            ->hasPermission(
                                                'sections.delete'
                                            )
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.sections.destroy',
                                                $section
                                            ) }}"
                                            onsubmit="return confirm(
                                                'Are you sure you want to delete this section?'
                                            );"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg
                                                       bg-red-600
                                                       px-3 py-2
                                                       text-xs
                                                       font-semibold
                                                       text-white
                                                       hover:bg-red-700"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12
                                       text-center text-sm
                                       text-gray-500"
                            >
                                No sections have been created.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($sections->hasPages())

            <div class="border-t border-gray-200
                        px-6 py-4">

                {{ $sections->links() }}

            </div>

        @endif

    </div>

@endsection
