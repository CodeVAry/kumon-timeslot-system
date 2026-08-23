<?php

namespace App\Services;

use App\Models\Admin\Attendance;
use App\Models\Admin\Student;
use Carbon\Carbon;

class StudentStatusReviewService
{
    /*
    |--------------------------------------------------------------------------
    | Get Absence Review Information
    |--------------------------------------------------------------------------
    |
    | Business rule:
    |
    | Active student continuously absent for
    | 30 days or more requires Admin review.
    |
    | present  = breaks absence period
    | vacation = breaks absence period
    | absent   = counts as absence
    |
    */

    public function getAbsenceInfo(
        Student $student
    ): ?array {
        /*
        |--------------------------------------------------------------------------
        | Active Confirmed Enrolments
        |--------------------------------------------------------------------------
        */

        $enrolmentIds =
            $student
                ->enrolments()
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_wishlist',
                    false
                )
                ->pluck(
                    'id'
                );


        if ($enrolmentIds->isEmpty()) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Attendance History
        |--------------------------------------------------------------------------
        */

        $attendanceRecords =
            Attendance::whereIn(
                'enrolment_id',
                $enrolmentIds
            )
                ->orderBy(
                    'attendance_date'
                )
                ->orderBy(
                    'id'
                )
                ->get();


        if ($attendanceRecords->isEmpty()) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Latest Attendance
        |--------------------------------------------------------------------------
        |
        | Removal review only applies if the
        | student's latest recorded attendance
        | is absent.
        |
        */

        $latestAttendance =
            $attendanceRecords->last();


        if (
            strtolower(
                $latestAttendance->status
            )
            !==
            'absent'
        ) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Find Start Of Current Continuous Absence
        |--------------------------------------------------------------------------
        |
        | Work backwards.
        |
        | Stop when we find:
        |
        | present
        | vacation
        |
        */

        $continuousAbsences =
            collect();


        foreach (
            $attendanceRecords
                ->reverse()
                ->values()
            as $attendance
        ) {

            $status =
                strtolower(
                    $attendance->status
                );


            if (
                $status
                !==
                'absent'
            ) {

                break;
            }


            $continuousAbsences->push(
                $attendance
            );
        }


        if (
            $continuousAbsences->isEmpty()
        ) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Absence Start Date
        |--------------------------------------------------------------------------
        */

        $firstAbsence =
            $continuousAbsences
                ->sortBy(
                    'attendance_date'
                )
                ->first();


        $absenceStart =
            Carbon::parse(
                $firstAbsence
                    ->attendance_date
            )
                ->startOfDay();


        $today =
            now()
                ->startOfDay();


        $absenceDays =
            $absenceStart
                ->diffInDays(
                    $today
                );


        return [
            'absence_start' =>
                $absenceStart,

            'absence_days' =>
                $absenceDays,

            'latest_attendance_date' =>
                Carbon::parse(
                    $latestAttendance
                        ->attendance_date
                ),

            'requires_review' =>
                $absenceDays >= 30,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Requires 30 Day Absence Review
    |--------------------------------------------------------------------------
    */

    public function requiresAbsenceReview(
        Student $student
    ): bool {
        $absenceInfo =
            $this->getAbsenceInfo(
                $student
            );


        if (!$absenceInfo) {

            return false;
        }


        return
            $absenceInfo[
                'requires_review'
            ];
    }


    /*
    |--------------------------------------------------------------------------
    | Inactive Student Information
    |--------------------------------------------------------------------------
    |
    | Business rule:
    |
    | Student inactive for 6 months becomes
    | eligible for deletion review.
    |
    | Warning starts one month before,
    | therefore after 5 months inactive.
    |
    */

    public function getInactiveInfo(
        Student $student
    ): ?array {
        if (
            !$student->inactive_since
        ) {

            return null;
        }


        $inactiveSince =
            Carbon::parse(
                $student->inactive_since
            )
                ->startOfDay();


        $today =
            now()
                ->startOfDay();


        /*
         * Six months after becoming inactive.
         */
        $deletionDate =
            $inactiveSince
                ->copy()
                ->addMonths(6);


        /*
         * Start warning one month before deletion.
         */
        $warningDate =
            $deletionDate
                ->copy()
                ->subMonth();


        $showWarning =
            $today
                ->greaterThanOrEqualTo(
                    $warningDate
                );


        $requiresDeletionReview =
            $today
                ->greaterThanOrEqualTo(
                    $deletionDate
                );


        $daysUntilDeletion =
            0;


        if (
            $today->lessThan(
                $deletionDate
            )
        ) {

            $daysUntilDeletion =
                $today
                    ->diffInDays(
                        $deletionDate
                    );
        }


        return [
            'inactive_since' =>
                $inactiveSince,

            'warning_date' =>
                $warningDate,

            'deletion_date' =>
                $deletionDate,

            'show_warning' =>
                $showWarning,

            'requires_deletion_review' =>
                $requiresDeletionReview,

            'days_until_deletion' =>
                $daysUntilDeletion,
        ];
    }
}
