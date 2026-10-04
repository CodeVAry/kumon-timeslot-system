@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
            <p class="mt-1 text-sm text-slate-500">
                View your account details and update your contact information or password.
            </p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                Profile details updated successfully.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                Password updated successfully.
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white">
                    {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                    <p class="mt-1 text-sm font-semibold text-blue-700">
                        {{ $user->role?->role_name ?? 'Position not assigned' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @include('profile.partials.update-password-form')
        </div>
    </div>
@endsection
