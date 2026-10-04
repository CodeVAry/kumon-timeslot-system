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
    | Every working day gets its own worksheet.
    | Each worksheet is designed as ONE A4 portrait page.
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

            $sheet =
                $index === 0
                    ? $spreadsheet
                        ->getActiveSheet()
                    : $spreadsheet
                        ->createSheet();


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


        $spreadsheet
            ->setActiveSheetIndex(
                0
            );


        return $this->stream(
            $spreadsheet,
            $fileName
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Build One Day Sheet - SINGLE VERTICAL DESIGN
    |--------------------------------------------------------------------------
    |
    | A = Time
    | B = Subject / Student
    |
    | The schedule is rendered from top to bottom in one continuous list.
    | There is no second block on the right-hand side.
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


        /*
        |--------------------------------------------------------------------------
        | Page Columns - one vertical schedule
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getColumnDimension('A')
            ->setWidth(14);


        $sheet
            ->getColumnDimension('B')
            ->setWidth(58);


        /*
        |--------------------------------------------------------------------------
        | Title
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A1:B1');


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
                14
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
                24
            );


        /*
        |--------------------------------------------------------------------------
        | Day / Date
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A2:B2');


        $sheet->setCellValue(
            'A2',
            $date->format(
                'l, d F Y'
            )
        );


        $sheet
            ->getStyle(
                'A2:B2'
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
                'A2:B2'
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
                'A2:B2'
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
                2
            )
            ->setRowHeight(
                20
            );


        /*
        |--------------------------------------------------------------------------
        | Single Vertical List Headers
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'A3' => 'Time',
                'B3' => 'Subject / Student',
            ]
            as $cell =>
                $value
        ) {

            $sheet->setCellValue(
                $cell,
                $value
            );
        }


        foreach (
            ['A3:B3']
            as $range
        ) {

            $sheet
                ->getStyle(
                    $range
                )
                ->getFont()
                ->setBold(
                    true
                )
                ->setSize(
                    9
                );


            $sheet
                ->getStyle(
                    $range
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
                    $range
                )
                ->getFill()
                ->setFillType(
                    Fill::FILL_SOLID
                )
                ->getStartColor()
                ->setRGB(
                    'F3F4F6'
                );


            $this->applyBorders(
                $sheet,
                $range
            );
        }


        $sheet
            ->getRowDimension(
                3
            )
            ->setRowHeight(
                18
            );


        /*
        |--------------------------------------------------------------------------
        | Build Logical Schedule Blocks
        |--------------------------------------------------------------------------
        */

        $blocks =
            $this->buildBlocks(
                $rows
            );


        /*
        |--------------------------------------------------------------------------
        | Render One Continuous Column
        |--------------------------------------------------------------------------
        */

        $lastRow = $this->renderColumn(
            $sheet,
            $blocks,
            'A',
            'B',
            4
        );


        /*
        |--------------------------------------------------------------------------
        | Sheet Formatting
        |--------------------------------------------------------------------------
        */

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
        | Print Settings - ONE PORTRAIT PAGE
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getPageSetup()
            ->setOrientation(
                PageSetup::ORIENTATION_PORTRAIT
            )
            ->setPaperSize(
                PageSetup::PAPERSIZE_A4
            )
            ->setFitToPage(
                true
            )
            ->setFitToWidth(
                1
            )
            ->setFitToHeight(
                1
            );


        $sheet
            ->getPageMargins()
            ->setTop(
                0.2
            )
            ->setBottom(
                0.2
            )
            ->setLeft(
                0.2
            )
            ->setRight(
                0.2
            )
            ->setHeader(
                0
            )
            ->setFooter(
                0
            );


        $sheet
            ->getPageSetup()
            ->setHorizontalCentered(
                true
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
    | Build Blocks
    |--------------------------------------------------------------------------
    |
    | A block is one subject within one time.
    | Keeping the class and its students together makes the list easy to read.
    |--------------------------------------------------------------------------
    */

    private function buildBlocks(
        Collection $rows
    ): Collection {

        $blocks =
            collect();


        $timeGroups =
            $rows
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


        return $blocks;
    }


    /*
    |--------------------------------------------------------------------------
    | Render One Vertical List
    |--------------------------------------------------------------------------
    */

    private function renderColumn(
        Worksheet $sheet,
        Collection $blocks,
        string $timeColumn,
        string $detailColumn,
        int $startRow
    ): int {

        $currentRow =
            $startRow;


        foreach (
            $blocks
            as $block
        ) {

            $blockStartRow =
                $currentRow;


            /*
            |--------------------------------------------------------------------------
            | Subject Header
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                $detailColumn
                .
                $currentRow,
                $block[
                    'subject_name'
                ]
            );


            $sheet
                ->getStyle(
                    $detailColumn
                    .
                    $currentRow
                )
                ->getFont()
                ->setBold(
                    true
                )
                ->setSize(
                    9
                );


            $sheet
                ->getStyle(
                    $detailColumn
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
                    $detailColumn
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
                    14
                );


            $currentRow++;


            /*
            |--------------------------------------------------------------------------
            | Students
            |--------------------------------------------------------------------------
            */

            foreach (
                $block[
                    'rows'
                ]
                as $row
            ) {

                $sheet->setCellValue(
                    $detailColumn
                    .
                    $currentRow,
                    $row[
                        'student_name'
                    ]
                );


                $sheet
                    ->getStyle(
                        $detailColumn
                        .
                        $currentRow
                    )
                    ->getFont()
                    ->setSize(
                        8
                    );


                $sheet
                    ->getStyle(
                        $detailColumn
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


                $attendance = $row['attendance_status'] ?? null;
                $colour = $attendance === 'vacation'
                    ? '9CA3AF'
                    : ($attendance === 'absent'
                        ? 'DC2626'
                        : ltrim((string) ($row['status_fill'] ?? ''), '#'));

                if (preg_match('/^[0-9A-Fa-f]{6}$/', $colour)) {

                    $sheet
                        ->getStyle(
                            $detailColumn
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
                            $detailColumn
                            .
                            $currentRow
                        )
                        ->getFont()
                        ->setBold(
                            true
                        );


                    if (
                        $attendance === 'absent'
                        ||
                        $this->useWhiteText(
                            $colour
                        )
                    ) {

                        $sheet
                            ->getStyle(
                                $detailColumn
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
                        13
                    );


                $currentRow++;
            }


            /*
            |--------------------------------------------------------------------------
            | Time Cell
            |--------------------------------------------------------------------------
            */

            $blockEndRow =
                $currentRow
                -
                1;


            if (
                $blockEndRow
                >
                $blockStartRow
            ) {

                $sheet->mergeCells(
                    $timeColumn
                    .
                    $blockStartRow
                    .
                    ':'
                    .
                    $timeColumn
                    .
                    $blockEndRow
                );
            }


            $sheet->setCellValue(
                $timeColumn
                .
                $blockStartRow,
                Carbon::parse(
                    $block[
                        'time'
                    ]
                )->format(
                    'g:i A'
                )
            );


            $sheet
                ->getStyle(
                    $timeColumn
                    .
                    $blockStartRow
                )
                ->getFont()
                ->setBold(
                    true
                )
                ->setSize(
                    8
                );


            $sheet
                ->getStyle(
                    $timeColumn
                    .
                    $blockStartRow
                )
                ->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                )
                ->setVertical(
                    Alignment::VERTICAL_CENTER
                );


            $this->applyBorders(
                $sheet,
                $timeColumn
                .
                $blockStartRow
                .
                ':'
                .
                $detailColumn
                .
                $blockEndRow
            );
        }


        return
            max(
                $startRow
                    -
                    1,
                $currentRow
                    -
                    1
            );
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
        Worksheet $sheet,
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
