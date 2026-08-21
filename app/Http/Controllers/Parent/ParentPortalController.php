<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Guardian;
use App\Models\Admin\Student;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Parent Email
    |--------------------------------------------------------------------------
    |
    | Email is the parent identity.
    |
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
    | Welcome Page
    |--------------------------------------------------------------------------
    */

    public function welcome()
    {
        /*
         * Laravel authenticated Guardian.
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
         * Get every Guardian record
         * using the same email.
         */
        $guardianIds =
            $this
                ->getParentGuardianIds();


        /*
        |--------------------------------------------------------------------------
        | Get ALL Students
        |--------------------------------------------------------------------------
        |
        | Student may be attached to any Guardian row
        | using the same parent email.
        |
        */

        $students =
            Student::where(
                'is_active',
                true
            )
                ->whereHas(
                    'guardians',
                    function ($query) use ($guardianIds) {

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
            $this
                ->getParentGuardianIds();


        /*
         * Security:
         * selected student must belong
         * to this parent's email group.
         */
        $hasStudent =
            $student
                ->guardians()
                ->whereIn(
                    'guardians.id',
                    $guardianIds
                )
                ->exists();


        if (!$hasStudent) {

            abort(403);
        }


        /*
         * Save selected student.
         */
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


        $studentId =
            session(
                'parent_student_id'
            );


        if (!$studentId) {

            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        $guardianIds =
            $this
                ->getParentGuardianIds();


        /*
        |--------------------------------------------------------------------------
        | Secure Student Lookup
        |--------------------------------------------------------------------------
        */

        $student =
            Student::where(
                'id',
                $studentId
            )
                ->where(
                    'is_active',
                    true
                )
                ->whereHas(
                    'guardians',
                    function ($query) use ($guardianIds) {

                        $query->whereIn(
                            'guardians.id',
                            $guardianIds
                        );
                    }
                )
                ->first();


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
        | Confirmed Classes
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
                ->get();


        return view(
            'parent.dashboard',
            compact(
                'guardian',
                'student',
                'enrolments'
            )
        );
    }
}
