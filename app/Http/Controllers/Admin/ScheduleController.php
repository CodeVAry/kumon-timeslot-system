<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Section;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\StudentLeave;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Schedule Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $context =
            $this->getDayContext(
                $request->input('day_id')
            );

        $days =
            $context['days'];

        $dayDates =
            $context['dayDates'];

        $selectedDay =
            $context['selectedDay'];

        $selectedDayId =
            $selectedDay?->id;

        $selectedDate =
            $selectedDay
                ? $dayDates[$selectedDay->id]
                : null;


        /*
        |--------------------------------------------------------------------------
        | No Active Day
        |--------------------------------------------------------------------------
        */

        if (!$selectedDay) {

            return view(
                'admin.schedule.index',
                [
                    'days' =>
                        $days,

                    'dayDates' =>
                        $dayDates,

                    'selectedDay' =>
                        null,

                    'selectedDayId' =>
                        null,

                    'selectedDate' =>
                        null,

                    'scheduleRows' =>
                        collect(),

                    'timeOfferings' =>
                        collect(),

                    'selectedTime' =>
                        null,

                    'selectedOffering' =>
                        null,

                    'previewEnrolments' =>
                        collect(),

                    'totalClasses' =>
                        0,

                    'totalStudents' =>
                        0,

                    'totalWishlist' =>
                        0,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Load Active Offerings
        |--------------------------------------------------------------------------
        */

        $offerings =
            SectionOffering::with([
                'section',
                'day',
            ])
                ->withCount([
                    'enrolments as allocated_seats' =>
                        function ($query) {

                            $query
                                ->where(
                                    'is_active',
                                    true
                                )
                                ->where(
                                    'is_wishlist',
                                    false
                                );
                        },

                    'enrolments as wishlist_count' =>
                        function ($query) {

                            $query
                                ->where(
                                    'is_active',
                                    true
                                )
                                ->where(
                                    'is_wishlist',
                                    true
                                );
                        },
                ])
                ->where(
                    'day_id',
                    $selectedDay->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->orderBy(
                    'start_time'
                )
                ->orderBy(
                    'section_id'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $offeringIds =
            $offerings->pluck('id');


        $totalClasses =
            $offerings->count();


        if ($offeringIds->isEmpty()) {

            $totalStudents = 0;

            $totalWishlist = 0;

        } else {

            $totalStudents =
                Enrolment::whereIn(
                    'section_offering_id',
                    $offeringIds
                )
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'is_wishlist',
                        false
                    )
                    ->distinct()
                    ->count(
                        'student_id'
                    );


            $totalWishlist =
                Enrolment::whereIn(
                    'section_offering_id',
                    $offeringIds
                )
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'is_wishlist',
                        true
                    )
                    ->count();
        }


        /*
        |--------------------------------------------------------------------------
        | Group By Start Time
        |--------------------------------------------------------------------------
        */

        $scheduleRows =
            collect();


        $groupedOfferings =
            $offerings->groupBy(
                function ($offering) {

                    return Carbon::parse(
                        $offering->start_time
                    )->format(
                        'H:i:s'
                    );
                }
            );


        foreach (
            $groupedOfferings
            as $rawTime => $group
        ) {

            $scheduleRows->push([

                'raw_time' =>
                    $rawTime,

                'time' =>
                    Carbon::parse(
                        $rawTime
                    )->format(
                        'g:i A'
                    ),

                'class_count' =>
                    $group->count(),

                'enrolment_count' =>
                    $group->sum(
                        'allocated_seats'
                    ),

                'wishlist_count' =>
                    $group->sum(
                        'wishlist_count'
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Selected Time
        |--------------------------------------------------------------------------
        */

        $selectedTime =
            $request->input(
                'time'
            );


        $validTimes =
            $scheduleRows
                ->pluck(
                    'raw_time'
                )
                ->toArray();


        if (
            !$selectedTime
            ||
            !in_array(
                $selectedTime,
                $validTimes
            )
        ) {

            $selectedTime =
                $scheduleRows
                    ->first()['raw_time']
                ??
                null;
        }


        /*
        |--------------------------------------------------------------------------
        | Offerings At Selected Time
        |--------------------------------------------------------------------------
        */

        $timeOfferings =
            $offerings
                ->filter(
                    function (
                        $offering
                    ) use (
                        $selectedTime
                    ) {

                        return
                            Carbon::parse(
                                $offering->start_time
                            )->format(
                                'H:i:s'
                            )
                            ===
                            $selectedTime;
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Selected Offering
        |--------------------------------------------------------------------------
        */

        $requestedOfferingId =
            (int)
            $request->input(
                'offering_id'
            );


        $selectedOffering =
            $timeOfferings
                ->firstWhere(
                    'id',
                    $requestedOfferingId
                );


        if (!$selectedOffering) {

            $selectedOffering =
                $timeOfferings
                    ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Student Preview
        |--------------------------------------------------------------------------
        */

        $previewEnrolments =
            collect();


        if ($selectedOffering) {

            $selectedOffering->load([
                'section',
                'day',

                'enrolments' =>
                    function ($query) {

                        $query
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'is_wishlist',
                                false
                            )
                            ->orderBy(
                                'student_id'
                            );
                    },

                'enrolments.student.studentStatus',

                'enrolments.student.guardians',
            ]);


            $previewEnrolments =
                $selectedOffering
                    ->enrolments
                    ->take(8);
        }


        return view(
            'admin.schedule.index',
            compact(
                'days',
                'dayDates',
                'selectedDay',
                'selectedDayId',
                'selectedDate',
                'scheduleRows',
                'timeOfferings',
                'selectedTime',
                'selectedOffering',
                'previewEnrolments',
                'totalClasses',
                'totalStudents',
                'totalWishlist'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Full Student List For One Class
    |--------------------------------------------------------------------------
    */

    public function classStudents(
        SectionOffering $sectionOffering
    ) {
        $sectionOffering->load([
            'section',
            'day',
        ]);


        $enrolments =
            Enrolment::with([
                'student.studentStatus',
                'student.guardians',
            ])
                ->where(
                    'section_offering_id',
                    $sectionOffering->id
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_wishlist',
                    false
                )
                ->orderBy(
                    'student_id'
                )
                ->paginate(20);


        $wishlistCount =
            Enrolment::where(
                'section_offering_id',
                $sectionOffering->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_wishlist',
                    true
                )
                ->count();


        return view(
            'admin.schedule.class-students',
            compact(
                'sectionOffering',
                'enrolments',
                'wishlistCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Print / Export Filter Page
    |--------------------------------------------------------------------------
    */

    public function exportForm(
        Request $request
    ) {
        $context =
            $this->getDayContext(
                $request->input(
                    'day_id'
                )
            );


        $days =
            $context['days'];


        $selectedDay =
            $context['selectedDay'];


        $selectedDate =
            $selectedDay
                ? $context[
                    'dayDates'
                ][$selectedDay->id]
                : null;


        /*
        |--------------------------------------------------------------------------
        | Active Sections
        |--------------------------------------------------------------------------
        */

        $sections =
            Section::where(
                'is_active',
                true
            )
                ->orderBy(
                    'section_name'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Offerings For Selected Day
        |--------------------------------------------------------------------------
        */

        $offerings =
            collect();


        if ($selectedDay) {

            $offerings =
                SectionOffering::with([
                    'section',
                    'day',
                ])
                    ->where(
                        'day_id',
                        $selectedDay->id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->orderBy(
                        'start_time'
                    )
                    ->orderBy(
                        'section_id'
                    )
                    ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Unique Class Times
        |--------------------------------------------------------------------------
        */

        $times =
            $offerings
                ->map(
                    function ($offering) {

                        return Carbon::parse(
                            $offering->start_time
                        )->format(
                            'H:i:s'
                        );
                    }
                )
                ->unique()
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Preselected Offering
        |--------------------------------------------------------------------------
        */

        $selectedOfferingId =
            $request->input(
                'offering_id'
            );


        return view(
            'admin.schedule.export',
            compact(
                'days',
                'selectedDay',
                'selectedDate',
                'sections',
                'offerings',
                'times',
                'selectedOfferingId'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Export
    |--------------------------------------------------------------------------
    */

    public function export(
        Request $request
    ) {
        $validated =
            $request->validate([

                'day_id' => [
                    'required',
                    'exists:days,id',
                ],

                'date' => [
                    'required',
                    'date',
                ],

                'section_id' => [
                    'nullable',
                    'exists:sections,id',
                ],

                'time' => [
                    'nullable',
                    'date_format:H:i:s',
                ],

                'offering_id' => [
                    'nullable',
                    'exists:section_offerings,id',
                ],

                'attendance_status' => [
                    'required',
                    'in:all,present,absent,vacation,not_marked',
                ],

                'format' => [
                    'required',
                    'in:pdf,excel',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Build Rows
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->buildExportRows(
                $validated
            );


        $date =
            Carbon::parse(
                $validated['date']
            );


        /*
        |--------------------------------------------------------------------------
        | File Name
        |--------------------------------------------------------------------------
        */

        $fileName =
            'kumon-class-list-'
            .
            $date->format(
                'Y-m-d'
            );


        /*
        |--------------------------------------------------------------------------
        | Excel / CSV
        |--------------------------------------------------------------------------
        |
        | No Excel package needed.
        |
        | CSV opens directly in Microsoft Excel.
        |--------------------------------------------------------------------------
        */

        if (
            $validated['format']
            ===
            'excel'
        ) {

            return $this->downloadCsv(
                $rows,
                $fileName
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $groupedRows =
            $rows->groupBy(
                function ($row) {

                    return
                        $row[
                            'offering_id'
                        ];
                }
            );


        $pdf =
            Pdf::loadView(
                'admin.schedule.export-pdf',
                [
                    'rows' =>
                        $rows,

                    'groupedRows' =>
                        $groupedRows,

                    'date' =>
                        $date,

                    'filters' =>
                        $validated,
                ]
            )
                ->setPaper(
                    'a4',
                    'landscape'
                );


        return $pdf->download(
            $fileName
            .
            '.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Build Export Rows
    |--------------------------------------------------------------------------
    */

    private function buildExportRows(
        array $filters
    ) {
        $date =
            Carbon::parse(
                $filters['date']
            )
                ->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | Matching Class Offerings
        |--------------------------------------------------------------------------
        */

        $offeringQuery =
            SectionOffering::with([
                'section',
                'day',
            ])
                ->where(
                    'day_id',
                    $filters['day_id']
                )
                ->where(
                    'is_active',
                    true
                );


        /*
        |--------------------------------------------------------------------------
        | Section Filter
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['section_id']
            )
        ) {

            $offeringQuery->where(
                'section_id',
                $filters['section_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Time Filter
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['time']
            )
        ) {

            $offeringQuery->whereTime(
                'start_time',
                $filters['time']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Specific Class Filter
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['offering_id']
            )
        ) {

            $offeringQuery->where(
                'id',
                $filters['offering_id']
            );
        }


        $offerings =
            $offeringQuery
                ->orderBy(
                    'start_time'
                )
                ->orderBy(
                    'section_id'
                )
                ->get();


        $offeringIds =
            $offerings
                ->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | No Matching Classes
        |--------------------------------------------------------------------------
        */

        if ($offeringIds->isEmpty()) {

            return collect();
        }


        /*
        |--------------------------------------------------------------------------
        | Confirmed Students
        |--------------------------------------------------------------------------
        */

        $enrolments =
            Enrolment::with([
                'student.studentStatus',
                'student.guardians',

                'sectionOffering.section',

                'sectionOffering.day',
            ])
                ->whereIn(
                    'section_offering_id',
                    $offeringIds
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_wishlist',
                    false
                )
                ->orderBy(
                    'section_offering_id'
                )
                ->orderBy(
                    'student_id'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Attendance Records
        |--------------------------------------------------------------------------
        */

        $attendanceRecords =
            Attendance::whereIn(
                'enrolment_id',
                $enrolments
                    ->pluck('id')
            )
                ->whereDate(
                    'attendance_date',
                    $date
                        ->toDateString()
                )
                ->get()
                ->keyBy(
                    'enrolment_id'
                );


        /*
        |--------------------------------------------------------------------------
        | Automatic Vacation Students
        |--------------------------------------------------------------------------
        */

        $studentIds =
            $enrolments
                ->pluck(
                    'student_id'
                )
                ->unique()
                ->values();


        $vacationStudentIds =
            $this->getVacationStudentIds(
                $studentIds,
                $date
            );


        /*
        |--------------------------------------------------------------------------
        | Final Rows
        |--------------------------------------------------------------------------
        */

        $rows =
            collect();


        foreach (
            $enrolments
            as $enrolment
        ) {

            $student =
                $enrolment->student;


            if (!$student) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            if (
                $vacationStudentIds
                    ->contains(
                        $student->id
                    )
            ) {

                $attendanceStatus =
                    'vacation';

            } else {

                $attendance =
                    $attendanceRecords
                        ->get(
                            $enrolment->id
                        );


                $attendanceStatus =
                    $attendance?->status
                    ??
                    'not_marked';
            }


            /*
            |--------------------------------------------------------------------------
            | Attendance Filter
            |--------------------------------------------------------------------------
            */

            if (
                $filters[
                    'attendance_status'
                ]
                !==
                'all'
                &&
                $attendanceStatus
                !==
                $filters[
                    'attendance_status'
                ]
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Primary Guardian
            |--------------------------------------------------------------------------
            */

            $guardian =
                $student
                    ->guardians
                    ->first(
                        function ($guardian) {

                            return
                                (bool)
                                $guardian
                                    ->pivot
                                    ->is_primary;
                        }
                    );


            if (!$guardian) {

                $guardian =
                    $student
                        ->guardians
                        ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | Offering
            |--------------------------------------------------------------------------
            */

            $offering =
                $enrolment
                    ->sectionOffering;


            /*
            |--------------------------------------------------------------------------
            | Row
            |--------------------------------------------------------------------------
            */

            $rows->push([

                'offering_id' =>
                    $offering?->id,

                'student_id' =>
                    $student
                        ->external_id
                    ??
                    '—',

                'student_name' =>
                    trim(
                        $student
                            ->first_name
                        .
                        ' '
                        .
                        $student
                            ->last_name
                    ),

                'guardian' =>
                    $guardian
                        ? trim(
                            $guardian
                                ->first_name
                            .
                            ' '
                            .
                            $guardian
                                ->last_name
                        )
                        : '—',

                'section' =>
                    $offering
                        ?->section
                        ?->section_name
                    ??
                    '—',

                'day' =>
                    $offering
                        ?->day
                        ?->day_name
                    ??
                    '—',

                'date' =>
                    $date->format(
                        'd M Y'
                    ),

                'start_time' =>
                    $offering
                        ? Carbon::parse(
                            $offering
                                ->start_time
                        )->format(
                            'g:i A'
                        )
                        : '—',

                'end_time' =>
                    $offering
                        ? Carbon::parse(
                            $offering
                                ->end_time
                        )->format(
                            'g:i A'
                        )
                        : '—',

                'student_status' =>
                    $student
                        ->studentStatus
                        ?->status_name
                    ??
                    '—',

                'attendance_status' =>
                    match (
                        $attendanceStatus
                    ) {

                        'present' =>
                            'Present',

                        'absent' =>
                            'Absent',

                        'vacation' =>
                            'Vacation',

                        default =>
                            'Not Marked',
                    },
            ]);
        }


        return $rows;
    }


    /*
    |--------------------------------------------------------------------------
    | Download Excel-Compatible CSV
    |--------------------------------------------------------------------------
    */

    private function downloadCsv(
        $rows,
        string $fileName
    ) {
        $headers = [

            'Content-Type' =>
                'text/csv; charset=UTF-8',

            'Content-Disposition' =>
                'attachment; filename="'
                .
                $fileName
                .
                '.csv"',
        ];


        $callback =
            function () use (
                $rows
            ) {

                $file =
                    fopen(
                        'php://output',
                        'w'
                    );


                /*
                 * UTF-8 BOM.
                 *
                 * Helps Excel display text correctly.
                 */
                fwrite(
                    $file,
                    "\xEF\xBB\xBF"
                );


                /*
                |--------------------------------------------------------------------------
                | Headings
                |--------------------------------------------------------------------------
                */

                fputcsv(
                    $file,
                    [
                        'Student ID',
                        'Student Name',
                        'Guardian',
                        'Class',
                        'Day',
                        'Date',
                        'Start Time',
                        'End Time',
                        'Student Status',
                        'Attendance',
                    ]
                );


                /*
                |--------------------------------------------------------------------------
                | Data
                |--------------------------------------------------------------------------
                */

                foreach (
                    $rows
                    as $row
                ) {

                    fputcsv(
                        $file,
                        [
                            $row[
                                'student_id'
                            ],

                            $row[
                                'student_name'
                            ],

                            $row[
                                'guardian'
                            ],

                            $row[
                                'section'
                            ],

                            $row[
                                'day'
                            ],

                            $row[
                                'date'
                            ],

                            $row[
                                'start_time'
                            ],

                            $row[
                                'end_time'
                            ],

                            $row[
                                'student_status'
                            ],

                            $row[
                                'attendance_status'
                            ],
                        ]
                    );
                }


                fclose(
                    $file
                );
            };


        return response()->stream(
            $callback,
            200,
            $headers
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Automatic Vacation Detection
    |--------------------------------------------------------------------------
    */

    private function getVacationStudentIds(
        $studentIds,
        Carbon $attendanceDate
    ) {
        if ($studentIds->isEmpty()) {

            return collect();
        }


        $date =
            $attendanceDate
                ->toDateString();


        return StudentLeave::whereIn(
            'student_id',
            $studentIds
        )

            /*
             * Approved leave or old admin leave
             * where status may still be NULL.
             */
            ->where(
                function ($query) {

                    $query
                        ->where(
                            'status',
                            'approved'
                        )
                        ->orWhereNull(
                            'status'
                        );
                }
            )

            /*
             * Leave started on/before
             * attendance date.
             */
            ->whereDate(
                'start_date',
                '<=',
                $date
            )

            /*
             * Effective leave end.
             */
            ->where(
                function ($query) use (
                    $date
                ) {

                    /*
                     * Not returned yet.
                     */
                    $query->where(
                        function ($query) use (
                            $date
                        ) {

                            $query
                                ->whereNull(
                                    'actual_return_date'
                                )
                                ->whereDate(
                                    'expected_return_date',
                                    '>=',
                                    $date
                                );
                        }
                    )

                    /*
                     * Returned early.
                     *
                     * Vacation only applies
                     * before actual return date.
                     */
                    ->orWhere(
                        function ($query) use (
                            $date
                        ) {

                            $query
                                ->whereNotNull(
                                    'actual_return_date'
                                )
                                ->whereDate(
                                    'actual_return_date',
                                    '>',
                                    $date
                                );
                        }
                    );
                }
            )
            ->pluck(
                'student_id'
            )
            ->unique()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Old Print Whole Day
    |--------------------------------------------------------------------------
    |
    | Existing button still works.
    | Now redirects to new filter page.
    |--------------------------------------------------------------------------
    */

    public function printDay(
        Request $request
    ) {
        return redirect()
            ->route(
                'admin.schedule.export.form',
                [
                    'day_id' =>
                        $request->input(
                            'day_id'
                        ),
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Old Print One Class
    |--------------------------------------------------------------------------
    |
    | Existing Print List button still works.
    |--------------------------------------------------------------------------
    */

    public function printClass(
        SectionOffering $sectionOffering
    ) {
        return redirect()
            ->route(
                'admin.schedule.export.form',
                [
                    'day_id' =>
                        $sectionOffering
                            ->day_id,

                    'offering_id' =>
                        $sectionOffering
                            ->id,
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Day Selection Helper
    |--------------------------------------------------------------------------
    */

    private function getDayContext(
        $requestedDayId = null
    ) {
        $days =
            Day::where(
                'is_active',
                true
            )
                ->orderBy(
                    'sort_order'
                )
                ->get();


        $today =
            now()
                ->startOfDay();


        $todayName =
            $today
                ->format('l');


        $dayDates = [];


        /*
        |--------------------------------------------------------------------------
        | Build Real Dates
        |--------------------------------------------------------------------------
        */

        foreach (
            $days
            as $day
        ) {

            if (
                strtolower(
                    $day
                        ->day_name
                )
                ===
                strtolower(
                    $todayName
                )
            ) {

                $date =
                    $today
                        ->copy();

            } else {

                $date =
                    $today
                        ->copy()
                        ->next(
                            $day
                                ->day_name
                        );
            }


            $dayDates[
                $day->id
            ] =
                $date;
        }


        /*
        |--------------------------------------------------------------------------
        | Today As Default
        |--------------------------------------------------------------------------
        */

        $defaultDay =
            $days->first(
                function (
                    $day
                ) use (
                    $todayName
                ) {

                    return
                        strtolower(
                            $day
                                ->day_name
                        )
                        ===
                        strtolower(
                            $todayName
                        );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Otherwise Nearest Day
        |--------------------------------------------------------------------------
        */

        if (
            !$defaultDay
            &&
            $days->isNotEmpty()
        ) {

            $defaultDay =
                $days
                    ->sortBy(
                        function (
                            $day
                        ) use (
                            $dayDates
                        ) {

                            return
                                $dayDates[
                                    $day->id
                                ]
                                    ->timestamp;
                        }
                    )
                    ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Requested Day
        |--------------------------------------------------------------------------
        */

        $selectedDay =
            $days->firstWhere(
                'id',
                (int)
                $requestedDayId
            );


        if (!$selectedDay) {

            $selectedDay =
                $defaultDay;
        }


        return [

            'days' =>
                $days,

            'dayDates' =>
                $dayDates,

            'selectedDay' =>
                $selectedDay,
        ];
    }
}
