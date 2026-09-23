<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\StudentLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Attendance Overview
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Working Days Only
        |--------------------------------------------------------------------------
        */

        $activeDayIds =
            SectionOffering::where(
                'is_active',
                true
            )
                ->pluck(
                    'day_id'
                )
                ->unique()
                ->values();


        $days =
            Day::where(
                'is_active',
                true
            )
                ->whereIn(
                    'id',
                    $activeDayIds
                )
                ->orderBy(
                    'sort_order'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Selected Attendance Date
        |--------------------------------------------------------------------------
        |
        | Admin can open today or any previous date.
        | Future dates are not accepted.
        |--------------------------------------------------------------------------
        */

        $today =
            now()
                ->startOfDay();


        $requestedDate =
            null;


        if (
            $request->filled(
                'date'
            )
        ) {

            try {

                $requestedDate =
                    Carbon::parse(
                        $request->input(
                            'date'
                        )
                    )
                        ->startOfDay();

            } catch (\Throwable $exception) {

                $requestedDate =
                    null;
            }
        }


        if (
            $requestedDate
            &&
            $requestedDate
                ->greaterThan(
                    $today
                )
        ) {

            return redirect()
                ->route(
                    'admin.attendance.index'
                )
                ->with(
                    'error',
                    'Attendance cannot be entered for a future date.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Selected Day + Date
        |--------------------------------------------------------------------------
        */

        $selectedDay =
            null;


        $selectedDate =
            null;


        if ($requestedDate) {

            $selectedDay =
                $days
                    ->first(
                        function ($day) use (
                            $requestedDate
                        ) {

                            return
                                strtolower(
                                    $day->day_name
                                )
                                ===
                                strtolower(
                                    $requestedDate
                                        ->format(
                                            'l'
                                        )
                                );
                        }
                    );


            if (!$selectedDay) {

                return redirect()
                    ->route(
                        'admin.attendance.index'
                    )
                    ->with(
                        'error',
                        $requestedDate->format(
                            'l, j F Y'
                        )
                        .
                        ' is not an active teaching day.'
                    );
            }


            $selectedDate =
                $requestedDate
                    ->copy();

        } else {

            $todayName =
                $today
                    ->format(
                        'l'
                    );


            $todayDay =
                $days
                    ->first(
                        function ($day) use (
                            $todayName
                        ) {

                            return
                                strtolower(
                                    $day->day_name
                                )
                                ===
                                strtolower(
                                    $todayName
                                );
                        }
                    );


            $requestedDayId =
                $request->input(
                    'day_id'
                );


            if ($requestedDayId) {

                $selectedDay =
                    $days
                        ->firstWhere(
                            'id',
                            (int)
                            $requestedDayId
                        );
            }


            if (!$selectedDay) {

                $selectedDay =
                    $todayDay
                    ??
                    $days
                        ->first();
            }


            if ($selectedDay) {

                if (
                    strtolower(
                        $selectedDay->day_name
                    )
                    ===
                    strtolower(
                        $todayName
                    )
                ) {

                    $selectedDate =
                        $today
                            ->copy();

                } else {

                    $selectedDate =
                        $today
                            ->copy()
                            ->next(
                                $selectedDay->day_name
                            );
                }
            }
        }


        $selectedDayId =
            $selectedDay
                ?->id;


        /*
        |--------------------------------------------------------------------------
        | Dates Used By Day Buttons
        |--------------------------------------------------------------------------
        */

        $dayDates =
            [];


        if ($requestedDate) {

            $weekStart =
                $selectedDate
                    ->copy()
                    ->startOfWeek(
                        Carbon::MONDAY
                    );


            $dayNameToIso = [
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6,
                'sunday' => 7,
            ];


            foreach (
                $days
                as $day
            ) {

                $isoDay =
                    $dayNameToIso[
                        strtolower(
                            $day->day_name
                        )
                    ]
                    ??
                    1;


                $dayDates[
                    $day->id
                ] =
                    $weekStart
                        ->copy()
                        ->addDays(
                            $isoDay
                            -
                            1
                        );
            }

        } else {

            foreach (
                $days
                as $day
            ) {

                if (
                    strtolower(
                        $day->day_name
                    )
                    ===
                    strtolower(
                        $today
                            ->format(
                                'l'
                            )
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
                                $day->day_name
                            );
                }


                $dayDates[
                    $day->id
                ] =
                    $date;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Active Offerings For Selected Day
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
                    ->get();
        }


        $offeringIds =
            $offerings
                ->pluck(
                    'id'
                );


        /*
        |--------------------------------------------------------------------------
        | Load Every Confirmed Enrolment For The Day
        |--------------------------------------------------------------------------
        */

        $dayEnrolments =
            collect();


        if (
            $offeringIds
                ->isNotEmpty()
        ) {

            $dayEnrolments =
                Enrolment::with([
                    'student.studentStatus',
                    'student.guardians',
                    'sectionOffering.section',
                    'sectionOffering.day',
                    'subSection',
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
                    ->whereHas(
                        'student',
                        function ($query) {

                            $query->where(
                                'is_active',
                                true
                            );
                        }
                    )
                    ->get();
        }


        $studentGroups =
            $dayEnrolments
                ->groupBy(
                    'student_id'
                );


        $studentIds =
            $studentGroups
                ->keys()
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Vacation Students
        |--------------------------------------------------------------------------
        */

        $vacationStudentIds =
            $selectedDate
                ? $this
                    ->getVacationStudentIds(
                        $studentIds,
                        $selectedDate
                    )
                : collect();


        /*
        |--------------------------------------------------------------------------
        | Existing Attendance Mapped To Student
        |--------------------------------------------------------------------------
        */

        $existingStatusByStudent =
            collect();


        if (
            $selectedDate
            &&
            $dayEnrolments
                ->isNotEmpty()
        ) {

            $enrolmentStudentMap =
                $dayEnrolments
                    ->pluck(
                        'student_id',
                        'id'
                    );


            $existingAttendance =
                Attendance::whereIn(
                    'enrolment_id',
                    $dayEnrolments
                        ->pluck(
                            'id'
                        )
                )
                    ->whereDate(
                        'attendance_date',
                        $selectedDate
                            ->toDateString()
                    )
                    ->get();


            $existingStatusByStudent =
                $existingAttendance
                    ->groupBy(
                        function ($attendance) use (
                            $enrolmentStudentMap
                        ) {

                            return
                                $enrolmentStudentMap[
                                    $attendance->enrolment_id
                                ]
                                ??
                                null;
                        }
                    )
                    ->filter(
                        fn ($items, $studentId) =>
                            !is_null(
                                $studentId
                            )
                    )
                    ->map(
                        function ($records) {

                            $statuses =
                                $records
                                    ->pluck(
                                        'status'
                                    )
                                    ->filter()
                                    ->values();


                            if (
                                $statuses
                                    ->contains(
                                        'vacation'
                                    )
                            ) {

                                return
                                    'vacation';
                            }


                            if (
                                $statuses
                                    ->contains(
                                        'absent'
                                    )
                            ) {

                                return
                                    'absent';
                            }


                            if (
                                $statuses
                                    ->contains(
                                        'present'
                                    )
                            ) {

                                return
                                    'present';
                            }


                            return null;
                        }
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Build One Attendance Row Per Student
        |--------------------------------------------------------------------------
        */

        $attendanceRows =
            $studentGroups
                ->map(
                    function (
                        $enrolments,
                        $studentId
                    ) use (
                        $vacationStudentIds,
                        $existingStatusByStudent
                    ) {

                        $student =
                            $enrolments
                                ->first()
                                ?->student;


                        if (!$student) {

                            return null;
                        }


                        $classes =
                            $enrolments
                                ->map(
                                    function ($enrolment) {

                                        $sectionName =
                                            $enrolment
                                                ->sectionOffering
                                                ?->section
                                                ?->section_name
                                            ??
                                            'Class';


                                        $subSectionName =
                                            $enrolment
                                                ->subSection
                                                ?->sub_section_name;


                                        if (
                                            $subSectionName
                                        ) {

                                            return
                                                $sectionName
                                                .
                                                ' - '
                                                .
                                                $subSectionName;
                                        }


                                        return
                                            $sectionName;
                                    }
                                )
                                ->filter()
                                ->unique()
                                ->values();


                        $times =
                            $enrolments
                                ->map(
                                    function ($enrolment) {

                                        $startTime =
                                            $enrolment
                                                ->sectionOffering
                                                ?->start_time;


                                        if (!$startTime) {

                                            return null;
                                        }


                                        return
                                            Carbon::parse(
                                                $startTime
                                            )->format(
                                                'g:i A'
                                            );
                                    }
                                )
                                ->filter()
                                ->unique()
                                ->values();


                        $isVacation =
                            $vacationStudentIds
                                ->contains(
                                    (int)
                                    $studentId
                                );


                        $status =
                            $isVacation
                                ? 'vacation'
                                : $existingStatusByStudent
                                    ->get(
                                        $studentId
                                    );


                        return [
                            'student' =>
                                $student,

                            'student_id' =>
                                (int)
                                $studentId,

                            'classes' =>
                                $classes,

                            'times' =>
                                $times,

                            'status' =>
                                $status,

                            'is_vacation' =>
                                $isVacation,

                            'enrolment_ids' =>
                                $enrolments
                                    ->pluck(
                                        'id'
                                    )
                                    ->values(),
                        ];
                    }
                )
                ->filter()
                ->sortBy(
                    function ($row) {

                        return
                            strtolower(
                                trim(
                                    $row[
                                        'student'
                                    ]->first_name
                                    .
                                    ' '
                                    .
                                    $row[
                                        'student'
                                    ]->last_name
                                )
                            );
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Summary Counts
        |--------------------------------------------------------------------------
        */

        $totalClasses =
            $offerings
                ->count();


        $studentsEnrolled =
            $attendanceRows
                ->count();


        $presentCount =
            $attendanceRows
                ->where(
                    'status',
                    'present'
                )
                ->count();


        $absentCount =
            $attendanceRows
                ->where(
                    'status',
                    'absent'
                )
                ->count();


        $vacationCount =
            $attendanceRows
                ->where(
                    'status',
                    'vacation'
                )
                ->count();


        $notMarkedCount =
            $attendanceRows
                ->filter(
                    fn ($row) =>
                        empty(
                            $row[
                                'status'
                            ]
                        )
                )
                ->count();


        return view(
            'admin.attendance.index',
            compact(
                'days',
                'dayDates',
                'selectedDay',
                'selectedDayId',
                'selectedDate',
                'offerings',
                'attendanceRows',
                'totalClasses',
                'studentsEnrolled',
                'presentCount',
                'absentCount',
                'vacationCount',
                'notMarkedCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Save Attendance For The Whole Day
    |--------------------------------------------------------------------------
    |
    | Staff marks each student once.
    |
    | The selected status is then copied to all of that student's confirmed
    | enrolments for this day. This keeps the existing attendances table and
    | all existing reports compatible while removing duplicate work for staff.
    |--------------------------------------------------------------------------
    */

    public function storeDay(
        Request $request,
        Day $day
    ) {
        $validated =
            $request->validate([
                'attendance_date' => [
                    'required',
                    'date',
                ],

                'attendance' => [
                    'nullable',
                    'array',
                ],

                'attendance.*' => [
                    'nullable',

                    Rule::in([
                        'present',
                        'absent',
                    ]),
                ],
            ]);


        $attendanceDate =
            Carbon::parse(
                $validated[
                    'attendance_date'
                ]
            )
                ->startOfDay();


        if (
            $attendanceDate
                ->greaterThan(
                    now()
                        ->startOfDay()
                )
        ) {

            throw ValidationException::withMessages([
                'attendance_date' =>
                    'Attendance cannot be entered for a future date.',
            ]);
        }


        if (
            strtolower(
                $attendanceDate
                    ->format(
                        'l'
                    )
            )
            !==
            strtolower(
                $day
                    ->day_name
            )
        ) {

            throw ValidationException::withMessages([
                'attendance_date' =>
                    'The selected date does not match the selected teaching day.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Valid Offerings For The Selected Day
        |--------------------------------------------------------------------------
        */

        $offeringIds =
            SectionOffering::where(
                'day_id',
                $day->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->pluck(
                    'id'
                );


        $validEnrolments =
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
                ->whereHas(
                    'student',
                    function ($query) {

                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )
                ->get([
                    'id',
                    'student_id',
                    'section_offering_id',
                ]);


        $studentGroups =
            $validEnrolments
                ->groupBy(
                    'student_id'
                );


        $vacationStudentIds =
            $this
                ->getVacationStudentIds(
                    $studentGroups
                        ->keys()
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->values(),
                    $attendanceDate
                );


        DB::transaction(
            function () use (
                $validated,
                $studentGroups,
                $vacationStudentIds,
                $attendanceDate
            ) {

                foreach (
                    $studentGroups
                    as $studentId =>
                        $studentEnrolments
                ) {

                    $studentId =
                        (int)
                        $studentId;


                    /*
                    |--------------------------------------------------------------------------
                    | Vacation Is Automatic
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $vacationStudentIds
                            ->contains(
                                $studentId
                            )
                    ) {

                        $status =
                            'vacation';

                    } else {

                        $status =
                            $validated[
                                'attendance'
                            ][
                                $studentId
                            ]
                            ??
                            null;


                        if (!$status) {

                            throw ValidationException::withMessages([
                                'attendance' =>
                                    'Please mark Present or Absent for every student who is not on vacation.',
                            ]);
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Apply The One Student Choice To Every Class Enrolment
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $studentEnrolments
                        as $enrolment
                    ) {

                        Attendance::updateOrCreate(
                            [
                                'enrolment_id' =>
                                    $enrolment->id,

                                'attendance_date' =>
                                    $attendanceDate
                                        ->toDateString(),
                            ],
                            [
                                'status' =>
                                    $status,
                            ]
                        );
                    }
                }
            }
        );


        return redirect()
            ->route(
                'admin.attendance.index',
                [
                    'day_id' =>
                        $day->id,

                    'date' =>
                        $attendanceDate
                            ->toDateString(),
                ]
            )
            ->with(
                'success',
                'Attendance saved for '
                .
                $attendanceDate->format(
                    'l, j F Y'
                )
                .
                '.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Take Attendance
    |--------------------------------------------------------------------------
    */

    public function takeAttendance(
        Request $request,
        SectionOffering $sectionOffering
    ) {
        $validated =
            $request->validate([
                'date' => [
                    'required',
                    'date',
                ],
            ]);


        $attendanceDate =
            Carbon::parse(
                $validated[
                    'date'
                ]
            )
                ->startOfDay();


        $sectionOffering->load([
            'section',
            'day',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Confirmed Active Enrolments
        |--------------------------------------------------------------------------
        */

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
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Students Automatically On Vacation
        |--------------------------------------------------------------------------
        |
        | An approved/current leave automatically makes attendance Vacation.
        |
        | If actual_return_date is null:
        |     leave remains active until expected_return_date.
        |
        | If actual_return_date exists:
        |     the actual return date itself is treated as a returned day,
        |     therefore Vacation only applies BEFORE actual_return_date.
        |--------------------------------------------------------------------------
        */

        $vacationStudentIds =
            $this->getVacationStudentIds(
                $enrolments
                    ->pluck(
                        'student_id'
                    )
                    ->unique()
                    ->values(),
                $attendanceDate
            );


        /*
        |--------------------------------------------------------------------------
        | Convert Student IDs To Enrolment IDs
        |--------------------------------------------------------------------------
        */

        $vacationEnrolmentIds =
            $enrolments
                ->filter(
                    function (
                        $enrolment
                    ) use (
                        $vacationStudentIds
                    ) {

                        return
                            $vacationStudentIds
                                ->contains(
                                    $enrolment
                                        ->student_id
                                );
                    }
                )
                ->pluck(
                    'id'
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Existing Attendance
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
                    $attendanceDate
                        ->toDateString()
                )
                ->get()
                ->keyBy(
                    'enrolment_id'
                );


        return view(
            'admin.attendance.takeAttendance',
            compact(
                'sectionOffering',
                'attendanceDate',
                'enrolments',
                'attendanceRecords',
                'vacationEnrolmentIds'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Save Attendance
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        SectionOffering $sectionOffering
    ) {
        $validated =
            $request->validate([

                'attendance_date' => [
                    'required',
                    'date',
                ],

                'attendance' => [
                    'required',
                    'array',
                ],

                'attendance.*' => [
                    'required',

                    Rule::in([
                        'present',
                        'absent',
                        'vacation',
                    ]),
                ],
            ]);


        $attendanceDate =
            Carbon::parse(
                $validated[
                    'attendance_date'
                ]
            )
                ->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | Get Valid Enrolments For This Class
        |--------------------------------------------------------------------------
        */

        $validEnrolments =
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
                    false
                )
                ->get([
                    'id',
                    'student_id',
                ]);


        /*
        |--------------------------------------------------------------------------
        | Automatically Determine Vacation Students
        |--------------------------------------------------------------------------
        */

        $vacationStudentIds =
            $this->getVacationStudentIds(
                $validEnrolments
                    ->pluck(
                        'student_id'
                    )
                    ->unique()
                    ->values(),
                $attendanceDate
            );


        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $validated,
                $validEnrolments,
                $vacationStudentIds,
                $attendanceDate
            ) {

                foreach (
                    $validEnrolments
                    as $enrolment
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Force Vacation From Leave
                    |--------------------------------------------------------------------------
                    |
                    | Even if someone manually changes the HTML request,
                    | an approved/current leave cannot be changed to Present
                    | or Absent.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $vacationStudentIds
                            ->contains(
                                $enrolment
                                    ->student_id
                            )
                    ) {

                        $status =
                            'vacation';

                    } else {

                        $status =
                            $validated[
                                'attendance'
                            ][
                                $enrolment->id
                            ]
                            ?? null;


                        /*
                         * Backend protection.
                         * Every non-vacation student
                         * must still have attendance.
                         */
                        if (!$status) {

                            throw ValidationException::withMessages([
                                'attendance' =>
                                    'Please mark attendance for every student.',
                            ]);
                        }
                    }


                    Attendance::updateOrCreate(
                        [
                            'enrolment_id' =>
                                $enrolment->id,

                            'attendance_date' =>
                                $attendanceDate
                                    ->toDateString(),
                        ],
                        [
                            'status' =>
                                $status,
                        ]
                    );
                }
            }
        );


        return redirect()
            ->route(
                'admin.attendance.takeAttendance',
                [
                    'sectionOffering' =>
                        $sectionOffering->id,

                    'date' =>
                        $attendanceDate
                            ->toDateString(),
                ]
            )
            ->with(
                'success',
                'Attendance saved successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Students On Vacation
    |--------------------------------------------------------------------------
    |
    | Only approved leave is used.
    |
    | NULL status is also accepted for older/admin-created leave records
    | created before leave approval status was introduced.
    |--------------------------------------------------------------------------
    */

    private function getVacationStudentIds(
        $studentIds,
        Carbon $attendanceDate
    ) {
        if (
            $studentIds
                ->isEmpty()
        ) {

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
             * Do not use Pending, Rejected
             * or Cancelled parent requests.
             */
            ->where(
                function ($query) {

                    $query
                        ->where(
                            'status',
                            'approved'
                        )

                        /*
                         * Supports older/admin-created
                         * leave rows if status was NULL.
                         */
                        ->orWhereNull(
                            'status'
                        );
                }
            )

            /*
             * Leave must already have started.
             */
            ->whereDate(
                'start_date',
                '<=',
                $date
            )

            /*
             * Determine the effective end.
             */
            ->where(
                function ($query) use (
                    $date
                ) {

                    /*
                     * No actual return yet:
                     * use expected return date.
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
                     * Returned already:
                     * vacation applies only before
                     * the return date.
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
}
