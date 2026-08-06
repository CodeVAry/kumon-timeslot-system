@extends('layouts.admin')

@section('title', 'Student Statuses')

@section('page-title', 'Student Status Management')

@php
    $breadcrumbs = [
        [
            'label' => 'Student Setup',
            'url' => null,
        ],
        [
            'label' => 'Student Statuses',
            'url' => null,
        ],
    ];
@endphp

@section('content')

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-green-200
                    bg-green-50 px-4 py-3
                    text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200
                    bg-red-50 px-4 py-3
                    text-sm text-red-700">
            {{ session('error') }}
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
                    Student Statuses
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage student status names and colours.
                </p>
            </div>

            @if (
                auth()->user()
                    ->hasPermission(
                        'student_statuses.create'
                    )
            )
                <a
                    href="{{ route(
                        'admin.student-statuses.create'
                    ) }}"
                    class="inline-flex items-center
                           justify-center rounded-lg
                           bg-blue-600 px-4 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700"
                >
                    + Create Status
                </a>
            @endif

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Colour
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Description
                        </th>

                        <th class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Availability
                        </th>

                        <th class="px-6 py-3 text-right text-xs
                                   font-semibold uppercase
                                   tracking-wider text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse (
                        $studentStatuses as $studentStatus
                    )

                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-6 py-4">

                                <span
                                    class="inline-flex rounded-full
                                           px-3 py-1 text-xs
                                           font-semibold text-white"
                                    style="background-color:
                                        {{ $studentStatus->color_code }};"
                                >
                                    {{ $studentStatus->status_name }}
                                </span>

                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <span
                                        class="h-7 w-7 rounded-full
                                               border border-gray-300"
                                        style="background-color:
                                            {{ $studentStatus->color_code }};"
                                    ></span>

                                    <span class="text-sm text-gray-600">
                                        {{ $studentStatus->color_code }}
                                    </span>

                                </div>

                            </td>

                            <td class="max-w-md px-6 py-4
                                       text-sm text-gray-600">

                                @if ($studentStatus->description)
                                    {{ $studentStatus->description }}
                                @else
                                    <span class="text-gray-400">
                                        No description
                                    </span>
                                @endif

                            </td>

                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($studentStatus->is_active)
                                    <span class="rounded-full bg-green-100
                                                 px-3 py-1 text-xs
                                                 font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100
                                                 px-3 py-1 text-xs
                                                 font-semibold text-red-700">
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
                                                'student_statuses.edit'
                                            )
                                    )
                                        <a
                                            href="{{ route(
                                                'admin.student-statuses.edit',
                                                $studentStatus
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
                                                'student_statuses.delete'
                                            )
                                    )
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.student-statuses.destroy',
                                                $studentStatus
                                            ) }}"
                                            onsubmit="return confirm(
                                                'Delete this student status?'
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
                                colspan="5"
                                class="px-6 py-12 text-center
                                       text-sm text-gray-500"
                            >
                                No student statuses have been created.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($studentStatuses->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $studentStatuses->links() }}
            </div>
        @endif

    </div>

@endsection
