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
    | Build One Day Sheet - TWO COLUMN DESIGN
    |--------------------------------------------------------------------------
    |
    | LEFT:  A = Time, B = Subject / Student
    | SPACE: C
    | RIGHT: D = Time, E = Subject / Student
    |
    | The schedule is divided approximately in half and shown side-by-side.
    | This makes the exported sheet compact enough to print on one portrait page.
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
        | Page Columns
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getColumnDimension(
                'A'
            )
            ->setWidth(
                8
            );


        $sheet
            ->getColumnDimension(
                'B'
            )
            ->setWidth(
                30
            );


        $sheet
            ->getColumnDimension(
                'C'
            )
            ->setWidth(
                2
            );


        $sheet
            ->getColumnDimension(
                'D'
            )
            ->setWidth(
                8
            );


        $sheet
            ->getColumnDimension(
                'E'
            )
            ->setWidth(
                30
            );


        /*
        |--------------------------------------------------------------------------
        | Title
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A1:E1'
        );


        $sheet->setCellValue(
            'A1',
            'Kumon North Hobart Centre Schedule'
        );


        $sheet
            ->getStyle(
                'A1:E1'
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
                'A1:E1'
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
                'A1:E1'
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

        $sheet->mergeCells(
            'A2:E2'
        );


        $sheet->setCellValue(
            'A2',
            $date->format(
                'l, d F Y'
            )
        );


        $sheet
            ->getStyle(
                'A2:E2'
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
                'A2:E2'
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
                'A2:E2'
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
        | Two Column Headers
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'A3' => 'Time',
                'B3' => 'Subject / Student',
                'D3' => 'Time',
                'E3' => 'Subject / Student',
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
            [
                'A3:B3',
                'D3:E3',
            ]
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


        [
            $leftBlocks,
            $rightBlocks,
        ] =
            $this->splitBlocks(
                $blocks
            );


        /*
        |--------------------------------------------------------------------------
        | Render Both Sides
        |--------------------------------------------------------------------------
        */

        $leftLastRow =
            $this->renderColumn(
                $sheet,
                $leftBlocks,
                'A',
                'B',
                4
            );


        $rightLastRow =
            $this->renderColumn(
                $sheet,
                $rightBlocks,
                'D',
                'E',
                4
            );


        $lastRow =
            max(
                3,
                $leftLastRow,
                $rightLastRow
            );


        /*
        |--------------------------------------------------------------------------
        | Sheet Formatting
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:E'
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
                'A1:E'
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
    | Keeping a block together reduces awkward splits between columns.
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
    | Split Blocks Approximately 50 / 50
    |--------------------------------------------------------------------------
    */

    private function splitBlocks(
        Collection $blocks
    ): array {

        if (
            $blocks
                ->isEmpty()
        ) {

            return [
                collect(),
                collect(),
            ];
        }


        $totalHeight =
            $blocks
                ->sum(
                    'height'
                );


        $target =
            (int)
            ceil(
                $totalHeight
                /
                2
            );


        $left =
            collect();


        $right =
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
                $target
            ) {

                $left->push(
                    $block
                );


                $leftHeight +=
                    $block[
                        'height'
                    ];

            } else {

                $right->push(
                    $block
                );
            }
        }


        return [
            $left,
            $right,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Render One Side
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
                ->setTextRotation(
                    90
                )
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
