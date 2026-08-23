<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Guardian;
use App\Models\Admin\Student;
use App\Models\Admin\StudentLeave;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Parent Email
    |--------------------------------------------------------------------------
    */

    private function getParentEmail()
    {
        return session(
            'parent_auth_email'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Guardian IDs For Parent Email
    |--------------------------------------------------------------------------
    */

    private function getParentGuardianIds()
    {
        $email =
            $this->getParentEmail();


        if (!$email) {

            return collect();
        }


        return Guardian::where(
            'is_active',
            true
        )
            ->where(
                'normalized_email',
                $email
            )
            ->pluck(
                'id'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Selected Student
    |--------------------------------------------------------------------------
    |
    | Do not use only:
    |
    | $guardian->students()
    |
    | because the same parent email may exist
    | on multiple Guardian records.
    |
    */

    private function getSelectedStudent()
    {
        $studentId =
            session(
                'parent_student_id'
            );


        if (!$studentId) {

            return null;
        }


        $guardianIds =
            $this->getParentGuardianIds();


        if ($guardianIds->isEmpty()) {

            return null;
        }


        return Student::where(
            'id',
            $studentId
        )
            ->where(
                'is_active',
                true
            )
            ->whereHas(
                'guardians',
                function ($query) use (
                    $guardianIds
                ) {

                    $query->whereIn(
                        'guardians.id',
                        $guardianIds
                    );
                }
            )
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Welcome Page
    |--------------------------------------------------------------------------
    */

    public function welcome()
    {
        $guardian =
            Auth::guard(
                'parent'
            )->user();


        if (!$guardian) {

            return redirect()
                ->route(
                    'parent.login'
                );
        }


        $guardianIds =
            $this->getParentGuardianIds();


        if ($guardianIds->isEmpty()) {

            Auth::guard(
                'parent'
            )->logout();


            session()->forget([
                'parent_auth_email',
                'parent_student_id',
            ]);


            return redirect()
                ->route(
                    'parent.login'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Get ALL Students
        |--------------------------------------------------------------------------
        |
        | Get students connected to any Guardian row
        | that uses this parent email.
        |
        */

        $students =
            Student::where(
                'is_active',
                true
            )
                ->whereHas(
                    'guardians',
                    function ($query) use (
                        $guardianIds
                    ) {

                        $query->whereIn(
                            'guardians.id',
                            $guardianIds
                        );
                    }
                )
                ->orderBy(
                    'first_name'
                )
                ->orderBy(
                    'last_name'
                )
                ->get();


        return view(
            'parent.welcome',
            compact(
                'guardian',
                'students'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Select Student
    |--------------------------------------------------------------------------
    */

    public function selectStudent(
        Student $student
    ) {
        $guardianIds =
            $this->getParentGuardianIds();


        if ($guardianIds->isEmpty()) {

            abort(403);
        }


        /*
         * Security:
         * selected student must belong
         * to one of the matching Guardian records.
         */
        $hasStudent =
            Student::where(
                'id',
                $student->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->whereHas(
                    'guardians',
                    function ($query) use (
                        $guardianIds
                    ) {

                        $query->whereIn(
                            'guardians.id',
                            $guardianIds
                        );
                    }
                )
                ->exists();


        if (!$hasStudent) {

            abort(403);
        }


        session([
            'parent_student_id' =>
                $student->id,
        ]);


        return redirect()
            ->route(
                'parent.dashboard'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Parent Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | Authenticated Parent
        |--------------------------------------------------------------------------
        */

        $guardian =
            Auth::guard(
                'parent'
            )->user();


        if (!$guardian) {

            return redirect()
                ->route(
                    'parent.login'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Selected Student
        |--------------------------------------------------------------------------
        */

        $student =
            $this->getSelectedStudent();


        if (!$student) {

            session()->forget(
                'parent_student_id'
            );


            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Active Confirmed Classes
        |--------------------------------------------------------------------------
        */

        $enrolments =
            Enrolment::with([
                'sectionOffering.section',
                'sectionOffering.day',
            ])
                ->where(
                    'student_id',
                    $student->id
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
                    'id'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Latest Wishlist Activity
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Do NOT filter:
        |
        | is_active = true
        | is_wishlist = true
        |
        | because:
        |
        | Rejected / Cancelled:
        | is_active may be false.
        |
        | Approved:
        | is_wishlist becomes false.
        |
        | wishlist_status is what identifies
        | wishlist request/history records.
        |
        */

        $latestWishlist =
            Enrolment::with([
                'sectionOffering.section',
                'sectionOffering.day',

                'wishlistForEnrolment.sectionOffering.section',
                'wishlistForEnrolment.sectionOffering.day',

                'reviewedBy',
            ])
                ->where(
                    'student_id',
                    $student->id
                )
                ->whereIn(
                    'wishlist_status',
                    [
                        'pending',
                        'approved',
                        'rejected',
                        'cancelled',
                    ]
                )
                ->orderByDesc(
                    'updated_at'
                )
                ->orderByDesc(
                    'id'
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Wishlist Counts
        |--------------------------------------------------------------------------
        */

        $pendingWishlistCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'pending'
                )
                ->count();


        $approvedWishlistCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'approved'
                )
                ->count();


        $rejectedWishlistCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'rejected'
                )
                ->count();


        $cancelledWishlistCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'cancelled'
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | Leave Summary
        |--------------------------------------------------------------------------
        */

        $today =
            now()->toDateString();


        /*
         * Pending Leave
         */
        $pendingLeaveCount =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'pending'
                )
                ->count();


        /*
         * Current Leave
         */
        $currentLeave =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'approved'
                )
                ->whereNull(
                    'actual_return_date'
                )
                ->whereDate(
                    'start_date',
                    '<=',
                    $today
                )
                ->whereDate(
                    'expected_return_date',
                    '>=',
                    $today
                )
                ->orderBy(
                    'start_date'
                )
                ->first();


        /*
         * Upcoming Leave
         */
        $upcomingLeave =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'approved'
                )
                ->whereNull(
                    'actual_return_date'
                )
                ->whereDate(
                    'start_date',
                    '>',
                    $today
                )
                ->orderBy(
                    'start_date'
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Latest Leave Activity
        |--------------------------------------------------------------------------
        |
        | Allows dashboard to show:
        |
        | Pending
        | Approved
        | Rejected
        | Cancelled
        |
        */

        $latestLeave =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->orderByDesc(
                    'updated_at'
                )
                ->orderByDesc(
                    'id'
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'parent.dashboard',
            [
                'guardian' =>
                    $guardian,

                'student' =>
                    $student,

                'enrolments' =>
                    $enrolments,

                /*
                 * Wishlist
                 */
                'latestWishlist' =>
                    $latestWishlist,

                'pendingWishlistCount' =>
                    $pendingWishlistCount,

                'approvedWishlistCount' =>
                    $approvedWishlistCount,

                'rejectedWishlistCount' =>
                    $rejectedWishlistCount,

                'cancelledWishlistCount' =>
                    $cancelledWishlistCount,

                /*
                 * Leave
                 */
                'pendingLeaveCount' =>
                    $pendingLeaveCount,

                'currentLeave' =>
                    $currentLeave,

                'upcomingLeave' =>
                    $upcomingLeave,

                'latestLeave' =>
                    $latestLeave,
            ]
        );
    }
}