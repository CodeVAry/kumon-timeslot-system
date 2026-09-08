<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use App\Models\Admin\StudentLeave;
use App\Models\Admin\StudentStatus;
use App\Services\StudentStatusReviewService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(
        StudentStatusReviewService $reviewService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Current Date / Time
        |--------------------------------------------------------------------------
        */

        $dashboardDate =
            now();


        $todayName =
            $dashboardDate
                ->format(
                    'l'
                );


        $currentTime =
            $dashboardDate
                ->format(
                    'H:i:s'
                );


        /*
        |--------------------------------------------------------------------------
        | Default Dashboard Values
        |--------------------------------------------------------------------------
        */

        $studentsToday =
            0;


        $currentlyAttending =
            0;


        $absentToday =
            0;


        $wishlistCount =
            0;


        $currentClassTime =
            null;


        $currentClassStudents =
            0;


        $nextClassTime =
            null;


        $nextClassStudents =
            0;


        $todayClasses =
            collect();


        $absentStudents =
            collect();


        $adminReminders =
            collect();


        /*
        |--------------------------------------------------------------------------
        | Overall Wishlist Count
        |--------------------------------------------------------------------------
        */

        $wishlistCount =
            Enrolment::where(
                'is_active',
                true
            )
                ->where(
                    'is_wishlist',
                    true
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | Today's Actual Absences
        |--------------------------------------------------------------------------
        */

        $todayAbsentAttendance =
            Attendance::with([
                'enrolment.student',
            ])
                ->whereDate(
                    'attendance_date',
                    $dashboardDate
                        ->toDateString()
                )
                ->where(
                    'status',
                    'absent'
                )
                ->get();


        $absentStudents =
            $todayAbsentAttendance
                ->map(
                    function ($attendance) {

                        return
                            $attendance
                                ->enrolment
                                ?->student;
                    }
                )
                ->filter()
                ->unique(
                    'id'
                )
                ->values();


        $absentToday =
            $absentStudents
                ->count();


        /*
        |--------------------------------------------------------------------------
        | Find Today's Day
        |--------------------------------------------------------------------------
        */

        $todayDay =
            Day::where(
                'day_name',
                $todayName
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Today's Classes
        |--------------------------------------------------------------------------
        */

        if ($todayDay) {

            $offerings =
                SectionOffering::with([
                    'section',

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
                                );
                        },
                ])
                    ->where(
                        'day_id',
                        $todayDay->id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->orderBy(
                        'start_time'
                    )
                    ->get();


            /*
            |--------------------------------------------------------------------------
            | Students Today
            |--------------------------------------------------------------------------
            */

            $studentsToday =
                $offerings
                    ->flatMap(
                        function ($offering) {

                            return
                                $offering
                                    ->enrolments
                                    ->pluck(
                                        'student_id'
                                    );
                        }
                    )
                    ->unique()
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | Group Classes By Start Time
            |--------------------------------------------------------------------------
            */

            $groupedOfferings =
                $offerings
                    ->groupBy(
                        function ($offering) {

                            return
                                Carbon::parse(
                                    $offering
                                        ->start_time
                                )
                                    ->format(
                                        'H:i:s'
                                    );
                        }
                    );


            /*
            |--------------------------------------------------------------------------
            | Build Today's Class Table
            |--------------------------------------------------------------------------
            */

            foreach (
                $groupedOfferings
                as $startTime => $classOfferings
            ) {

                $startDateTime =
                    Carbon::parse(
                        $dashboardDate
                            ->format(
                                'Y-m-d'
                            )
                        .
                        ' '
                        .
                        $startTime
                    );


                $latestEndTime =
                    $classOfferings
                        ->max(
                            'end_time'
                        );


                $endDateTime =
                    Carbon::parse(
                        $dashboardDate
                            ->format(
                                'Y-m-d'
                            )
                        .
                        ' '
                        .
                        $latestEndTime
                    );


                /*
                |--------------------------------------------------------------------------
                | Student Groups
                |--------------------------------------------------------------------------
                */

                $allStudentIds =
                    collect();


                $regularStudentIds =
                    collect();


                $interactiveStudentIds =
                    collect();


                foreach (
                    $classOfferings
                    as $offering
                ) {

                    $studentIds =
                        $offering
                            ->enrolments
                            ->pluck(
                                'student_id'
                            );


                    $allStudentIds =
                        $allStudentIds
                            ->merge(
                                $studentIds
                            );


                    $sectionName =
                        strtolower(
                            trim(
                                $offering
                                    ->section
                                    ?->section_name
                                ??
                                ''
                            )
                        );


                    if (
                        $sectionName
                        ===
                        'interactive'
                    ) {

                        $interactiveStudentIds =
                            $interactiveStudentIds
                                ->merge(
                                    $studentIds
                                );

                    } else {

                        $regularStudentIds =
                            $regularStudentIds
                                ->merge(
                                    $studentIds
                                );
                    }
                }


                $regularStudents =
                    $regularStudentIds
                        ->unique()
                        ->count();


                $interactiveStudents =
                    $interactiveStudentIds
                        ->unique()
                        ->count();


                $totalStudents =
                    $allStudentIds
                        ->unique()
                        ->count();


                /*
                |--------------------------------------------------------------------------
                | Class Status
                |--------------------------------------------------------------------------
                */

                if (
                    $dashboardDate
                        ->greaterThanOrEqualTo(
                            $startDateTime
                        )
                    &&
                    $dashboardDate
                        ->lessThan(
                            $endDateTime
                        )
                ) {

                    $status =
                        'In Progress';

                } elseif (
                    $dashboardDate
                        ->greaterThanOrEqualTo(
                            $endDateTime
                        )
                ) {

                    $status =
                        'Completed';

                } elseif (
                    $dashboardDate
                        ->lessThan(
                            $startDateTime
                        )
                    &&
                    $dashboardDate
                        ->diffInMinutes(
                            $startDateTime
                        )
                    <=
                    30
                ) {

                    $status =
                        'Starting Soon';

                } else {

                    $status =
                        'Upcoming';
                }


                /*
                |--------------------------------------------------------------------------
                | Add Dashboard Table Row
                |--------------------------------------------------------------------------
                */

                $todayClasses->push([

                    'raw_time' =>
                        $startTime,

                    'class_time' =>
                        $startDateTime
                            ->format(
                                'g:i A'
                            ),

                    'regular_students' =>
                        $regularStudents,

                    'interactive_students' =>
                        $interactiveStudents,

                    'total_students' =>
                        $totalStudents,

                    'status' =>
                        $status,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Current Class
            |--------------------------------------------------------------------------
            */

            $currentClass =
                $todayClasses
                    ->first(
                        function ($class) {

                            return
                                $class[
                                    'status'
                                ]
                                ===
                                'In Progress';
                        }
                    );


            if ($currentClass) {

                $currentClassTime =
                    $currentClass[
                        'class_time'
                    ];


                $currentClassStudents =
                    $currentClass[
                        'total_students'
                    ];


                $currentlyAttending =
                    $currentClassStudents;
            }


            /*
            |--------------------------------------------------------------------------
            | Next Class
            |--------------------------------------------------------------------------
            */

            $nextClass =
                $todayClasses
                    ->first(
                        function ($class) use (
                            $currentTime
                        ) {

                            return
                                $class[
                                    'raw_time'
                                ]
                                >
                                $currentTime;
                        }
                    );


            if ($nextClass) {

                $nextClassTime =
                    $nextClass[
                        'class_time'
                    ];


                $nextClassStudents =
                    $nextClass[
                        'total_students'
                    ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Reminder 1
        | Trial Students
        |--------------------------------------------------------------------------
        */

        $trialStatus =
            StudentStatus::whereRaw(
                'LOWER(status_name) = ?',
                [
                    'trial',
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        $trialReviewCount =
            0;


        if ($trialStatus) {

            $trialReviewCount =
                Student::where(
                    'student_status_id',
                    $trialStatus->id
                )
                    ->where(
                        'is_active',
                        true
                    )
                    ->count();
        }


        if (
            $trialReviewCount > 0
        ) {

            $adminReminders->push([

                'type' =>
                    'trial',

                'title' =>
                    'Trial student review required',

                'message' =>
                    $trialReviewCount
                    .
                    ' Trial student(s) are waiting for an Admin decision.',

                'url' =>
                    route(
                        'admin.student-reviews.index'
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Reminder 2
        | 30-Day Absence Review
        |--------------------------------------------------------------------------
        */

        $activeStatus =
            StudentStatus::whereRaw(
                'LOWER(status_name) = ?',
                [
                    'active',
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        $absenceReviewCount =
            0;


        if ($activeStatus) {

            $activeStudents =
                Student::with([
                    'enrolments',
                ])
                    ->where(
                        'student_status_id',
                        $activeStatus->id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->get();


            foreach (
                $activeStudents
                as $student
            ) {

                if (
                    $reviewService
                        ->requiresAbsenceReview(
                            $student
                        )
                ) {

                    $absenceReviewCount++;
                }
            }
        }


        if (
            $absenceReviewCount > 0
        ) {

            $adminReminders->push([

                'type' =>
                    'absence',

                'title' =>
                    'Student removal review required',

                'message' =>
                    $absenceReviewCount
                    .
                    ' student(s) have been absent for at least 30 days.',

                'url' =>
                    route(
                        'admin.student-reviews.index'
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Reminder 3
        | Inactive Student Warning
        |--------------------------------------------------------------------------
        */

        $inactiveStatus =
            StudentStatus::whereRaw(
                'LOWER(status_name) = ?',
                [
                    'inactive',
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        $inactiveWarningCount =
            0;


        $deletionReviewCount =
            0;


        if ($inactiveStatus) {

            $inactiveStudents =
                Student::where(
                    'student_status_id',
                    $inactiveStatus->id
                )
                    ->whereNotNull(
                        'inactive_since'
                    )
                    ->get();


            foreach (
                $inactiveStudents
                as $student
            ) {

                $inactiveInfo =
                    $reviewService
                        ->getInactiveInfo(
                            $student
                        );


                if (!$inactiveInfo) {

                    continue;
                }


                if (
                    $inactiveInfo[
                        'requires_deletion_review'
                    ]
                ) {

                    $deletionReviewCount++;

                } elseif (
                    $inactiveInfo[
                        'show_warning'
                    ]
                ) {

                    $inactiveWarningCount++;
                }
            }
        }


        if (
            $inactiveWarningCount > 0
        ) {

            $adminReminders->push([

                'type' =>
                    'inactive-warning',

                'title' =>
                    'Inactive student deletion warning',

                'message' =>
                    $inactiveWarningCount
                    .
                    ' inactive student(s) are approaching six months inactive.',

                'url' =>
                    route(
                        'admin.student-reviews.index'
                    ),
            ]);
        }


        if (
            $deletionReviewCount > 0
        ) {

            $adminReminders->push([

                'type' =>
                    'deletion',

                'title' =>
                    'Student deletion review required',

                'message' =>
                    $deletionReviewCount
                    .
                    ' inactive student(s) have reached six months and require review.',

                'url' =>
                    route(
                        'admin.student-reviews.index'
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Reminder 4
        | Parent Early Return
        |--------------------------------------------------------------------------
        |
        | A parent can record an early return from
        | an approved leave.
        |
        | The dashboard will show early returns
        | recorded within the last 7 days.
        |
        */

        $recentEarlyReturns =
            StudentLeave::with([
                'student',
            ])
                ->where(
                    'status',
                    'approved'
                )
                ->where(
                    'returned_early',
                    true
                )
                ->whereNotNull(
                    'actual_return_date'
                )
                ->whereDate(
                    'actual_return_date',
                    '>=',
                    now()
                        ->subDays(7)
                        ->toDateString()
                )
                ->orderByDesc(
                    'actual_return_date'
                )
                ->get();


        foreach (
            $recentEarlyReturns
            as $earlyReturn
        ) {

            $student =
                $earlyReturn
                    ->student;


            if (!$student) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Student Name
            |--------------------------------------------------------------------------
            */

            $studentName =
                trim(
                    $student
                        ->first_name
                    .
                    ' '
                    .
                    $student
                        ->last_name
                );


            /*
            |--------------------------------------------------------------------------
            | Manual Student ID
            |--------------------------------------------------------------------------
            |
            | Do NOT use the database student ID.
            |
            */

            $manualStudentId =
                $student
                    ->external_id
                ??
                'No Student ID';


            /*
            |--------------------------------------------------------------------------
            | Expected Return
            |--------------------------------------------------------------------------
            */

            $expectedReturn =
                $earlyReturn
                    ->expected_return_date
                    ?->format(
                        'd M Y'
                    )
                ??
                '—';


            /*
            |--------------------------------------------------------------------------
            | Actual Return
            |--------------------------------------------------------------------------
            */

            $actualReturn =
                $earlyReturn
                    ->actual_return_date
                    ?->format(
                        'd M Y'
                    )
                ??
                '—';


            /*
            |--------------------------------------------------------------------------
            | Push Dashboard Reminder
            |--------------------------------------------------------------------------
            */

            $adminReminders->push([

                'type' =>
                    'early-return',

                'title' =>
                    'Student returned early',

                'message' =>
                    $studentName
                    .
                    ' ('
                    .
                    $manualStudentId
                    .
                    ') returned early on '
                    .
                    $actualReturn
                    .
                    '. Expected return was '
                    .
                    $expectedReturn
                    .
                    '.',

                'url' =>
                    route(
                        'admin.student-leaves.show',
                        $earlyReturn
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard',
            compact(
                'dashboardDate',
                'studentsToday',
                'currentlyAttending',
                'absentToday',
                'wishlistCount',
                'currentClassTime',
                'currentClassStudents',
                'nextClassTime',
                'nextClassStudents',
                'todayClasses',
                'absentStudents',
                'adminReminders'
            )
        );
    }
}
