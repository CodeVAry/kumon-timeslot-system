<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Kumon Centre Schedule
    </title>


    <style>

        @page {
            size: A4 portrait;
            margin: 12px;
        }


        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
        }


        .page {
            width: 100%;
            page-break-after: always;
        }


        .page:last-child {
            page-break-after: auto;
        }


        .title {
            margin: 0;
            padding: 6px;
            border: 1px solid #000;
            background: #d9ead3;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }


        .day-title {
            margin: 4px 0 6px;
            padding: 5px;
            border: 1px solid #000;
            background: #e2f0d9;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }


        .columns {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        .columns > tbody > tr > td {
            border: 0;
            vertical-align: top;
        }


        .panel {
            width: 49%;
        }


        .gap {
            width: 2%;
        }


        .schedule {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        .schedule th,
        .schedule td {
            border: 1px solid #000;
        }


        .schedule th {
            padding: 3px 2px;
            background: #f3f4f6;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
        }


        .time-column {
            width: 15%;
        }


        .detail-column {
            width: 85%;
        }


        .time-cell {
            padding: 0;
            text-align: center;
            vertical-align: middle;
        }


        .time-text {
            display: inline-block;
            white-space: nowrap;
            font-weight: bold;
            transform: rotate(-90deg);
            transform-origin: center center;
        }


        .subject-row {
            padding: 2px 3px;
            background: #eaf2f8;
            font-weight: bold;
            text-align: center;
        }


        .student-row {
            padding: 1.5px 4px;
            line-height: 1.1;
            text-align: left;
        }


        .student-highlight {
            font-weight: bold;
        }


        .empty {
            padding: 30px;
            border: 1px solid #000;
            color: #64748b;
            text-align: center;
            font-size: 10px;
        }

    </style>

</head>


<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Single Day + All Days
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
                ],
            ]);
    }

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


        /*
        |--------------------------------------------------------------------------
        | Build Blocks
        |--------------------------------------------------------------------------
        */

        $blocks =
            collect();


        $timeGroups =
            $scheduleRows
                ->groupBy(
                    'start_time'
                );


        foreach (
            $timeGroups
            as $time =>
                $timeRows
        ) {

            $subjectGroups =
                $timeRows
                    ->groupBy(
                        'class_display'
                    );


            foreach (
                $subjectGroups
                as $subjectName =>
                    $subjectRows
            ) {

                $blocks->push([
                    'time' =>
                        $time,

                    'subject_name' =>
                        $subjectName,

                    'rows' =>
                        $subjectRows
                            ->values(),

                    'height' =>
                        1
                        +
                        $subjectRows
                            ->count(),
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Split Into Two Balanced Columns
        |--------------------------------------------------------------------------
        */

        $totalHeight =
            $blocks
                ->sum(
                    'height'
                );


        $targetHeight =
            max(
                1,
                (int)
                ceil(
                    $totalHeight
                    /
                    2
                )
            );


        $leftBlocks =
            collect();


        $rightBlocks =
            collect();


        $leftHeight =
            0;


        foreach (
            $blocks
            as $block
        ) {

            if (
                $leftHeight
                <
                $targetHeight
            ) {

                $leftBlocks->push(
                    $block
                );


                $leftHeight +=
                    $block[
                        'height'
                    ];

            } else {

                $rightBlocks->push(
                    $block
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Dynamic Compact Font
        |--------------------------------------------------------------------------
        */

        $rightHeight =
            $rightBlocks
                ->sum(
                    'height'
                );


        $maxColumnRows =
            max(
                $leftHeight,
                $rightHeight
            );


        if (
            $maxColumnRows
            >
            58
        ) {

            $studentFontSize =
                5.4;

            $subjectFontSize =
                5.8;

            $timeFontSize =
                5.5;

        } elseif (
            $maxColumnRows
            >
            48
        ) {

            $studentFontSize =
                6.0;

            $subjectFontSize =
                6.4;

            $timeFontSize =
                6.0;

        } elseif (
            $maxColumnRows
            >
            40
        ) {

            $studentFontSize =
                6.6;

            $subjectFontSize =
                7.0;

            $timeFontSize =
                6.5;

        } else {

            $studentFontSize =
                7.2;

            $subjectFontSize =
                7.6;

            $timeFontSize =
                7.0;
        }

    @endphp


    <div class="page">

        <h1 class="title">
            Kumon North Hobart Centre Schedule
        </h1>


        <div class="day-title">
            {{
                $scheduleDate->format(
                    'l, d F Y'
                )
            }}
        </div>


        @if (
            $scheduleRows
                ->isEmpty()
        )

            <div class="empty">
                No students matched the selected filters.
            </div>

        @else

            <table class="columns">

                <tbody>

                    <tr>

                        <td class="panel">

                            @include(
                                'admin.schedule.partials.two-column-panel',
                                [
                                    'panelBlocks' =>
                                        $leftBlocks,

                                    'studentFontSize' =>
                                        $studentFontSize,

                                    'subjectFontSize' =>
                                        $subjectFontSize,

                                    'timeFontSize' =>
                                        $timeFontSize,
                                ]
                            )

                        </td>


                        <td class="gap"></td>


                        <td class="panel">

                            @include(
                                'admin.schedule.partials.two-column-panel',
                                [
                                    'panelBlocks' =>
                                        $rightBlocks,

                                    'studentFontSize' =>
                                        $studentFontSize,

                                    'subjectFontSize' =>
                                        $subjectFontSize,

                                    'timeFontSize' =>
                                        $timeFontSize,
                                ]
                            )

                        </td>

                    </tr>

                </tbody>

            </table>

        @endif

    </div>

@endforeach


</body>

</html>
