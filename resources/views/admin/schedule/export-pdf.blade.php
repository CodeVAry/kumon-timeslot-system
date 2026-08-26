<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Kumon Class List
    </title>


    <style>

        @page {
            margin: 20px;
        }


        body {
            margin: 0;

            font-family:
                DejaVu Sans,
                sans-serif;

            font-size: 10px;

            color: #1e293b;
        }


        .document-header {
            margin-bottom: 18px;

            padding-bottom: 12px;

            border-bottom:
                2px solid #2563eb;
        }


        .system-name {
            margin: 0;

            color: #2563eb;

            font-size: 10px;

            font-weight: bold;

            text-transform: uppercase;
        }


        .title {
            margin:
                5px 0 0 0;

            font-size: 22px;

            color: #0f172a;
        }


        .meta {
            margin-top: 7px;

            color: #64748b;

            font-size: 9px;
        }


        .summary {
            margin-bottom: 15px;

            padding: 8px 10px;

            background: #f8fafc;

            border:
                1px solid #e2e8f0;
        }


        .summary strong {
            color: #0f172a;
        }


        .class-block {
            margin-bottom: 22px;
        }


        /*
         * Separate every class when several classes
         * are exported.
         */
        .page-break {
            page-break-before: always;
        }


        .class-header {
            padding: 10px 12px;

            background: #eff6ff;

            border:
                1px solid #bfdbfe;

            border-bottom: none;
        }


        .class-name {
            margin: 0;

            color: #0f172a;

            font-size: 15px;

            font-weight: bold;
        }


        .class-meta {
            margin-top: 4px;

            color: #64748b;

            font-size: 9px;
        }


        table {
            width: 100%;

            border-collapse:
                collapse;
        }


        thead {
            display:
                table-header-group;
        }


        tr {
            page-break-inside:
                avoid;
        }


        th {
            padding: 8px;

            background: #f8fafc;

            border:
                1px solid #cbd5e1;

            color: #475569;

            text-align: left;

            font-size: 8px;

            font-weight: bold;

            text-transform: uppercase;
        }


        td {
            padding: 8px;

            border:
                1px solid #e2e8f0;

            vertical-align: top;
        }


        .student-name {
            font-weight: bold;

            color: #0f172a;
        }


        .attendance-present {
            color: #15803d;

            font-weight: bold;
        }


        .attendance-absent {
            color: #dc2626;

            font-weight: bold;
        }


        .attendance-vacation {
            color: #d97706;

            font-weight: bold;
        }


        .attendance-not-marked {
            color: #64748b;

            font-weight: bold;
        }


        .empty {
            margin-top: 20px;

            padding: 40px;

            border:
                1px solid #e2e8f0;

            background: #f8fafc;

            color: #64748b;

            text-align: center;
        }


        .footer {
            margin-top: 18px;

            padding-top: 8px;

            border-top:
                1px solid #e2e8f0;

            color: #94a3b8;

            font-size: 8px;

            text-align: right;
        }

    </style>

</head>


<body>


    {{-- =========================================================
        DOCUMENT HEADER
    ========================================================== --}}

    <div class="document-header">

        <p class="system-name">
            Kumon Timeslot System
        </p>


        <h1 class="title">
            Class List
        </h1>


        <div class="meta">

            Date:

            <strong>
                {{
                    $date->format(
                        'l, d F Y'
                    )
                }}
            </strong>

            &nbsp; | &nbsp;

            Generated:

            {{
                now()->format(
                    'd M Y, g:i A'
                )
            }}

        </div>

    </div>



    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div class="summary">

        <strong>
            Total exported student records:
        </strong>

        {{ $rows->count() }}

        &nbsp; | &nbsp;

        <strong>
            Classes:
        </strong>

        {{ $groupedRows->count() }}

        &nbsp; | &nbsp;

        <strong>
            Attendance filter:
        </strong>

        {{
            match (
                $filters[
                    'attendance_status'
                ]
            ) {

                'present' =>
                    'Present Only',

                'absent' =>
                    'Absent Only',

                'vacation' =>
                    'Vacation Only',

                'not_marked' =>
                    'Attendance Not Marked',

                default =>
                    'All Students',
            }
        }}

    </div>



    {{-- =========================================================
        EMPTY
    ========================================================== --}}

    @if ($rows->isEmpty())

        <div class="empty">

            No students matched
            the selected export filters.

        </div>


    @else


        {{-- =====================================================
            CLASSES
        ====================================================== --}}

        @foreach (
            $groupedRows
            as $offeringRows
        )

            @php

                $firstRow =
                    $offeringRows
                        ->first();

            @endphp


            <div
                class="class-block
                {{ !$loop->first ? 'page-break' : '' }}"
            >


                {{-- =============================================
                    CLASS HEADER
                ============================================== --}}

                <div class="class-header">

                    <h2 class="class-name">

                        {{
                            $firstRow[
                                'section'
                            ]
                        }}

                    </h2>


                    <div class="class-meta">

                        {{
                            $firstRow[
                                'day'
                            ]
                        }}

                        &nbsp; • &nbsp;

                        {{
                            $firstRow[
                                'start_time'
                            ]
                        }}

                        –

                        {{
                            $firstRow[
                                'end_time'
                            ]
                        }}

                        &nbsp; • &nbsp;

                        {{
                            $offeringRows
                                ->count()
                        }}

                        {{
                            $offeringRows
                                ->count()
                            ===
                            1
                                ? 'student'
                                : 'students'
                        }}

                    </div>

                </div>



                {{-- =============================================
                    STUDENT TABLE
                ============================================== --}}

                <table>

                    <thead>

                        <tr>

                            <th style="width: 4%;">
                                #
                            </th>


                            <th style="width: 13%;">
                                Student ID
                            </th>


                            <th style="width: 22%;">
                                Student
                            </th>


                            <th style="width: 22%;">
                                Guardian
                            </th>


                            <th style="width: 18%;">
                                Student Status
                            </th>


                            <th style="width: 21%;">
                                Attendance
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach (
                            $offeringRows
                            as $row
                        )

                            @php

                                $attendanceClass =
                                    match (
                                        $row[
                                            'attendance_status'
                                        ]
                                    ) {

                                        'Present' =>
                                            'attendance-present',

                                        'Absent' =>
                                            'attendance-absent',

                                        'Vacation' =>
                                            'attendance-vacation',

                                        default =>
                                            'attendance-not-marked',
                                    };

                            @endphp


                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td>
                                    {{
                                        $row[
                                            'student_id'
                                        ]
                                    }}
                                </td>


                                <td
                                    class="student-name"
                                >
                                    {{
                                        $row[
                                            'student_name'
                                        ]
                                    }}
                                </td>


                                <td>
                                    {{
                                        $row[
                                            'guardian'
                                        ]
                                    }}
                                </td>


                                <td>
                                    {{
                                        $row[
                                            'student_status'
                                        ]
                                    }}
                                </td>


                                <td
                                    class="{{
                                        $attendanceClass
                                    }}"
                                >
                                    {{
                                        $row[
                                            'attendance_status'
                                        ]
                                    }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endforeach

    @endif



    {{-- =========================================================
        FOOTER
    ========================================================== --}}

    <div class="footer">

        Generated by Kumon Timeslot System

    </div>


</body>

</html>
