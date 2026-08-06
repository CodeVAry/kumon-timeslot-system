@extends('layouts.admin')

@section('title', 'Section Offerings')

@section('page-title', 'Section Offering Management')

@php
    $breadcrumbs = [
        [
            'label' => 'Class Setup',
            'url' => null,
        ],
        [
            'label' => 'Section Offerings',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-green-200
                    bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200
                bg-white shadow-sm">

        <div class="flex flex-col gap-4 border-b
                    border-gray-200 p-6
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Section Offerings
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage section days, times and seat capacities.
                </p>
            </div>

            @if (
                auth()->user()
                    ->hasPermission(
                        'section_offerings.create'
                    )
            )
                <a
                    href="{{ route(
                        'admin.section-offerings.create'
                    ) }}"
                    class="inline-flex items-center
                           justify-center rounded-lg
                           bg-blue-600 px-4 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700"
                >
                    + Create Section Offering
                </a>
            @endif

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y
                          divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Day
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Section
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Time
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Duration
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Max Seats
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200
                              bg-white">

                    @forelse (
                        $sectionOfferings as $sectionOffering
                    )

                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       font-semibold text-gray-800">
                                {{ $sectionOffering->day->day_name }}
                            </td>

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-gray-700">
                                {{ $sectionOffering->section->section_name }}
                            </td>

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-gray-700">
                                {{ \Carbon\Carbon::createFromFormat(
                                    'H:i:s',
                                    $sectionOffering->start_time
                                )->format('g:i A') }}

                                -

                                {{ \Carbon\Carbon::createFromFormat(
                                    'H:i:s',
                                    $sectionOffering->end_time
                                )->format('g:i A') }}
                            </td>

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-gray-600">
                                {{ $sectionOffering->duration_minutes }}
                                minutes
                            </td>

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-gray-600">
                                {{ $sectionOffering->max_seats }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($sectionOffering->is_active)
                                    <span class="inline-flex rounded-full
                                                 bg-green-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full
                                                 bg-red-100 px-3 py-1
                                                 text-xs font-semibold
                                                 text-red-700">
                                        Inactive
                                    </span>
                                @endif

                            </td>

                            <td class="whitespace-nowrap
                                       px-6 py-4 text-right">

                                <div class="flex justify-end gap-2">

                                    @if (
                                        auth()->user()
                                            ->hasPermission(
                                                'section_offerings.edit'
                                            )
                                    )
                                        <a
                                            href="{{ route(
                                                'admin.section-offerings.edit',
                                                $sectionOffering
                                            ) }}"
                                            class="rounded-lg bg-amber-500
                                                   px-3 py-2 text-xs
                                                   font-semibold text-white
                                                   hover:bg-amber-600"
                                        >
                                            Edit
                                        </a>
                                    @endif

                                    @if (
                                        auth()->user()
                                            ->hasPermission(
                                                'section_offerings.delete'
                                            )
                                    )
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.section-offerings.destroy',
                                                $sectionOffering
                                            ) }}"
                                            onsubmit="return confirm(
                                                'Delete this section offering?'
                                            );"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-red-600
                                                       px-3 py-2 text-xs
                                                       font-semibold text-white
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
                                colspan="7"
                                class="px-6 py-12 text-center
                                       text-sm text-gray-500"
                            >
                                No section offerings have been created.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($sectionOfferings->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $sectionOfferings->links() }}
            </div>
        @endif

    </div>

@endsection
