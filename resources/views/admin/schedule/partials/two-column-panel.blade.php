<table class="schedule">

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
            $panelBlocks
            as $block
        )

            @php

                $blockRows =
                    $block[
                        'rows'
                    ];


                $rowspan =
                    1
                    +
                    $blockRows
                        ->count();

            @endphp


            <tr>

                <td
                    class="time-cell"
                    rowspan="{{ $rowspan }}"
                >

                    <div
                        class="time-text"
                        style="
                            font-size:
                            {{ $timeFontSize }}px;
                        "
                    >
                        {{
                            \Carbon\Carbon::parse(
                                $block[
                                    'time'
                                ]
                            )->format(
                                'g:i A'
                            )
                        }}
                    </div>

                </td>


                <td
                    class="subject-row"
                    style="
                        font-size:
                        {{ $subjectFontSize }}px;
                    "
                >
                    {{
                        $block[
                            'subject_name'
                        ]
                    }}
                </td>

            </tr>


            @foreach (
                $blockRows
                as $row
            )

                @php

                    $attendanceStatus =
                        $row[
                            'attendance_status'
                        ]
                        ??
                        null;


                    if (
                        $attendanceStatus
                        ===
                        'vacation'
                    ) {

                        $rowFill =
                            '#9ca3af';

                        $rowTextColour =
                            '#111827';

                    } elseif (
                        $attendanceStatus
                        ===
                        'absent'
                    ) {

                        $rowFill =
                            '#dc2626';

                        $rowTextColour =
                            '#ffffff';

                    } else {

                        $rowFill =
                            $row[
                                'status_fill'
                            ]
                            ??
                            null;


                        $rowTextColour =
                            '#111827';


                        if ($rowFill) {

                            $hex =
                                ltrim(
                                    $rowFill,
                                    '#'
                                );


                            if (
                                strlen(
                                    $hex
                                )
                                ===
                                6
                            ) {

                                $red =
                                    hexdec(
                                        substr(
                                            $hex,
                                            0,
                                            2
                                        )
                                    );


                                $green =
                                    hexdec(
                                        substr(
                                            $hex,
                                            2,
                                            2
                                        )
                                    );


                                $blue =
                                    hexdec(
                                        substr(
                                            $hex,
                                            4,
                                            2
                                        )
                                    );


                                $brightness =
                                    (
                                        $red * 299
                                        +
                                        $green * 587
                                        +
                                        $blue * 114
                                    )
                                    /
                                    1000;


                                if (
                                    $brightness
                                    <
                                    150
                                ) {

                                    $rowTextColour =
                                        '#ffffff';
                                }
                            }
                        }
                    }

                @endphp


                <tr>

                    <td
                        class="
                            student-row

                            {{
                                $rowFill
                                    ? 'student-highlight'
                                    : ''
                            }}
                        "
                        style="
                            font-size:
                            {{ $studentFontSize }}px;

                            @if ($rowFill)
                                background-color:
                                {{ $rowFill }};

                                color:
                                {{ $rowTextColour }};
                            @endif
                        "
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
