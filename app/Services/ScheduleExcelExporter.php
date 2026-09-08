<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ScheduleExcelExporter
{
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


        $sheet->setTitle(
            substr(
                $date->format('l'),
                0,
                31
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Columns
        |--------------------------------------------------------------------------
        |
        | A = Time
        | B = Subject / Student
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getColumnDimension('A')
            ->setWidth(12);


        $sheet
            ->getColumnDimension('B')
            ->setWidth(42);


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
            ->getStyle('A1:B1')
            ->getFont()
            ->setBold(true)
            ->setSize(16);


        $sheet
            ->getStyle('A1:B1')
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle('A1:B1')
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                'D9EAD3'
            );


        $sheet
            ->getRowDimension(1)
            ->setRowHeight(30);


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
            ->getStyle('A3:B3')
            ->getFont()
            ->setBold(true)
            ->setSize(13);


        $sheet
            ->getStyle('A3:B3')
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle('A3:B3')
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setRGB(
                'E2F0D9'
            );


        $sheet
            ->getRowDimension(3)
            ->setRowHeight(25);


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
            ->getStyle('A4:B4')
            ->getFont()
            ->setBold(true)
            ->setSize(11);


        $sheet
            ->getStyle('A4:B4')
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle('A4:B4')
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
        | Group Data
        |--------------------------------------------------------------------------
        */

        $timeGroups =
            $rows
                ->groupBy(
                    'start_time'
                );


        $currentRow =
            5;


        foreach (
            $timeGroups
            as $time =>
                $timeRows
        ) {

            $timeStartRow =
                $currentRow;


            /*
            |--------------------------------------------------------------------------
            | Subject Groups Under This Time
            |--------------------------------------------------------------------------
            */

            $subjectGroups =
                $timeRows
                    ->groupBy(
                        'class_display'
                    );


            foreach (
                $subjectGroups
                as $subject =>
                    $subjectRows
            ) {

                /*
                |--------------------------------------------------------------------------
                | Subject Heading
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    'B' . $currentRow,
                    $subject
                );


                $sheet
                    ->getStyle(
                        'B' . $currentRow
                    )
                    ->getFont()
                    ->setBold(true)
                    ->setSize(13);


                $sheet
                    ->getStyle(
                        'B' . $currentRow
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
                        'B' . $currentRow
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
                    'A' . $currentRow
                    .
                    ':B'
                    .
                    $currentRow
                );


                $currentRow++;


                /*
                |--------------------------------------------------------------------------
                | Students Under Subject
                |--------------------------------------------------------------------------
                */

                foreach (
                    $subjectRows
                    as $row
                ) {

                    $sheet->setCellValue(
                        'B' . $currentRow,
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
                        ->setSize(11);


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


                    /*
                    |--------------------------------------------------------------------------
                    | Student Status Colour
                    |--------------------------------------------------------------------------
                    |
                    | Active = NO colour.
                    |
                    | Other statuses use their configured
                    | StudentStatus colour.
                    |
                    | Vacation uses the dynamic leave colour.
                    |--------------------------------------------------------------------------
                    */

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
                            ->setBold(true);


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
            |
            | Time appears ONCE and spans the complete
            | set of subjects/students for the time.
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


                $sheet->setCellValue(
                    'A'
                    .
                    $timeStartRow,
                    $time
                );


                /*
                |--------------------------------------------------------------------------
                | Rotate Time 90 Degrees
                |--------------------------------------------------------------------------
                */

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
                    ->setBold(true)
                    ->setSize(11);


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


        /*
        |--------------------------------------------------------------------------
        | General Formatting
        |--------------------------------------------------------------------------
        */

        $lastRow =
            max(
                4,
                $currentRow - 1
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
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            )
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
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
                1,
                4
            );


        /*
        |--------------------------------------------------------------------------
        | Download XLSX
        |--------------------------------------------------------------------------
        */

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
