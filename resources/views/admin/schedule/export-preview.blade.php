@php
    // The admin layout also reads this variable on schedule pages.
    $selectedDay = $selectedDay ?? null;
@endphp

@extends('layouts.admin')

@section('title', 'Schedule Preview')
@section('page-title', 'Schedule Preview')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Schedule Preview</h1>
            <p class="mt-1 text-sm text-slate-500">
                Check the class list. A file will download only when you choose a format.
            </p>
        </div>
        <a href="{{ route('admin.schedule.export.form', array_filter([
                'day_id' => $filters['day_id'] ?? null,
                'date' => $filters['date'] ?? null,
            ])) }}"
           class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
            Change Filters
        </a>
    </div>

    @php
        // Accept both collections and arrays supplied by schedule export controllers.
        $daySchedules = collect($daySchedules ?? [])->map(function ($schedule) {
            $schedule['rows'] = collect($schedule['rows'] ?? []);

            return $schedule;
        });

        $totalAllocations = $daySchedules->sum(fn ($day) => $day['rows']->count());
    @endphp

    <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-900">
        {{ $daySchedules->count() }} day(s) · {{ $totalAllocations }} class allocations
        @if ($totalAllocations === 0)
            — No students matched the selected filters.
        @endif
    </div>

    @if ($totalAllocations > 0)
    <div class="flex flex-wrap gap-3">
        @foreach (['pdf' => 'Download PDF', 'excel' => 'Download Excel'] as $format => $label)
            <form method="POST" action="{{ route('admin.schedule.export') }}">
                @csrf
                <input type="hidden" name="download" value="1">
                @foreach ([
                    'date', 'day_id', 'section_id', 'sub_section_id',
                    'time', 'offering_id', 'attendance_status',
                    'include_attendance_status',
                ] as $key)
                    @if (isset($filters[$key]) && $filters[$key] !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}">
                    @endif
                @endforeach
                <button type="submit" name="format" value="{{ $format }}"
                        class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white {{ $format === 'pdf' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }}">
                    {{ $label }}
                </button>
            </form>
        @endforeach
    </div>
    @endif

    @forelse ($daySchedules as $daySchedule)
        @php
            $previewRows = collect($daySchedule['rows']);
            $groups = $previewRows->groupBy(
                fn ($row) => ($row['start_time'] ?? '') . '|' . ($row['class_display'] ?? $row['section'] ?? '')
            );
        @endphp

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h2 class="font-bold text-slate-900">
                    {{ \Carbon\Carbon::parse($daySchedule['date'])->format('l, d F Y') }}
                </h2>
                <span class="text-sm text-slate-500">
                    {{ $previewRows->count() }} class allocations
                </span>
            </div>

            <div class="grid gap-4 p-5 md:grid-cols-2">
                @forelse ($groups as $group)
                    @php
                        $first = $group->first();
                        $className = $first['class_display'] ?? $first['section'] ?? 'Class';
                    @endphp
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <div class="flex justify-between gap-3 bg-sky-50 px-4 py-3 text-sm font-bold text-slate-800">
                            <span>{{ $first['start_time'] ?? '' }} · {{ $className }}</span>
                            <span class="text-slate-500">{{ $group->count() }}</span>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($group as $row)
                                @php
                                    $status = $row['attendance_status'] ?? null;
                                @endphp
                                <li class="px-4 py-2 text-sm text-slate-800
                                           {{ $status === 'absent' ? 'bg-red-50' : ($status === 'vacation' ? 'bg-slate-100' : '') }}">
                                    {{ $row['student_name'] ?? '' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No students for this day.</p>
                @endforelse
            </div>
        </section>
    @empty
        <section class="rounded-2xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
            No students matched the selected filters.
        </section>
    @endforelse

</div>
@endsection
