<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Schedule List</title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        body { margin: 0; background: #ffffff; font-family: DejaVu Sans, sans-serif; color: #182338; font-size: 9px; }
        .sheet { border: 1px solid #cbd5e1; background: #ffffff; padding: 12px; }
        .next-sheet { page-break-before: always; }
        .masthead { width: 100%; border-collapse: collapse; background: #ffffff; color: #182338; }
        .masthead td { padding: 8px 6px; vertical-align: middle; }
        .brand { width: 70%; font-size: 16px; font-weight: bold; }
        .brand img { display: block; max-width: 115px; max-height: 35px; margin-bottom: 3px; background: #ffffff; }
        .centre { display: block; margin-top: 3px; font-size: 8px; font-weight: normal; }
        .printed { text-align: right; font-size: 8px; line-height: 1.5; }
        h1 { margin: 0; padding: 12px 10px; color: #182338; text-align: center; font-size: 15px; }
        .panel { background: #ffffff; color: #182338; padding: 10px; }
        .date-bar { margin: 0 0 8px; padding: 8px 10px; background: #eff6fa; color: #182338; font-size: 9px; border-left: 3px solid #0891b2; }
        .date-bar strong { color: #182338; }
        .date-bar .count { float: right; font-size: 8px; color: #475569; }
        /* The printable list is intentionally one full-width column. */
        .list { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 0 3px; }
        .list td { width: 100%; padding: 5px 8px; vertical-align: middle; }
        .list td.group { background: #e4eff5; color: #164e63; font-weight: bold; font-size: 8px; }
        .list td.name { background: #ffffff; color: #182338; border: 1px solid #e2e8f0; font-size: 9px; }
        .list td.blank { background: #ffffff; }
        .small-count { float: right; font-weight: normal; }
        .footer { margin-top: 8px; padding: 5px 2px 0; font-size: 8px; color: #64748b; text-align: right; }
        .none { padding: 20px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
@php
    $schedules = !empty($multiDay)
        ? $daySchedules
        : collect([['date' => $date, 'rows' => $rows]]);

    $logoPath = public_path('kumon-logo.png');
    if (!is_file($logoPath)) {
        $logoPath = public_path('images/kumon-logo.png');
    }

    $firstSheet = true;
@endphp

@forelse ($schedules as $schedule)
    @php
        $exportRows = collect($schedule['rows'])->values();
        $lines = collect();

        foreach ($exportRows->groupBy(function ($row) {
            return ($row['start_time'] ?? '') . '|' . ($row['class_display'] ?? $row['section'] ?? '');
        }) as $group) {
            $first = $group->first();
            $time = $first['start_time'] ?? '';
            $subject = $first['class_display'] ?? $first['section'] ?? '';

            $lines->push([
                'type' => 'group',
                'time' => $time,
                'subject' => $subject,
                'count' => $group->count(),
            ]);

            foreach ($group as $row) {
                $lines->push([
                    'type' => 'student',
                    'time' => $time,
                    'subject' => $subject,
                    'row' => $row,
                ]);
            }
        }

        // Leave enough room for the headings and footer on an A4 portrait page.
        // Each page now uses one full-width column.
        $pages = $lines->chunk(44);
        if ($pages->isEmpty()) {
            $pages = collect([collect()]);
        }
    @endphp

    @foreach ($pages as $pageIndex => $pageLines)
        @php
            $pageLines = $pageLines->values();

            // If a page starts in the middle of a class, show a continuation
            // heading so the single-column printout remains easy to follow.
            if ($pageLines->isNotEmpty() && $pageLines->first()['type'] === 'student') {
                $firstLine = $pageLines->first();
                $pageLines->prepend([
                    'type' => 'group',
                    'time' => $firstLine['time'],
                    'subject' => $firstLine['subject'] . ' (continued)',
                    'count' => null,
                ]);
            }

            $visibleLines = $pageLines->count();
        @endphp

        <div class="sheet @if (!$firstSheet) next-sheet @endif">
            <table class="masthead">
                <tr>
                    <td class="brand">
                        @if (is_file($logoPath))
                            <img src="{{ $logoPath }}" alt="Kumon logo">
                        @else
                            KUMON
                        @endif
                        <span class="centre">Kumon North Hobart Education Centre</span>
                    </td>
                    <td class="printed">
                        Printed {{ now('Australia/Hobart')->format('d M Y') }}<br>
                        Page {{ $pageIndex + 1 }} of {{ $pages->count() }}
                    </td>
                </tr>
            </table>

            <h1>Student Schedule List</h1>

            <div class="panel">
            <div class="date-bar">
                <span><strong>Schedule day &amp; date:</strong>
                    {{ \Carbon\Carbon::parse($schedule['date'])->format('l, d F Y') }}
                </span>
                <span class="count">{{ $exportRows->count() }} class allocations</span>
            </div>

            @if ($exportRows->isEmpty())
                <div class="none">No students matched the selected filters.</div>
            @else
                <table class="list">
                    <tbody>
                        @for ($index = 0; $index < $visibleLines; $index++)
                            <tr>
                                @php $line = $pageLines->get($index); @endphp
                                @if ($line['type'] === 'group')
                                    <td class="group">
                                        {{ $line['time'] }} &nbsp;·&nbsp; {{ $line['subject'] }}
                                        @if ($line['count'] !== null)
                                            <span class="small-count">{{ $line['count'] }}</span>
                                        @endif
                                    </td>
                                @else
                                    @php
                                        $student = $line['row'];
                                        $attendance = $student['attendance_status'] ?? null;
                                        $fill = $attendance === 'vacation' ? '#9ca3af'
                                            : ($attendance === 'absent' ? '#dc2626' : ($student['status_fill'] ?? null));
                                        $textColor = $attendance === 'absent' ? '#ffffff' : '#182338';
                                        if ($fill && $attendance !== 'absent' && preg_match('/^#[0-9a-fA-F]{6}$/', $fill)) {
                                            $r = hexdec(substr($fill, 1, 2));
                                            $g = hexdec(substr($fill, 3, 2));
                                            $b = hexdec(substr($fill, 5, 2));
                                            if (($r * 299 + $g * 587 + $b * 114) / 1000 < 150) {
                                                $textColor = '#ffffff';
                                            }
                                        }
                                    @endphp
                                    <td class="name" @if ($fill) style="background: {{ $fill }}; color: {{ $textColor }};" @endif>
                                        {{ $student['student_name'] ?? '' }}
                                    </td>
                                @endif
                            </tr>
                        @endfor
                    </tbody>
                </table>
            @endif
            </div>

            <div class="footer">Kumon North Hobart · Student Schedule List</div>
        </div>

        @php $firstSheet = false; @endphp
    @endforeach
@empty
    <div class="sheet">
        <h1>Student Schedule List</h1>
        <div class="panel"><div class="none">No students matched the selected filters.</div></div>
    </div>
@endforelse
</body>
</html>
