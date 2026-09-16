<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ScheduleExcelExporter
{
    private const MAX_DETAIL_ROWS_PER_CHUNK = 20;


    /*
    |--------------------------------------------------------------------------
    | Single Day
    |--------------------------------------------------------------------------
    */

    public function download(
        Collection $rows,
        Carbon $date,
        string $fileName
    ) {
        $spreadsheet =
            new Spreadsheet();


        $sheet =
            $spreadsheet
                ->getActiveSheet();


        $this->buildDaySheet(
            $sheet,
            $rows,
            $date
        );


        return $this->stream(
            $spreadsheet,
            $fileName
        );
    }


    /*
    |--------------------------------------------------------------------------
    | All Days
    |--------------------------------------------------------------------------
    |
    | One worksheet is created for each working day.
    |--------------------------------------------------------------------------
    */

    public function downloadMultiDay(
        Collection $daySchedules,
        string $fileName
    ) {
        $spreadsheet =
            new Spreadsheet();


        if (
            $daySchedules
                ->isEmpty()
        ) {

            $sheet =
                $spreadsheet
                    ->getActiveSheet();


            $sheet->setTitle(
                'No Data'
            );


            $sheet->setCellValue(
                'A1',
                'No students matched the selected filters.'
            );


            return $this->stream(
                $spreadsheet,
                $fileName
            );
        }


        foreach (
            $daySchedules
            as $index =>
                $schedule
        ) {

            if (
                $index
                ===
                0
            ) {

                $sheet =
                    $spreadsheet
                        ->getActiveSheet();

            } else {

                $sheet =
                    $spreadsheet
                        ->createSheet();
            }


            $this->buildDaySheet(
                $sheet,
                $schedule[
                    'rows'
                ],
                $schedule[
                    'date'
                ]
            );
        }


        $spreadsheet->setActiveSheetIndex(
            0
        );


        return $this->stream(
            $spreadsheet,
            $fileName
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Build One Day Worksheet
    |--------------------------------------------------------------------------
    */

    private function buildDaySheet(
        Worksheet $sheet,
        Collection $rows,
        Carbon $date
    ): void {

        $sheet->setTitle(
            substr(
                $date->format(
                    'l'
                ),
                0,
                31
            )
        );


        $sheet
            ->getColumnDimension(
                'A'
            )
            ->setWidth(
                12
            );


        $sheet
            ->getColumnDimension(
                'B'
            )
            ->setWidth(
                42
            );


        /*
        |--------------------------------------------------------------------------
        | Main Title
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A1:B1'
        );


        $sheet->setCellValue(
            'A1',
            'Kumon North Hobart Centre Schedule'
        );


        $sheet
            ->getStyle(
                'A1:B1'
            )
            ->getFont()
            ->setBold(
                true
            )
            ->setSize(
                16
            );


        $sheet
            ->getStyle(
                'A1:B1'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle(
                'A1:B1'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                'D9EAD3'
            );


        $sheet
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                30
            );


        /*
        |--------------------------------------------------------------------------
        | Day / Date
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A3:B3'
        );


        $sheet->setCellValue(
            'A3',
            $date->format(
                'l, d F Y'
            )
        );


        $sheet
            ->getStyle(
                'A3:B3'
            )
            ->getFont()
            ->setBold(
                true
            )
            ->setSize(
                13
            );


        $sheet
            ->getStyle(
                'A3:B3'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle(
                'A3:B3'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                'E2F0D9'
            );


        $sheet
            ->getRowDimension(
                3
            )
            ->setRowHeight(
                25
            );


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A4',
            'Time'
        );


        $sheet->setCellValue(
            'B4',
            'Subject / Student'
        );


        $sheet
            ->getStyle(
                'A4:B4'
            )
            ->getFont()
            ->setBold(
                true
            )
            ->setSize(
                11
            );


        $sheet
            ->getStyle(
                'A4:B4'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle(
                'A4:B4'
            )
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                'F3F4F6'
            );


        /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */

        $timeGroups =
            $rows
                ->groupBy(
                    'start_time'
                );


        $chunks =
            $this->buildPrintableChunks(
                $timeGroups
            );


        $currentRow =
            5;


        foreach (
            $chunks
            as $chunkIndex =>
                $chunk
        ) {

            if (
                $chunkIndex
                >
                0
            ) {

                $sheet->setBreak(
                    'A'
                    .
                    $currentRow,
                    Worksheet::BREAK_ROW
                );
            }


            $timeStartRow =
                $currentRow;


            foreach (
                $chunk[
                    'subjects'
                ]
                as $subjectBlock
            ) {

                /*
                |--------------------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    'B'
                    .
                    $currentRow,
                    $subjectBlock[
                        'subject_name'
                    ]
                );


                $sheet
                    ->getStyle(
                        'B'
                        .
                        $currentRow
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->setSize(
                        13
                    );


                $sheet
                    ->getStyle(
                        'B'
                        .
                        $currentRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    )
                    ->setVertical(
                        Alignment::VERTICAL_CENTER
                    );


                $sheet
                    ->getStyle(
                        'B'
                        .
                        $currentRow
                    )
                    ->getFill()
                    ->setFillType(
                        Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setRGB(
                        'EAF2F8'
                    );


                $sheet
                    ->getRowDimension(
                        $currentRow
                    )
                    ->setRowHeight(
                        24
                    );


                $this->applyBorders(
                    $sheet,
                    'A'
                    .
                    $currentRow
                    .
                    ':B'
                    .
                    $currentRow
                );


                $currentRow++;


                /*
                |--------------------------------------------------------------------------
                | Students
                |--------------------------------------------------------------------------
                */

                foreach (
                    $subjectBlock[
                        'rows'
                    ]
                    as $row
                ) {

                    $sheet->setCellValue(
                        'B'
                        .
                        $currentRow,
                        $row[
                            'student_name'
                        ]
                    );


                    $sheet
                        ->getStyle(
                            'B'
                            .
                            $currentRow
                        )
                        ->getFont()
                        ->setSize(
                            11
                        );


                    $sheet
                        ->getStyle(
                            'B'
                            .
                            $currentRow
                        )
                        ->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_LEFT
                        )
                        ->setVertical(
                            Alignment::VERTICAL_CENTER
                        );


                    if (
                        !empty(
                            $row[
                                'status_fill'
                            ]
                        )
                    ) {

                        $colour =
                            ltrim(
                                $row[
                                    'status_fill'
                                ],
                                '#'
                            );


                        if (
                            preg_match(
                                '/^[0-9A-Fa-f]{6}$/',
                                $colour
                            )
                        ) {

                            $sheet
                                ->getStyle(
                                    'B'
                                    .
                                    $currentRow
                                )
                                ->getFill()
                                ->setFillType(
                                    Fill::FILL_SOLID
                                )
                                ->getStartColor()
                                ->setRGB(
                                    strtoupper(
                                        $colour
                                    )
                                );


                            $sheet
                                ->getStyle(
                                    'B'
                                    .
                                    $currentRow
                                )
                                ->getFont()
                                ->setBold(
                                    true
                                );


                            if (
                                $this->useWhiteText(
                                    $colour
                                )
                            ) {

                                $sheet
                                    ->getStyle(
                                        'B'
                                        .
                                        $currentRow
                                    )
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB(
                                        'FFFFFF'
                                    );
                            }
                        }
                    }


                    $sheet
                        ->getRowDimension(
                            $currentRow
                        )
                        ->setRowHeight(
                            22
                        );


                    $this->applyBorders(
                        $sheet,
                        'A'
                        .
                        $currentRow
                        .
                        ':B'
                        .
                        $currentRow
                    );


                    $currentRow++;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */

            $timeEndRow =
                $currentRow
                -
                1;


            if (
                $timeEndRow
                >=
                $timeStartRow
            ) {

                if (
                    $timeEndRow
                    >
                    $timeStartRow
                ) {

                    $sheet->mergeCells(
                        'A'
                        .
                        $timeStartRow
                        .
                        ':A'
                        .
                        $timeEndRow
                    );
                }


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


                $sheet->setCellValue(
                    'A'
                    .
                    $timeStartRow,
                    $timeLabel
                );


                $sheet
                    ->getStyle(
                        'A'
                        .
                        $timeStartRow
                    )
                    ->getAlignment()
                    ->setTextRotation(
                        90
                    )
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    )
                    ->setVertical(
                        Alignment::VERTICAL_CENTER
                    );


                $sheet
                    ->getStyle(
                        'A'
                        .
                        $timeStartRow
                    )
                    ->getFont()
                    ->setBold(
                        true
                    )
                    ->setSize(
                        11
                    );


                $this->applyBorders(
                    $sheet,
                    'A'
                    .
                    $timeStartRow
                    .
                    ':A'
                    .
                    $timeEndRow
                );
            }
        }


        $lastRow =
            max(
                4,
                $currentRow
                -
                1
            );


        $this->applyBorders(
            $sheet,
            'A4:B'
            .
            $lastRow
        );


        $sheet
            ->getStyle(
                'A1:B'
                .
                $lastRow
            )
            ->getAlignment()
            ->setWrapText(
                true
            );


        /*
        |--------------------------------------------------------------------------
        | Print Settings
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getPageSetup()
            ->setOrientation(
                PageSetup::ORIENTATION_LANDSCAPE
            )
            ->setPaperSize(
                PageSetup::PAPERSIZE_A4
            )
            ->setFitToWidth(
                1
            )
            ->setFitToHeight(
                0
            );


        $sheet
            ->getPageMargins()
            ->setTop(
                0.3
            )
            ->setBottom(
                0.3
            )
            ->setLeft(
                0.3
            )
            ->setRight(
                0.3
            );


        $sheet
            ->getPageSetup()
            ->setRowsToRepeatAtTopByStartAndEnd(
                4,
                4
            );


        $sheet
            ->getPageSetup()
            ->setPrintArea(
                'A1:B'
                .
                $lastRow
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Printable Chunks
    |--------------------------------------------------------------------------
    */

    private function buildPrintableChunks(
        Collection $timeGroups
    ): Collection {

        $chunks =
            collect();


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


            $currentSubjects = [];
            $currentRowCount = 0;
            $chunkNumber = 1;


            foreach (
                $subjectGroups
                as $subjectName =>
                    $subjectRows
            ) {

                $subjectRows =
                    $subjectRows
                        ->values();


                $studentsPerPart =
                    max(
                        1,
                        self::MAX_DETAIL_ROWS_PER_CHUNK
                        -
                        1
                    );


                foreach (
                    $subjectRows
                        ->chunk(
                            $studentsPerPart
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
                        $currentRowCount
                        >
                        0
                        &&
                        (
                            $currentRowCount
                            +
                            $neededRows
                        )
                        >
                        self::MAX_DETAIL_ROWS_PER_CHUNK
                    ) {

                        $chunks->push([
                            'time' =>
                                $time,

                            'chunk_number' =>
                                $chunkNumber,

                            'subjects' =>
                                collect(
                                    $currentSubjects
                                ),

                            'row_count' =>
                                $currentRowCount,
                        ]);


                        $chunkNumber++;

                        $currentSubjects = [];

                        $currentRowCount = 0;
                    }


                    $currentSubjects[] = [
                        'subject_name' =>
                            $partSubjectName,

                        'rows' =>
                            $subjectPartRows,
                    ];


                    $currentRowCount +=
                        $neededRows;
                }
            }


            if (
                $currentRowCount
                >
                0
            ) {

                $chunks->push([
                    'time' =>
                        $time,

                    'chunk_number' =>
                        $chunkNumber,

                    'subjects' =>
                        collect(
                            $currentSubjects
                        ),

                    'row_count' =>
                        $currentRowCount,
                ]);
            }
        }


        return $chunks;
    }


    /*
    |--------------------------------------------------------------------------
    | Stream XLSX
    |--------------------------------------------------------------------------
    */

    private function stream(
        Spreadsheet $spreadsheet,
        string $fileName
    ) {

        return response()
            ->streamDownload(
                function () use (
                    $spreadsheet
                ) {

                    $writer =
                        new Xlsx(
                            $spreadsheet
                        );


                    $writer->save(
                        'php://output'
                    );


                    $spreadsheet
                        ->disconnectWorksheets();
                },

                $fileName
                .
                '.xlsx',

                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Border Helper
    |--------------------------------------------------------------------------
    */

    private function applyBorders(
        $sheet,
        string $range
    ): void {

        $sheet
            ->getStyle(
                $range
            )
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB(
                '000000'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Text Colour Helper
    |--------------------------------------------------------------------------
    */

    private function useWhiteText(
        string $hex
    ): bool {

        $hex =
            ltrim(
                $hex,
                '#'
            );


        if (
            strlen(
                $hex
            )
            !==
            6
        ) {

            return false;
        }


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


        return
            $brightness
            <
            150;
    }
}
