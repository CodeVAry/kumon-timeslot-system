<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Student;
use App\Models\Admin\StudentStatus;
use App\Services\StudentStatusReviewService;
use Illuminate\Support\Facades\DB;

class StudentReviewController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Review Dashboard
    |--------------------------------------------------------------------------
    */

    public function index(
        StudentStatusReviewService $reviewService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Trial Status
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


        $trialStudents =
            collect();


        if ($trialStatus) {

            $trialStudents =
                Student::with([
                    'studentStatus',
                    'guardians',

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

                    'enrolments.sectionOffering.section',
                    'enrolments.sectionOffering.day',
                ])
                    ->where(
                        'student_status_id',
                        $trialStatus->id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->orderBy(
                        'status_changed_at'
                    )
                    ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Active Status
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


        $absenceReviews =
            collect();


        if ($activeStatus) {

            $activeStudents =
                Student::with([
                    'studentStatus',

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

                    'enrolments.sectionOffering.section',
                    'enrolments.sectionOffering.day',
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

                $absenceInfo =
                    $reviewService
                        ->getAbsenceInfo(
                            $student
                        );


                if (
                    $absenceInfo
                    &&
                    $absenceInfo[
                        'requires_review'
                    ]
                ) {

                    $absenceReviews->push([
                        'student' =>
                            $student,

                        'absence_start' =>
                            $absenceInfo[
                                'absence_start'
                            ],

                        'absence_days' =>
                            $absenceInfo[
                                'absence_days'
                            ],

                        'latest_attendance_date' =>
                            $absenceInfo[
                                'latest_attendance_date'
                            ],
                    ]);
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Inactive Students
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


        $deletionWarnings =
            collect();


        if ($inactiveStatus) {

            $inactiveStudents =
                Student::with([
                    'studentStatus',
                ])
                    ->where(
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


                if (
                    $inactiveInfo
                    &&
                    $inactiveInfo[
                        'show_warning'
                    ]
                ) {

                    $deletionWarnings->push([
                        'student' =>
                            $student,

                        'inactive_since' =>
                            $inactiveInfo[
                                'inactive_since'
                            ],

                        'deletion_date' =>
                            $inactiveInfo[
                                'deletion_date'
                            ],

                        'days_until_deletion' =>
                            $inactiveInfo[
                                'days_until_deletion'
                            ],

                        'requires_deletion_review' =>
                            $inactiveInfo[
                                'requires_deletion_review'
                            ],
                    ]);
                }
            }
        }


        return view(
            'admin.student-reviews.index',
            compact(
                'trialStudents',
                'absenceReviews',
                'deletionWarnings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Trial -> New
    |--------------------------------------------------------------------------
    */

    public function continueTrial(
        Student $student
    ) {
        $student->load(
            'studentStatus'
        );


        if (
            strtolower(
                $student
                    ->studentStatus
                    ?->status_name
                ??
                ''
            )
            !==
            'trial'
        ) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'This student is no longer in Trial status.'
                );
        }


        $newStatus =
            StudentStatus::whereRaw(
                'LOWER(status_name) = ?',
                [
                    'new',
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (!$newStatus) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'New status could not be found.'
                );
        }


        $student->update([
            'student_status_id' =>
                $newStatus->id,

            'is_active' =>
                true,

            'inactive_since' =>
                null,
        ]);


        return redirect()
            ->route(
                'admin.student-reviews.index'
            )
            ->with(
                'success',
                'Student will continue. Status changed from Trial to New.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Trial -> Inactive
    |--------------------------------------------------------------------------
    */

    public function rejectTrial(
        Student $student
    ) {
        $student->load(
            'studentStatus'
        );


        if (
            strtolower(
                $student
                    ->studentStatus
                    ?->status_name
                ??
                ''
            )
            !==
            'trial'
        ) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'This student is no longer in Trial status.'
                );
        }


        return $this->makeInactive(
            $student,
            'Student did not continue after Trial and was removed from active classes.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Keep Student After Absence Review
    |--------------------------------------------------------------------------
    |
    | Student remains Active.
    |
    | No database status change is needed.
    |
    | The warning naturally disappears when
    | attendance is recorded as Present again.
    |
    */

    public function keepAfterAbsence(
        Student $student
    ) {
        $student->load(
            'studentStatus'
        );


        if (
            strtolower(
                $student
                    ->studentStatus
                    ?->status_name
                ??
                ''
            )
            !==
            'active'
        ) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'This student is no longer Active.'
                );
        }


        return redirect()
            ->route(
                'admin.students.show',
                $student
            )
            ->with(
                'success',
                'Student has been kept active. The absence warning will clear when attendance is recorded as Present.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Remove Student After Absence Review
    |--------------------------------------------------------------------------
    */

    public function removeAfterAbsence(
        Student $student
    ) {
        $student->load(
            'studentStatus'
        );


        if (
            strtolower(
                $student
                    ->studentStatus
                    ?->status_name
                ??
                ''
            )
            !==
            'active'
        ) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'This student is no longer Active.'
                );
        }


        return $this->makeInactive(
            $student,
            'Student was removed from active classes and changed to Inactive.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Student
    |--------------------------------------------------------------------------
    |
    | Only allowed after six months inactive.
    |
    */

    public function deleteStudent(
        Student $student,
        StudentStatusReviewService $reviewService
    ) {
        $student->load(
            'studentStatus'
        );


        if (
            strtolower(
                $student
                    ->studentStatus
                    ?->status_name
                ??
                ''
            )
            !==
            'inactive'
        ) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'Only Inactive students can be deleted through this review.'
                );
        }


        $inactiveInfo =
            $reviewService
                ->getInactiveInfo(
                    $student
                );


        if (
            !$inactiveInfo
            ||
            !$inactiveInfo[
                'requires_deletion_review'
            ]
        ) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'This student has not yet been inactive for six months.'
                );
        }


        /*
         * WARNING:
         *
         * This is physical deletion.
         *
         * Your foreign-key rules determine
         * whether related records are deleted,
         * restricted, or detached.
         *
         * If deletion fails because historical
         * records exist, we should change this
         * to an archival approach instead.
         */

        $student->delete();


        return redirect()
            ->route(
                'admin.student-reviews.index'
            )
            ->with(
                'success',
                'Student was removed from the database.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Make-Inactive Method
    |--------------------------------------------------------------------------
    */

    private function makeInactive(
        Student $student,
        string $message
    ) {
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


        if (!$inactiveStatus) {

            return redirect()
                ->route(
                    'admin.student-reviews.index'
                )
                ->with(
                    'error',
                    'Inactive status could not be found.'
                );
        }


        DB::transaction(
            function () use (
                $student,
                $inactiveStatus
            ) {

                /*
                 * Deactivate active confirmed classes.
                 */
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
                    ->update([
                        'is_active' =>
                            false,
                    ]);


                /*
                 * Also deactivate active wishlist
                 * enrolments for an inactive student.
                 */
                $student
                    ->enrolments()
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'is_wishlist',
                        true
                    )
                    ->update([
                        'is_active' =>
                            false,
                    ]);


                $student->update([
                    'student_status_id' =>
                        $inactiveStatus->id,

                    'is_active' =>
                        false,

                    'inactive_since' =>
                        now()->toDateString(),
                ]);
            }
        );


        return redirect()
            ->route(
                'admin.student-reviews.index'
            )
            ->with(
                'success',
                $message
            );
    }
}
