<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Kumon Centre Schedule
    </title>


    <style>

        @page {
            size: A4 landscape;
            margin: 14px;
        }


        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 11px;
        }


        .title {
            margin: 0;
            padding: 8px;
            background: #d9ead3;
            border: 1px solid #000;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }


        .day-title {
            margin-top: 8px;
            padding: 7px;
            background: #e2f0d9;
            border: 1px solid #000;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }


        .schedule-block {
            width: 100%;
            margin-top: 8px;
        }


        .page-break {
            page-break-before: always;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        thead {
            display: table-header-group;
        }


        tr {
            page-break-inside: avoid;
        }


        th,
        td {
            border: 1px solid #000;
        }


        th {
            padding: 6px;
            background: #f3f4f6;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }


        .time-column {
            width: 10%;
        }


        .detail-column {
            width: 90%;
        }


        .time-cell {
            padding: 0;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }


        .time-text {
            display: inline-block;
            white-space: nowrap;
            font-size: 12px;
            font-weight: bold;
            transform: rotate(-90deg);
            transform-origin: center center;
        }


        .subject-row {
            padding: 6px 8px;
            background: #eaf2f8;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }


        .student-row {
            padding: 5px 10px;
            font-size: 11px;
            text-align: left;
            vertical-align: middle;
        }


        .student-highlight {
            font-weight: bold;
        }


        .empty {
            padding: 40px;
            border: 1px solid #000;
            text-align: center;
            color: #64748b;
        }

    </style>

</head>


<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Normalise Single-Day + All-Days Into One Collection
    |--------------------------------------------------------------------------
    */

    if (
        isset(
            $multiDay
        )
        &&
        $multiDay
    ) {

        $schedules =
            $daySchedules;

    } else {

        $schedules =
            collect([
                [
                    'date' =>
                        $date,

                    'rows' =>
                        $rows,

                    'timeGroups' =>
                        $timeGroups,
                ],
            ]);
    }


    $maxDetailRowsPerChunk =
        20;


    $documentChunkIndex =
        0;

@endphp


@foreach (
    $schedules
    as $schedule
)

    @php

        $scheduleRows =
            $schedule[
                'rows'
            ];


        $scheduleDate =
            $schedule[
                'date'
            ];


        $scheduleTimeGroups =
            $schedule[
                'timeGroups'
            ];


        $printChunks =
            collect();


        foreach (
            $scheduleTimeGroups
            as $time =>
                $timeRows
        ) {

            $subjectGroups =
                $timeRows
                    ->groupBy(
                        'class_display'
                    );


            $currentChunk = [];
            $currentChunkRows = 0;
            $chunkNumber = 1;


            foreach (
                $subjectGroups
                as $subjectName =>
                    $subjectRows
            ) {

                $subjectRows =
                    $subjectRows
                        ->values();


                $studentsPerSubjectPart =
                    max(
                        1,
                        $maxDetailRowsPerChunk
                        -
                        1
                    );


                foreach (
                    $subjectRows
                        ->chunk(
                            $studentsPerSubjectPart
                        )
                    as $partIndex =>
                        $subjectPartRows
                ) {

                    $partSubjectName =
                        $subjectName;


                    if (
                        $partIndex
                        >
                        0
                    ) {

                        $partSubjectName .=
                            ' (continued)';
                    }


                    $neededRows =
                        1
                        +
                        $subjectPartRows
                            ->count();


                    if (
                        $currentChunkRows
                        >
                        0
                        &&
                        (
                            $currentChunkRows
                            +
                            $neededRows
                        )
                        >
                        $maxDetailRowsPerChunk
                    ) {

                        $printChunks->push([
                            'time' =>
                                $time,

                            'chunk_number' =>
                                $chunkNumber,

                            'subjects' =>
                                collect(
                                    $currentChunk
                                ),

                            'row_count' =>
                                $currentChunkRows,
                        ]);


                        $chunkNumber++;

                        $currentChunk = [];

                        $currentChunkRows = 0;
                    }


                    $currentChunk[] = [
                        'subject_name' =>
                            $partSubjectName,

                        'rows' =>
                            $subjectPartRows,
                    ];


                    $currentChunkRows +=
                        $neededRows;
                }
            }


            if (
                $currentChunkRows
                >
                0
            ) {

                $printChunks->push([
                    'time' =>
                        $time,

                    'chunk_number' =>
                        $chunkNumber,

                    'subjects' =>
                        collect(
                            $currentChunk
                        ),

                    'row_count' =>
                        $currentChunkRows,
                ]);
            }
        }

    @endphp


    @if (
        $scheduleRows
            ->isEmpty()
    )

        @continue

    @endif


    @foreach (
        $printChunks
        as $chunk
    )

        <div
            class="{{
                $documentChunkIndex
                >
                0
                    ? 'page-break'
                    : ''
            }}"
        >

            <h1 class="title">
                Kumon North Hobart Centre Schedule
            </h1>


            <div class="day-title">

                {{
                    $scheduleDate->format(
                        'l, d F Y'
                    )
                }}

                @if (
                    $chunk[
                        'chunk_number'
                    ]
                    >
                    1
                )

                    — continued

                @endif

            </div>


            <div class="schedule-block">

                <table>

                    <thead>

                        <tr>

                            <th class="time-column">
                                Time
                            </th>

                            <th class="detail-column">
                                Subject / Student
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @php

                            $firstOutputRow =
                                true;


                            $timeLabel =
                                $chunk[
                                    'time'
                                ];


                            if (
                                $chunk[
                                    'chunk_number'
                                ]
                                >
                                1
                            ) {

                                $timeLabel .=
                                    ' cont.';
                            }

                        @endphp


                        @foreach (
                            $chunk[
                                'subjects'
                            ]
                            as $subjectBlock
                        )

                            <tr>

                                @if (
                                    $firstOutputRow
                                )

                                    <td
                                        class="time-cell"
                                        rowspan="{{
                                            $chunk[
                                                'row_count'
                                            ]
                                        }}"
                                    >

                                        <div class="time-text">
                                            {{ $timeLabel }}
                                        </div>

                                    </td>


                                    @php
                                        $firstOutputRow = false;
                                    @endphp

                                @endif


                                <td class="subject-row">

                                    {{
                                        $subjectBlock[
                                            'subject_name'
                                        ]
                                    }}

                                </td>

                            </tr>


                            @foreach (
                                $subjectBlock[
                                    'rows'
                                ]
                                as $row
                            )

                                @php

                                    $fill =
                                        $row[
                                            'status_fill'
                                        ];


                                    $textColour =
                                        '#111827';


                                    if ($fill) {

                                        $hex =
                                            ltrim(
                                                $fill,
                                                '#'
                                            );


                                        if (
                                            strlen(
                                                $hex
                                            )
                                            ===
                                            6
                                        ) {

                                            $r =
                                                hexdec(
                                                    substr(
                                                        $hex,
                                                        0,
                                                        2
                                                    )
                                                );


                                            $g =
                                                hexdec(
                                                    substr(
                                                        $hex,
                                                        2,
                                                        2
                                                    )
                                                );


                                            $b =
                                                hexdec(
                                                    substr(
                                                        $hex,
                                                        4,
                                                        2
                                                    )
                                                );


                                            $brightness =
                                                (
                                                    $r * 299
                                                    +
                                                    $g * 587
                                                    +
                                                    $b * 114
                                                )
                                                /
                                                1000;


                                            if (
                                                $brightness
                                                <
                                                150
                                            ) {

                                                $textColour =
                                                    '#ffffff';
                                            }
                                        }
                                    }

                                @endphp


                                <tr>

                                    <td
                                        class="
                                            student-row

                                            {{
                                                $fill
                                                    ? 'student-highlight'
                                                    : ''
                                            }}
                                        "

                                        @if ($fill)

                                            style="
                                                background-color:
                                                {{ $fill }};

                                                color:
                                                {{ $textColour }};
                                            "

                                        @endif
                                    >

                                        {{
                                            $row[
                                                'student_name'
                                            ]
                                        }}

                                    </td>

                                </tr>

                            @endforeach

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        @php
            $documentChunkIndex++;
        @endphp

    @endforeach

@endforeach


@if (
    $documentChunkIndex
    ===
    0
)

    <h1 class="title">
        Kumon North Hobart Centre Schedule
    </h1>


    <div class="empty">
        No students matched the selected filters.
    </div>

@endif


</body>

</html>
