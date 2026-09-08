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
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        StudentStatusReviewService $reviewService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Real Current Date / Time
        |--------------------------------------------------------------------------
        */

        $now =
            now();


        /*
        |--------------------------------------------------------------------------
        | Selected Dashboard Date
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'date'
            )
        ) {

            try {

                $dashboardDate =
                    Carbon::parse(
                        $request->input(
                            'date'
                        )
                    )
                        ->startOfDay();

            } catch (\Exception $exception) {

                $dashboardDate =
                    $now
                        ->copy()
                        ->startOfDay();
            }

        } else {

            $dashboardDate =
                $now
                    ->copy()
                    ->startOfDay();
        }


        $isToday =
            $dashboardDate
                ->isSameDay(
                    $now
                );


        $selectedDayName =
            $dashboardDate
                ->format(
                    'l'
                );


        $currentTime =
            $isToday
                ? $now->format(
                    'H:i:s'
                )
                : null;


        /*
        |--------------------------------------------------------------------------
        | Working Days
        |--------------------------------------------------------------------------
        |
        | Only active days that actually have active offerings.
        |--------------------------------------------------------------------------
        */

        $workingDays =
            Day::where(
                'is_active',
                true
            )
                ->whereIn(
                    'id',
                    SectionOffering::query()
                        ->where(
                            'is_active',
                            true
                        )
                        ->select(
                            'day_id'
                        )
                        ->distinct()
                )
                ->orderBy(
                    'sort_order'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Previous Working Date
        |--------------------------------------------------------------------------
        */

        $previousScheduleDate =
            null;


        for (
            $i = 1;
            $i <= 7;
            $i++
        ) {

            $candidateDate =
                $dashboardDate
                    ->copy()
                    ->subDays(
                        $i
                    );


            $isWorkingDay =
                $workingDays
                    ->contains(
                        function ($day) use (
                            $candidateDate
                        ) {

                            return
                                strtolower(
                                    $day
                                        ->day_name
                                )
                                ===
                                strtolower(
                                    $candidateDate
                                        ->format(
                                            'l'
                                        )
                                );
                        }
                    );


            if ($isWorkingDay) {

                $previousScheduleDate =
                    $candidateDate;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Next Working Date
        |--------------------------------------------------------------------------
        */

        $nextScheduleDate =
            null;


        for (
            $i = 1;
            $i <= 7;
            $i++
        ) {

            $candidateDate =
                $dashboardDate
                    ->copy()
                    ->addDays(
                        $i
                    );


            $isWorkingDay =
                $workingDays
                    ->contains(
                        function ($day) use (
                            $candidateDate
                        ) {

                            return
                                strtolower(
                                    $day
                                        ->day_name
                                )
                                ===
                                strtolower(
                                    $candidateDate
                                        ->format(
                                            'l'
                                        )
                                );
                        }
                    );


            if ($isWorkingDay) {

                $nextScheduleDate =
                    $candidateDate;

                break;
            }
        }


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
        | Absences For Selected Date
        |--------------------------------------------------------------------------
        */

        $absentAttendance =
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
            $absentAttendance
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
        | Find Selected Day
        |--------------------------------------------------------------------------
        */

        $selectedDay =
            Day::where(
                'day_name',
                $selectedDayName
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Classes For Selected Day
        |--------------------------------------------------------------------------
        */

        if ($selectedDay) {

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


            /*
            |--------------------------------------------------------------------------
            | Students For Selected Day
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
            | Build Class Table
            |--------------------------------------------------------------------------
            */

            foreach (
                $groupedOfferings
                as $startTime =>
                $classOfferings
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

                if (!$isToday) {

                    if (
                        $dashboardDate
                            ->isBefore(
                                $now
                                    ->copy()
                                    ->startOfDay()
                            )
                    ) {

                        $status =
                            'Completed';

                    } else {

                        $status =
                            'Scheduled';
                    }

                } else {

                    if (
                        $now
                            ->greaterThanOrEqualTo(
                                $startDateTime
                            )
                        &&
                        $now
                            ->lessThan(
                                $endDateTime
                            )
                    ) {

                        $status =
                            'In Progress';

                    } elseif (
                        $now
                            ->greaterThanOrEqualTo(
                                $endDateTime
                            )
                    ) {

                        $status =
                            'Completed';

                    } elseif (
                        $now
                            ->lessThan(
                                $startDateTime
                            )
                        &&
                        $now
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
                }


                /*
                |--------------------------------------------------------------------------
                | Add Table Row
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
            |
            | Only applies to actual today.
            |--------------------------------------------------------------------------
            */

            if ($isToday) {

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


            $manualStudentId =
                $student
                    ->external_id
                ??
                'No Student ID';


            $expectedReturn =
                $earlyReturn
                    ->expected_return_date
                    ?->format(
                        'd M Y'
                    )
                ??
                '—';


            $actualReturn =
                $earlyReturn
                    ->actual_return_date
                    ?->format(
                        'd M Y'
                    )
                ??
                '—';


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
                'isToday',
                'previousScheduleDate',
                'nextScheduleDate',
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
