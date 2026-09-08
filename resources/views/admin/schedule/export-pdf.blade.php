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


        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }


        th,
        td {
            border: 1px solid #000;
        }


        th {
            padding: 7px;
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
            position: relative;
            padding: 0;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }


        /*
        |--------------------------------------------------------------------------
        | Vertical Time
        |--------------------------------------------------------------------------
        */

        .time-text {
            display: inline-block;
            white-space: nowrap;
            font-size: 12px;
            font-weight: bold;

            transform: rotate(-90deg);
            transform-origin: center center;
        }


        /*
        |--------------------------------------------------------------------------
        | Subject
        |--------------------------------------------------------------------------
        */

        .subject-row {
            padding: 7px 8px;
            background: #eaf2f8;

            font-size: 14px;
            font-weight: bold;

            text-align: center;
            vertical-align: middle;
        }


        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        .student-row {
            padding: 7px 12px;

            font-size: 12px;

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


    <h1 class="title">
        Kumon North Hobart Centre Schedule
    </h1>


    <div class="day-title">

        {{
            $date->format(
                'l, d F Y'
            )
        }}

    </div>



    @if ($rows->isEmpty())

        <div class="empty">

            No students matched the selected filters.

        </div>

    @else


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


                @foreach (
                    $timeGroups
                    as $time =>
                        $timeRows
                )

                    @php

                        $subjectGroups =
                            $timeRows
                                ->groupBy(
                                    'class_display'
                                );


                        /*
                        |--------------------------------------------------------------------------
                        | Calculate Complete Time Block Rows
                        |--------------------------------------------------------------------------
                        |
                        | Each subject has:
                        |
                        | 1 subject heading
                        | + student count
                        |--------------------------------------------------------------------------
                        */

                        $timeRowspan =
                            $subjectGroups
                                ->sum(
                                    function (
                                        $subjectRows
                                    ) {

                                        return
                                            1
                                            +
                                            $subjectRows
                                                ->count();
                                    }
                                );


                        $firstOutputRow =
                            true;

                    @endphp



                    @foreach (
                        $subjectGroups
                        as $subjectName =>
                            $subjectRows
                    )


                        {{-- Subject --}}

                        <tr>


                            @if ($firstOutputRow)

                                <td
                                    class="time-cell"
                                    rowspan="{{
                                        $timeRowspan
                                    }}"
                                >

                                    <div class="time-text">
                                        {{ $time }}
                                    </div>

                                </td>


                                @php
                                    $firstOutputRow = false;
                                @endphp

                            @endif


                            <td class="subject-row">

                                {{ $subjectName }}

                            </td>

                        </tr>



                        {{-- Students --}}

                        @foreach (
                            $subjectRows
                            as $row
                        )

                            @php

                                $fill =
                                    $row[
                                        'status_fill'
                                    ];


                                /*
                                |--------------------------------------------------------------------------
                                | Choose readable text colour
                                |--------------------------------------------------------------------------
                                */

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


                @endforeach


            </tbody>

        </table>


    @endif


</body>

</html>
