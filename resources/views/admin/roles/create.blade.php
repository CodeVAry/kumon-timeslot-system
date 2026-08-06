@extends('layouts.admin')

@section('title', 'Create Role')

@section('page-title', 'Create Role')

@php
    $breadcrumbs = [
        [
            'label' => 'Roles',
            'url' => route('admin.roles.index'),
        ],
        [
            'label' => 'Create Role',
            'url' => null,
        ],
    ];
@endphp

@section('content')

<div class="mx-auto max-w-3xl">

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-800">
                Create New Role
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Enter the details for the new system role.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('admin.roles.store') }}"
        >
            @csrf

            <div class="space-y-6 p-6">

                {{-- Role name --}}
                <div>
                    <label
                        for="role_name"
                        class="mb-2 block text-sm font-semibold text-gray-700"
                    >
                        Role Name
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="role_name"
                        id="role_name"
                        value="{{ old('role_name') }}"
                        maxlength="100"
                        required
                        autofocus
                        placeholder="For example: Centre Manager"
                        class="w-full rounded-lg border-gray-300 shadow-sm
                               focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('role_name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label
                        for="description"
                        class="mb-2 block text-sm font-semibold text-gray-700"
                    >
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        maxlength="500"
                        placeholder="Enter the role description"
                        class="w-full rounded-lg border-gray-300 shadow-sm
                               focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status --}}
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
                            class="rounded border-gray-300 text-blue-600
                                   shadow-sm focus:ring-blue-500"
                            @checked(old('is_active', true))
                        >

                        <span class="text-sm font-semibold text-gray-700">
                            Active Role
                        </span>
                    </label>

                    <p class="mt-1 text-sm text-gray-500">
                        An inactive role should not be assigned to new users.
                    </p>

                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 p-6">

                <a
                    href="{{ route('admin.roles.index') }}"
                    class="rounded-lg border border-gray-300 bg-white
                           px-4 py-2.5 text-sm font-semibold text-gray-700
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
                    Save Role
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
