<?php

namespace App\Services;

use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Section;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use App\Models\Admin\StudentImportAlias;
use App\Models\Admin\SubSection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTimeslotImportService
{
    /*
    |--------------------------------------------------------------------------
    | Parse Workbook
    |--------------------------------------------------------------------------
    */

    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);

        $results = [];

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $sheetName = mb_strtolower(
                trim($sheet->getTitle())
            );

            /*
             * Client workbook sheets:
             *
             * MoTh New
             * TuFri New
             */
            if (
                !str_contains($sheetName, 'moth')
                &&
                !str_contains($sheetName, 'tufri')
            ) {
                continue;
            }

            $results = array_merge(
                $results,
                $this->parseSheet($sheet)
            );
        }

        return $results;
    }


    /*
    |--------------------------------------------------------------------------
    | Parse One Sheet
    |--------------------------------------------------------------------------
    */

    private function parseSheet(
        Worksheet $sheet
    ): array {
        $highestRow = $sheet->getHighestDataRow();

        $dayColumns = $this->findDayColumns(
            $sheet
        );

        if (empty($dayColumns)) {
            throw new \RuntimeException(
                'Could not find day headings in sheet: '
                . $sheet->getTitle()
            );
        }

        $rows = [];

        $currentTime = null;
        $currentSubject = null;

        for (
            $rowNumber = 1;
            $rowNumber <= $highestRow;
            $rowNumber++
        ) {
            /*
            |--------------------------------------------------------------------------
            | Time - Column A
            |--------------------------------------------------------------------------
            */

            $timeCandidate = trim(
                (string) $sheet
                    ->getCell(
                        'A' . $rowNumber
                    )
                    ->getFormattedValue()
            );

            $parsedTime = $this->parseStartTime(
                $timeCandidate
            );

            if ($parsedTime) {
                $currentTime = $parsedTime;
            }


            /*
            |--------------------------------------------------------------------------
            | Subject - Column B
            |--------------------------------------------------------------------------
            */

            $subjectCandidate = trim(
                (string) $sheet
                    ->getCell(
                        'B' . $rowNumber
                    )
                    ->getFormattedValue()
            );

            if (
                $this->isSubject(
                    $subjectCandidate
                )
            ) {
                $currentSubject =
                    $subjectCandidate;
            }


            /*
             * Need both time and subject
             * before reading student allocations.
             */
            if (
                !$currentTime
                ||
                !$currentSubject
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Student Cells
            |--------------------------------------------------------------------------
            */

            foreach (
                $dayColumns
                as $columnLetter => $dayName
            ) {
                $rawStudent = trim(
                    (string) $sheet
                        ->getCell(
                            $columnLetter
                            . $rowNumber
                        )
                        ->getFormattedValue()
                );

                if (
                    !$this->isStudentCell(
                        $rawStudent
                    )
                ) {
                    continue;
                }

                $studentName =
                    $this->cleanStudentName(
                        $rawStudent
                    );

                if ($studentName === '') {
                    continue;
                }

                $subject =
                    $this->resolveSubject(
                        $currentSubject
                    );

                /*
                 * Default to start time
                 * from Column A.
                 */
                $rowTime =
                    $currentTime;


                /*
                |--------------------------------------------------------------------------
                | Interactive Exact Time
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | 620pm - - Student Name
                |
                | becomes:
                |
                | 18:20:00
                |--------------------------------------------------------------------------
                */

                if (
                    $subject['section']
                    ===
                    'Interactive'
                ) {
                    $embeddedTime =
                        $this->extractTimeFromText(
                            $rawStudent
                        );

                    if ($embeddedTime) {
                        $rowTime =
                            $embeddedTime;
                    }
                }


                $rows[] = [
                    'sheet' =>
                        $sheet->getTitle(),

                    'row_number' =>
                        $rowNumber,

                    'raw_student' =>
                        $rawStudent,

                    'student_name' =>
                        $studentName,

                    'normalised_name' =>
                        $this->normalizeName(
                            $studentName
                        ),

                    'day' =>
                        $dayName,

                    'time' =>
                        $rowTime,

                    'subject_label' =>
                        $currentSubject,

                    'section_name' =>
                        $subject['section'],

                    'sub_section_name' =>
                        $subject['sub_section'],
                ];
            }
        }

        return $rows;
    }


    /*
    |--------------------------------------------------------------------------
    | Find Day Columns
    |--------------------------------------------------------------------------
    */

    private function findDayColumns(
        Worksheet $sheet
    ): array {
        $highestColumn =
            $sheet->getHighestDataColumn();

        $highestColumnIndex =
            Coordinate::columnIndexFromString(
                $highestColumn
            );

        $dayColumns = [];

        for (
            $row = 1;
            $row <= 15;
            $row++
        ) {
            for (
                $column = 1;
                $column <= $highestColumnIndex;
                $column++
            ) {
                $columnLetter =
                    Coordinate::stringFromColumnIndex(
                        $column
                    );

                $value = trim(
                    (string) $sheet
                        ->getCell(
                            $columnLetter . $row
                        )
                        ->getFormattedValue()
                );

                $dayName = ucfirst(
                    mb_strtolower(
                        $value
                    )
                );

                if (
                    in_array(
                        $dayName,
                        [
                            'Monday',
                            'Tuesday',
                            'Thursday',
                            'Friday',
                        ],
                        true
                    )
                ) {
                    $dayColumns[
                        $columnLetter
                    ] =
                        $dayName;
                }
            }
        }

        return $dayColumns;
    }


    /*
    |--------------------------------------------------------------------------
    | Match Timeslot Rows With Student Profiles
    |--------------------------------------------------------------------------
    */

    public function matchWithProfiles(
        array $timeslotRows,
        array $profileIndex
    ): array {
        $results = [];

        /*
         * Prevent duplicate allocations
         * inside the same workbook.
         */
        $seenAllocations = [];


        foreach (
            $timeslotRows
            as $row
        ) {
            /*
            |--------------------------------------------------------------------------
            | Student Match
            |--------------------------------------------------------------------------
            */

            $profileResult =
                $this->findProfileMatch(
                    $row['normalised_name'],
                    $profileIndex
                );

            $profile =
                $profileResult['profile'];

            $status =
                $profileResult['status'];

            $message =
                $profileResult['message'];


            /*
            |--------------------------------------------------------------------------
            | Class Match
            |--------------------------------------------------------------------------
            */

            $classMatch =
                $this->findOffering(
                    $row
                );

            if (
                !$classMatch['offering']
            ) {
                $status =
                    'class_not_found';

                $message =
                    $classMatch['message'];
            }


            /*
            |--------------------------------------------------------------------------
            | Duplicate Allocation In Uploaded Workbook
            |--------------------------------------------------------------------------
            */

            if (
                $status === 'ready'
                &&
                $profile
                &&
                $classMatch['offering']
            ) {
                $studentIdentifier =
                    $profile['external_id']
                    ??
                    (
                        $profile['id']
                        ??
                        $row['normalised_name']
                    );

                $allocationKey =
                    $studentIdentifier
                    . '|'
                    . $classMatch['offering']->id
                    . '|'
                    . (
                        $classMatch['sub_section']
                            ?->id
                        ??
                        'none'
                    );

                if (
                    isset(
                        $seenAllocations[
                            $allocationKey
                        ]
                    )
                ) {
                    $status =
                        'duplicate';

                    $message =
                        'Duplicate allocation in spreadsheet - skipped.';
                } else {
                    $seenAllocations[
                        $allocationKey
                    ] = true;
                }
            }


            $results[] = array_merge(
                $row,
                [
                    'status' =>
                        $status,

                    'message' =>
                        $message,

                    'profile' =>
                        $profile,

                    'offering_id' =>
                        $classMatch['offering']
                            ?->id,

                    'sub_section_id' =>
                        $classMatch['sub_section']
                            ?->id,
                ]
            );
        }

        return $results;
    }


    /*
    |--------------------------------------------------------------------------
    | Find Student Profile
    |--------------------------------------------------------------------------
    |
    | Order:
    |
    | 1. Exact cleaned name
    | 2. Saved alias mapping
    | 3. Not found
    |--------------------------------------------------------------------------
    */

    private function findProfileMatch(
        string $timeslotName,
        array $profileIndex
    ): array {
        $timeslotName =
            $this->normalizeName(
                $timeslotName
            );


        /*
        |--------------------------------------------------------------------------
        | 1. Exact Match
        |--------------------------------------------------------------------------
        */

        $matches =
            $profileIndex[
                $timeslotName
            ]
            ??
            [];


        if (
            count($matches)
            ===
            1
        ) {
            return [
                'status' =>
                    'ready',

                'message' =>
                    'Ready',

                'profile' =>
                    $matches[0],
            ];
        }


        if (
            count($matches)
            >
            1
        ) {
            return [
                'status' =>
                    'student_ambiguous',

                'message' =>
                    'Multiple student profiles match this name',

                'profile' =>
                    null,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Saved Alias Mapping
        |--------------------------------------------------------------------------
        */

        $alias =
            StudentImportAlias::with(
                'student'
            )
                ->where(
                    'source_name',
                    $timeslotName
                )
                ->first();


        if (
            $alias
            &&
            $alias->student
        ) {
            $student =
                $alias->student;

            return [
                'status' =>
                    'ready',

                'message' =>
                    'Ready - matched using saved mapping',

                'profile' => [
                    'id' =>
                        $student->id,

                    'external_id' =>
                        $student->external_id,

                    'first_name' =>
                        $student->first_name,

                    'last_name' =>
                        $student->last_name,

                    'date_of_birth' =>
                        $student->date_of_birth
                            ?->format('Y-m-d'),
                ],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Not Found
        |--------------------------------------------------------------------------
        */

        return [
            'status' =>
                'student_not_found',

            'message' =>
                'Student not found - select the correct student and save mapping',

            'profile' =>
                null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Find Existing Seeded Offering
    |--------------------------------------------------------------------------
    */

    private function findOffering(
        array $row
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Day
        |--------------------------------------------------------------------------
        */

        $day =
            Day::whereRaw(
                'LOWER(day_name) = ?',
                [
                    mb_strtolower(
                        trim(
                            $row['day']
                        )
                    ),
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (!$day) {
            return [
                'offering' =>
                    null,

                'sub_section' =>
                    null,

                'message' =>
                    'Day not found: '
                    . $row['day'],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        $section =
            Section::where(
                'is_active',
                true
            )
                ->get()
                ->first(
                    function (
                        $section
                    ) use (
                        $row
                    ) {
                        return
                            $this->normalizeClassName(
                                $section->section_name
                            )
                            ===
                            $this->normalizeClassName(
                                $row['section_name']
                            );
                    }
                );


        if (!$section) {
            return [
                'offering' =>
                    null,

                'sub_section' =>
                    null,

                'message' =>
                    'Section not found: '
                    . $row['section_name'],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Math Sub-section
        |--------------------------------------------------------------------------
        */

        $subSection = null;


        if (
            !empty(
                $row['sub_section_name']
            )
        ) {
            $subSection =
                SubSection::where(
                    'section_id',
                    $section->id
                )
                    ->where(
                        'is_active',
                        true
                    )
                    ->get()
                    ->first(
                        function (
                            $subSection
                        ) use (
                            $row
                        ) {
                            return
                                $this->normalizeClassName(
                                    $subSection
                                        ->sub_section_name
                                )
                                ===
                                $this->normalizeClassName(
                                    $row[
                                        'sub_section_name'
                                    ]
                                );
                        }
                    );


            if (!$subSection) {
                return [
                    'offering' =>
                        null,

                    'sub_section' =>
                        null,

                    'message' =>
                        'Sub-section not found: '
                        . $row[
                            'sub_section_name'
                        ],
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Match START Time
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Excel:
        | 6 - 6.45
        |
        | We match:
        | 18:00:00
        |
        | Not:
        | 18:45:00
        |--------------------------------------------------------------------------
        */

        $offering =
            SectionOffering::where(
                'day_id',
                $day->id
            )
                ->where(
                    'section_id',
                    $section->id
                )
                ->whereTime(
                    'start_time',
                    $row['time']
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (!$offering) {
            return [
                'offering' =>
                    null,

                'sub_section' =>
                    $subSection,

                'message' =>
                    'Seeded class not found: '
                    . $row['section_name']
                    . (
                        $row['sub_section_name']
                            ? ' - '
                                . $row[
                                    'sub_section_name'
                                ]
                            : ''
                    )
                    . ' / '
                    . $row['day']
                    . ' / '
                    . Carbon::parse(
                        $row['time']
                    )->format(
                        'g:i A'
                    ),
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Math Sub-section Link
        |--------------------------------------------------------------------------
        */

        if ($subSection) {
            $linked =
                $offering
                    ->subSections()
                    ->where(
                        'sub_sections.id',
                        $subSection->id
                    )
                    ->exists();


            if (!$linked) {
                return [
                    'offering' =>
                        null,

                    'sub_section' =>
                        $subSection,

                    'message' =>
                        'Math offering is not linked to '
                        . $subSection
                            ->sub_section_name,
                ];
            }
        }


        return [
            'offering' =>
                $offering,

            'sub_section' =>
                $subSection,

            'message' =>
                null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Import Allocation
    |--------------------------------------------------------------------------
    |
    | Safe when same file is imported again.
    |
    | Same:
    |
    | student_id
    | + section_offering_id
    | + sub_section_id
    |
    | will reuse the existing enrolment.
    |--------------------------------------------------------------------------
    */

    public function importAllocation(
        Student $student,
        array $row
    ): Enrolment {
        $offering =
            SectionOffering::with([
                'section',
                'day',
            ])
                ->findOrFail(
                    $row['offering_id']
                );


        /*
        |--------------------------------------------------------------------------
        | Find Existing Enrolment
        |--------------------------------------------------------------------------
        */

        $query =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'section_offering_id',
                    $offering->id
                )
                ->where(
                    'is_wishlist',
                    false
                );


        if (
            !empty(
                $row['sub_section_id']
            )
        ) {
            $query->where(
                'sub_section_id',
                $row['sub_section_id']
            );
        } else {
            $query->whereNull(
                'sub_section_id'
            );
        }


        $existing =
            $query->first();


        /*
        |--------------------------------------------------------------------------
        | Existing Allocation
        |--------------------------------------------------------------------------
        */

        if ($existing) {
            if (
                !$existing->is_active
            ) {
                $existing->is_active =
                    true;

                $existing->save();
            }

            return $existing;
        }


        /*
        |--------------------------------------------------------------------------
        | Capacity Check
        |--------------------------------------------------------------------------
        */

        $this->guardCapacity(
            $offering
        );


        /*
        |--------------------------------------------------------------------------
        | Create New Enrolment
        |--------------------------------------------------------------------------
        */

        return Enrolment::create([
            'student_id' =>
                $student->id,

            'section_offering_id' =>
                $offering->id,

            'sub_section_id' =>
                $row['sub_section_id']
                ??
                null,

            'wishlist_for_enrolment_id' =>
                null,

            'enrolment_date' =>
                now()->toDateString(),

            'is_wishlist' =>
                false,

            'wishlist_status' =>
                null,

            'requested_by_guardian_id' =>
                null,

            'reviewed_by_user_id' =>
                null,

            'reviewed_at' =>
                null,

            'wishlist_review_note' =>
                null,

            'is_active' =>
                true,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Capacity Protection
    |--------------------------------------------------------------------------
    */

    private function guardCapacity(
        SectionOffering $offering
    ): void {
        if (
            !$offering->max_seats
            ||
            $offering->max_seats <= 0
        ) {
            return;
        }


        $usedSeats =
            Enrolment::where(
                'section_offering_id',
                $offering->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_wishlist',
                    false
                )
                ->count();


        if (
            $usedSeats
            >=
            $offering->max_seats
        ) {
            throw new \RuntimeException(
                'Class capacity exceeded for '
                . (
                    $offering
                        ->section
                        ?->section_name
                    ??
                    'Class'
                )
                . ' / '
                . (
                    $offering
                        ->day
                        ?->day_name
                    ??
                    ''
                )
                . ' / '
                . Carbon::parse(
                    $offering->start_time
                )->format(
                    'g:i A'
                )
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Subject
    |--------------------------------------------------------------------------
    */

    private function resolveSubject(
        string $subject
    ): array {
        $value =
            mb_strtolower(
                trim(
                    $subject
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Interactive
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $value,
                'interactive'
            )
        ) {
            return [
                'section' =>
                    'Interactive',

                'sub_section' =>
                    null,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | English
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $value,
                'english'
            )
        ) {
            return [
                'section' =>
                    'English',

                'sub_section' =>
                    null,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Math 3A
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $value,
                '3a'
            )
            ||
            str_contains(
                $value,
                '3a71'
            )
            ||
            str_contains(
                $value,
                '2a - a'
            )
            ||
            str_contains(
                $value,
                '2a-a'
            )
        ) {
            return [
                'section' =>
                    'Math',

                'sub_section' =>
                    '3A',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Math B-D
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $value,
                'b-d'
            )
            ||
            str_contains(
                $value,
                'b - d'
            )
            ||
            str_contains(
                $value,
                'b-c-d'
            )
            ||
            str_contains(
                $value,
                'b - c - d'
            )
            ||
            str_contains(
                $value,
                'b–d'
            )
        ) {
            return [
                'section' =>
                    'Math',

                'sub_section' =>
                    'B-D',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Math E+
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $value,
                'e+'
            )
        ) {
            return [
                'section' =>
                    'Math',

                'sub_section' =>
                    'E+',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Generic Math
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $value,
                'math'
            )
            ||
            str_contains(
                $value,
                'maths'
            )
        ) {
            return [
                'section' =>
                    'Math',

                'sub_section' =>
                    null,
            ];
        }


        return [
            'section' =>
                trim(
                    $subject
                ),

            'sub_section' =>
                null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Is Subject
    |--------------------------------------------------------------------------
    */

    private function isSubject(
        string $value
    ): bool {
        $value =
            mb_strtolower(
                trim(
                    $value
                )
            );

        if ($value === '') {
            return false;
        }

        return
            str_contains(
                $value,
                'english'
            )
            ||
            str_contains(
                $value,
                'interactive'
            )
            ||
            str_contains(
                $value,
                'math'
            )
            ||
            str_contains(
                $value,
                'maths'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Parse START Time
    |--------------------------------------------------------------------------
    |
    | Examples:
    |
    | 3.45 - 4.30
    | -> 15:45:00
    |
    | 4.30 - 5.15
    | -> 16:30:00
    |
    | 5.15 - 6
    | -> 17:15:00
    |
    | 6 - 6.45
    | -> 18:00:00
    |--------------------------------------------------------------------------
    */

    private function parseStartTime(
        string $value
    ): ?string {
        $value = trim($value);

        if ($value === '') {
            return null;
        }


        /*
         * Only use the FIRST time
         * in the range.
         */
        $parts =
            preg_split(
                '/\s*[-–—]\s*/u',
                $value
            );

        $start =
            trim(
                $parts[0]
                ??
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Time With Minutes
        |--------------------------------------------------------------------------
        |
        | 3.45
        | 3:45
        | 345pm
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^(\d{1,2})[:.]?(\d{2})\s*(am|pm)?$/i',
                $start,
                $matches
            )
        ) {
            $hour =
                (int) $matches[1];

            $minute =
                (int) $matches[2];

            $period =
                isset(
                    $matches[3]
                )
                &&
                $matches[3] !== ''
                    ? mb_strtolower(
                        $matches[3]
                    )
                    : null;


            if (
                $period === 'pm'
                &&
                $hour < 12
            ) {
                $hour += 12;
            }


            if (
                $period === 'am'
                &&
                $hour === 12
            ) {
                $hour = 0;
            }


            /*
             * Centre timetable is afternoon/evening.
             */
            if (
                !$period
                &&
                $hour < 12
            ) {
                $hour += 12;
            }


            return sprintf(
                '%02d:%02d:00',
                $hour,
                $minute
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Hour Only
        |--------------------------------------------------------------------------
        |
        | 6
        | -> 18:00:00
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^(\d{1,2})\s*(am|pm)?$/i',
                $start,
                $matches
            )
        ) {
            $hour =
                (int) $matches[1];

            $period =
                isset(
                    $matches[2]
                )
                &&
                $matches[2] !== ''
                    ? mb_strtolower(
                        $matches[2]
                    )
                    : null;


            if (
                $period === 'pm'
                &&
                $hour < 12
            ) {
                $hour += 12;
            }


            if (
                $period === 'am'
                &&
                $hour === 12
            ) {
                $hour = 0;
            }


            if (
                !$period
                &&
                $hour < 12
            ) {
                $hour += 12;
            }


            return sprintf(
                '%02d:00:00',
                $hour
            );
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Extract Interactive Exact Time
    |--------------------------------------------------------------------------
    */

    private function extractTimeFromText(
        string $text
    ): ?string {
        if (
            preg_match(
                '/\b(\d{1,2})[:.]?(\d{2})\s*(am|pm)\b/i',
                $text,
                $matches
            )
        ) {
            $hour =
                (int) $matches[1];

            $minute =
                (int) $matches[2];

            $period =
                mb_strtolower(
                    $matches[3]
                );


            if (
                $period === 'pm'
                &&
                $hour < 12
            ) {
                $hour += 12;
            }


            if (
                $period === 'am'
                &&
                $hour === 12
            ) {
                $hour = 0;
            }


            return sprintf(
                '%02d:%02d:00',
                $hour,
                $minute
            );
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Is Student Cell
    |--------------------------------------------------------------------------
    */

    private function isStudentCell(
        string $value
    ): bool {
        $value = trim($value);

        if ($value === '') {
            return false;
        }


        /*
         * Ignore day headings.
         */
        if (
            in_array(
                mb_strtolower(
                    $value
                ),
                [
                    'monday',
                    'tuesday',
                    'wednesday',
                    'thursday',
                    'friday',
                    'saturday',
                    'sunday',
                ],
                true
            )
        ) {
            return false;
        }


        /*
         * Ignore separator rows.
         */
        if (
            preg_match(
                '/^[-–—\s]+$/u',
                $value
            )
        ) {
            return false;
        }


        return
            $this->cleanStudentName(
                $value
            )
            !==
            '';
    }


    /*
    |--------------------------------------------------------------------------
    | Clean Student Name
    |--------------------------------------------------------------------------
    |
    | Examples:
    |
    | Amelia Barnard Eng + PSP
    | -> Amelia Barnard
    |
    | Amol Gauba x 2 + PSP
    | -> Amol Gauba
    |
    | Niya Berera (Mon only) - DO ENG FIRST
    | -> Niya Berera
    |
    | 620pm - - Emily Dang
    | -> Emily Dang
    |--------------------------------------------------------------------------
    */

    public function cleanStudentName(
        string $value
    ): string {
        $value = trim($value);


        /*
        |--------------------------------------------------------------------------
        | Remove Interactive Time Prefix
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/^\s*\d{1,2}[:.]?\d{2}\s*(?:am|pm)\s*[-–—]+\s*[-–—]*\s*/i',
                '',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove Bracket Notes
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\([^)]*\)/u',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove x2
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\bx\s*2\b/i',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove Eng + PSP
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\beng(?:lish)?\s*\+\s*psp\b/i',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove + PSP
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\+\s*psp\b/i',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove PSP
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\bpsp\b/i',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove Trailing Eng / English
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\beng(?:lish)?\b\s*$/i',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove DO ... FIRST
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\s*[-–—]\s*DO\s+(?:ENG|ENGLISH|MATH|MATHS)\s+FIRST.*$/i',
                '',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove WE MARK Notes
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\s*[-–—]\s*WE\s+MARK.*$/i',
                '',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove Other Instructions
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\s*[-–—]\s*(?:DO|START|FINISH)\b.*$/i',
                '',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Remove Double Hyphens
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\s*[-–—]{2,}\s*/u',
                ' ',
                $value
            );


        /*
        |--------------------------------------------------------------------------
        | Collapse Spaces
        |--------------------------------------------------------------------------
        */

        $value =
            preg_replace(
                '/\s+/',
                ' ',
                $value
            );


        return trim(
            $value,
            " \t\n\r\0\x0B-–—"
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Student Name
    |--------------------------------------------------------------------------
    */

    public function normalizeName(
        string $name
    ): string {
        $name =
            mb_strtolower(
                trim(
                    $name
                )
            );


        $name =
            preg_replace(
                '/[^a-z0-9]+/u',
                ' ',
                $name
            );


        $name =
            preg_replace(
                '/\s+/',
                ' ',
                $name
            );


        return trim(
            $name
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Class Name
    |--------------------------------------------------------------------------
    */

    private function normalizeClassName(
        string $value
    ): string {
        $value =
            mb_strtolower(
                trim(
                    $value
                )
            );


        return preg_replace(
            '/[^a-z0-9+]+/i',
            '',
            $value
        );
    }
}
