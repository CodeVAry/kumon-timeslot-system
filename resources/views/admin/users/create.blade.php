@extends('layouts.admin')

@section('title', 'Create User')

@section('page-title', 'Create User')

@php
    $breadcrumbs = [
        [
            'label' => 'Users',
            'url' => route('admin.users.index'),
        ],
        [
            'label' => 'Create User',
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
                    Create New User
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Enter the user's details and optionally assign a role.
                </p>

            </div>

            <form
                method="POST"
                action="{{ route('admin.users.store') }}"
            >
                @csrf

                <div class="space-y-6 p-6">

                    {{-- Name --}}
                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-semibold
                                   text-gray-700"
                        >
                            Name
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold
                                   text-gray-700"
                        >
                            Email
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            maxlength="255"
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label
                            for="phone"
                            class="mb-2 block text-sm font-semibold
                                   text-gray-700"
                        >
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            id="phone"
                            value="{{ old('phone') }}"
                            maxlength="30"
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label
                            for="role_id"
                            class="mb-2 block text-sm font-semibold
                                   text-gray-700"
                        >
                            Role
                        </label>

                        <select
                            name="role_id"
                            id="role_id"
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >
                            <option value="">
                                Not Assigned
                            </option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @selected(
                                        old('role_id') == $role->id
                                    )
                                >
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-1 text-sm text-gray-500">
                            A user without a role cannot access system modules.
                        </p>

                        @error('role_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold
                                   text-gray-700"
                        >
                            Password
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                        @error('password')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label
                            for="password_confirmation"
                            class="mb-2 block text-sm font-semibold
                                   text-gray-700"
                        >
                            Confirm Password
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            class="w-full rounded-lg border-gray-300
                                   shadow-sm focus:border-blue-500
                                   focus:ring-blue-500"
                        >
                    </div>

                    {{-- Active status --}}
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
                                       text-blue-600
                                       focus:ring-blue-500"
                                @checked(old('is_active', true))
                            >

                            <span class="text-sm font-semibold
                                         text-gray-700">
                                Active User
                            </span>

                        </label>

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
                        href="{{ route('admin.users.index') }}"
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
                        Save User
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
