<?php

namespace App\Services;

use App\Models\Admin\Guardian;
use App\Models\Admin\Student;
use App\Models\Admin\StudentStatus;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentProfileImportService
{
    /*
    |--------------------------------------------------------------------------
    | Read Student Profile Report
    |--------------------------------------------------------------------------
    */

    public function parse(
        string $filePath
    ): array {

        $spreadsheet =
            IOFactory::load(
                $filePath
            );


        $sheet =
            $spreadsheet
                ->getSheet(0);


        $highestRow =
            $sheet
                ->getHighestDataRow();


        $highestColumn =
            $sheet
                ->getHighestDataColumn();


        /*
        |--------------------------------------------------------------------------
        | Headers
        |--------------------------------------------------------------------------
        */

        $headerValues =
            $sheet
                ->rangeToArray(
                    'A1:'
                    .
                    $highestColumn
                    .
                    '1',
                    null,
                    true,
                    true,
                    false
                )[0];


        $headers =
            [];


        foreach (
            $headerValues
            as $index =>
                $header
        ) {

            $headers[
                $this->normalizeHeader(
                    $header
                )
            ] =
                $index;
        }


        /*
        |--------------------------------------------------------------------------
        | Required Fields
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'student id',
                'first name',
                'last name',
                'date of birth',
            ]
            as $required
        ) {

            if (
                !array_key_exists(
                    $required,
                    $headers
                )
            ) {

                throw new \RuntimeException(
                    'Student Profile Report is missing the column: '
                    .
                    $required
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Rows
        |--------------------------------------------------------------------------
        */

        $results =
            [];


        for (
            $rowNumber = 2;
            $rowNumber <= $highestRow;
            $rowNumber++
        ) {

            $row =
                $sheet
                    ->rangeToArray(
                        'A'
                        .
                        $rowNumber
                        .
                        ':'
                        .
                        $highestColumn
                        .
                        $rowNumber,
                        null,
                        true,
                        true,
                        false
                    )[0];


            $studentId =
                trim(
                    (string)
                    $this->cell(
                        $row,
                        $headers,
                        'student id'
                    )
                );


            $firstName =
                trim(
                    (string)
                    $this->cell(
                        $row,
                        $headers,
                        'first name'
                    )
                );


            $lastName =
                trim(
                    (string)
                    $this->cell(
                        $row,
                        $headers,
                        'last name'
                    )
                );


            if (
                $studentId === ''
                &&
                $firstName === ''
                &&
                $lastName === ''
            ) {

                continue;
            }


            $results[] = [

                'row_number' =>
                    $rowNumber,


                'external_id' =>
                    $studentId,


                'first_name' =>
                    $firstName,


                'last_name' =>
                    $lastName,


                'date_of_birth' =>
                    $this->parseDate(
                        $this->cell(
                            $row,
                            $headers,
                            'date of birth'
                        )
                    ),


                'normalised_name' =>
                    $this->normalizeName(
                        $firstName
                        .
                        ' '
                        .
                        $lastName
                    ),


                'guardians' =>
                    array_values(
                        array_filter([

                            $this->guardian(
                                $row,
                                $headers,
                                'mother',
                                'Mother'
                            ),


                            $this->guardian(
                                $row,
                                $headers,
                                'father',
                                'Father'
                            ),


                            $this->guardian(
                                $row,
                                $headers,
                                'other',
                                'Other'
                            ),
                        ])
                    ),
            ];
        }


        return $results;
    }


    /*
    |--------------------------------------------------------------------------
    | Student Name Index
    |--------------------------------------------------------------------------
    */

    public function indexByName(
        array $profiles
    ): array {

        $index =
            [];


        foreach (
            $profiles
            as $profile
        ) {

            $aliases =
                [];


            /*
             * Full name.
             */
            $aliases[] =
                $profile[
                    'normalised_name'
                ];


            /*
             * First given name + surname.
             *
             * Example:
             *
             * First name:
             * Amira Bade
             *
             * Last name:
             * Shrestha
             *
             * Also indexes:
             * Amira Shrestha
             */
            $firstNameParts =
                preg_split(
                    '/\s+/',
                    trim(
                        $profile[
                            'first_name'
                        ]
                    )
                );


            if (
                !empty(
                    $firstNameParts[0]
                )
            ) {

                $aliases[] =
                    $this->normalizeName(
                        $firstNameParts[0]
                        .
                        ' '
                        .
                        $profile[
                            'last_name'
                        ]
                    );
            }


            foreach (
                array_unique(
                    $aliases
                )
                as $alias
            ) {

                if (
                    $alias === ''
                ) {

                    continue;
                }


                $index[
                    $alias
                ][] =
                    $profile;
            }
        }


        return $index;
    }


    /*
    |--------------------------------------------------------------------------
    | Import Student
    |--------------------------------------------------------------------------
    */

    public function importProfileRow(
        array $profile
    ): Student {

        $activeStatus =
            StudentStatus::where(
                'status_name',
                'Active'
            )
                ->where(
                    'is_active',
                    true
                )
                ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $student =
            Student::where(
                'external_id',
                $profile[
                    'external_id'
                ]
            )
                ->first();


        if (
            !$student
        ) {

            $student =
                new Student();


            $student
                ->external_id =
                $profile[
                    'external_id'
                ];


            $student
                ->student_status_id =
                $activeStatus
                    ->id;


            $student
                ->join_date =
                now()
                    ->toDateString();
        }


        $student
            ->first_name =
            $profile[
                'first_name'
            ];


        $student
            ->last_name =
            $profile[
                'last_name'
            ];


        $student
            ->date_of_birth =
            $profile[
                'date_of_birth'
            ];


        $student
            ->is_active =
            true;


        $student
            ->inactive_since =
            null;


        $student
            ->save();


        /*
        |--------------------------------------------------------------------------
        | Guardians
        |--------------------------------------------------------------------------
        */

        $hasPrimary =
            $student
                ->guardians()
                ->wherePivot(
                    'is_primary',
                    true
                )
                ->exists();


        foreach (
            $profile[
                'guardians'
            ]
            as $guardianData
        ) {

            $guardian =
                $this->findGuardian(
                    $guardianData
                );


            if (
                !$guardian
            ) {

                $guardian =
                    Guardian::create([

                        'first_name' =>
                            $guardianData[
                                'first_name'
                            ],


                        'last_name' =>
                            $guardianData[
                                'last_name'
                            ],


                        'email' =>
                            $guardianData[
                                'email'
                            ],


                        'phone' =>
                            $guardianData[
                                'phone'
                            ],


                        'is_active' =>
                            true,
                    ]);
            }


            $isPrimary =
                !$hasPrimary;


            $student
                ->guardians()
                ->syncWithoutDetaching([

                    $guardian->id => [

                        'relationship' =>
                            $guardianData[
                                'relationship'
                            ],


                        'is_primary' =>
                            $isPrimary,


                        'is_emergency_contact' =>
                            false,
                    ],
                ]);


            if (
                $isPrimary
            ) {

                $hasPrimary =
                    true;
            }
        }


        return $student;
    }


    /*
    |--------------------------------------------------------------------------
    | Find Existing Guardian
    |--------------------------------------------------------------------------
    */

    private function findGuardian(
        array $guardianData
    ): ?Guardian {

        $email =
            Guardian::normalizeEmail(
                $guardianData[
                    'email'
                ]
            );


        $phone =
            Guardian::normalizePhone(
                $guardianData[
                    'phone'
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Prefer Email
        |--------------------------------------------------------------------------
        */

        if (
            $email
        ) {

            $guardian =
                Guardian::where(
                    'normalized_email',
                    $email
                )
                    ->whereRaw(
                        'LOWER(first_name) = ?',
                        [
                            mb_strtolower(
                                $guardianData[
                                    'first_name'
                                ]
                            ),
                        ]
                    )
                    ->whereRaw(
                        'LOWER(last_name) = ?',
                        [
                            mb_strtolower(
                                $guardianData[
                                    'last_name'
                                ]
                            ),
                        ]
                    )
                    ->first();


            if (
                $guardian
            ) {

                return $guardian;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Try Phone
        |--------------------------------------------------------------------------
        */

        if (
            $phone
        ) {

            $guardian =
                Guardian::where(
                    'normalized_phone',
                    $phone
                )
                    ->whereRaw(
                        'LOWER(first_name) = ?',
                        [
                            mb_strtolower(
                                $guardianData[
                                    'first_name'
                                ]
                            ),
                        ]
                    )
                    ->whereRaw(
                        'LOWER(last_name) = ?',
                        [
                            mb_strtolower(
                                $guardianData[
                                    'last_name'
                                ]
                            ),
                        ]
                    )
                    ->first();


            if (
                $guardian
            ) {

                return $guardian;
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Guardian Data
    |--------------------------------------------------------------------------
    */

    private function guardian(
        array $row,
        array $headers,
        string $prefix,
        string $relationship
    ): ?array {

        $firstName =
            trim(
                (string)
                $this->cell(
                    $row,
                    $headers,
                    $prefix
                    .
                    ' first name'
                )
            );


        $lastName =
            trim(
                (string)
                $this->cell(
                    $row,
                    $headers,
                    $prefix
                    .
                    ' last name'
                )
            );


        $email =
            trim(
                (string)
                $this->cell(
                    $row,
                    $headers,
                    $prefix
                    .
                    ' email'
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Try Several Phone Header Names
        |--------------------------------------------------------------------------
        */

        $phone =
            $this->firstExistingCell(
                $row,
                $headers,
                [

                    $prefix
                    .
                    ' cell/mobile phone',


                    $prefix
                    .
                    ' mobile phone',


                    $prefix
                    .
                    ' phone',


                    $prefix
                    .
                    ' home phone',
                ]
            );


        $phone =
            trim(
                (string)
                $phone
            );


        /*
         * No guardian information.
         */
        if (
            $firstName === ''
            &&
            $lastName === ''
            &&
            $email === ''
            &&
            $phone === ''
        ) {

            return null;
        }


        /*
         * Guardian must have a name.
         */
        if (
            $firstName === ''
            ||
            $lastName === ''
        ) {

            return null;
        }


        return [

            'relationship' =>
                $relationship,


            'first_name' =>
                $firstName,


            'last_name' =>
                $lastName,


            'email' =>
                $email !== ''
                    ? $email
                    : null,


            'phone' =>
                $phone !== ''
                    ? $phone
                    : null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | First Existing Cell
    |--------------------------------------------------------------------------
    */

    private function firstExistingCell(
        array $row,
        array $headers,
        array $headerNames
    ) {

        foreach (
            $headerNames
            as $headerName
        ) {

            $value =
                $this->cell(
                    $row,
                    $headers,
                    $headerName
                );


            if (
                $value !== null
                &&
                trim(
                    (string)
                    $value
                )
                !== ''
            ) {

                return $value;
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Cell
    |--------------------------------------------------------------------------
    */

    private function cell(
        array $row,
        array $headers,
        string $header
    ) {

        $header =
            $this->normalizeHeader(
                $header
            );


        if (
            !array_key_exists(
                $header,
                $headers
            )
        ) {

            return null;
        }


        return
            $row[
                $headers[
                    $header
                ]
            ]
            ??
            null;
    }


    /*
    |--------------------------------------------------------------------------
    | Header Normalisation
    |--------------------------------------------------------------------------
    */

    private function normalizeHeader(
        $header
    ): string {

        return mb_strtolower(
            trim(
                preg_replace(
                    '/\s+/',
                    ' ',
                    (string)
                    $header
                )
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Name Normalisation
    |--------------------------------------------------------------------------
    */

    public function normalizeName(
        string $name
    ): string {

        $name =
            mb_strtolower(
                $name
            );


        $name =
            preg_replace(
                '/[^a-z0-9]+/u',
                ' ',
                $name
            );


        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                $name
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Date
    |--------------------------------------------------------------------------
    */

    private function parseDate(
        $value
    ): ?string {

        if (
            $value === null
            ||
            trim(
                (string)
                $value
            )
            === ''
        ) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Excel Serial Date
        |--------------------------------------------------------------------------
        */

        if (
            is_numeric(
                $value
            )
        ) {

            try {

                return ExcelDate
                    ::excelToDateTimeObject(
                        $value
                    )
                    ->format(
                        'Y-m-d'
                    );

            } catch (\Throwable $e) {

                // Continue to text parsing.
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Known Formats
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'd/m/Y',
                'd-m-Y',
                'Y-m-d',
                'm/d/Y',
            ]
            as $format
        ) {

            try {

                $date =
                    Carbon::createFromFormat(
                        $format,
                        trim(
                            (string)
                            $value
                        )
                    );


                if (
                    $date !== false
                ) {

                    return $date
                        ->format(
                            'Y-m-d'
                        );
                }

            } catch (\Throwable $e) {

                //
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Final Fallback
        |--------------------------------------------------------------------------
        */

        try {

            return Carbon::parse(
                $value
            )
                ->format(
                    'Y-m-d'
                );

        } catch (\Throwable $e) {

            return null;
        }
    }
}
