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
    | Get Guardian IDs For Logged-In Parent Identity
    |--------------------------------------------------------------------------
    |
    | The email/mobile used during login identifies the parent.
    | One parent may exist in more than one guardian row because
    | they may have been registered separately for different students.
    |
    */

    private function getParentGuardianIds()
    {
        $guardian =
            Auth::guard('parent')->user();


        if (!$guardian) {
            return collect();
        }


        $loginType =
            session('parent_auth_type');


        $loginValue =
            session('parent_auth_value');


        /*
         * If parent logged in using email.
         */
        if (
            $loginType === 'email'
            &&
            $loginValue
        ) {

            return Guardian::where(
                'is_active',
                true
            )
                ->where(
                    'normalized_email',
                    $loginValue
                )
                ->pluck('id');
        }


        /*
         * If parent logged in using phone.
         */
        if (
            $loginType === 'phone'
            &&
            $loginValue
        ) {

            return Guardian::where(
                'is_active',
                true
            )
                ->where(
                    'normalized_phone',
                    $loginValue
                )
                ->pluck('id');
        }


        /*
         * Fallback.
         *
         * This is only used if the login identity
         * was not stored in the session.
         */
        return collect([
            $guardian->id,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Welcome Page
    |--------------------------------------------------------------------------
    */

    public function welcome()
    {
        $guardian =
            Auth::guard('parent')->user();


        $guardianIds =
            $this->getParentGuardianIds();


        /*
         * Get ALL students connected to any guardian
         * record belonging to this parent identity.
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
            $this->getParentGuardianIds();


        /*
         * Security:
         * Parent can select only a student connected
         * to one of their matching guardian records.
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
            Auth::guard('parent')->user();


        $studentId =
            session(
                'parent_student_id'
            );


        /*
         * No student selected.
         */
        if (!$studentId) {

            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        $guardianIds =
            $this->getParentGuardianIds();


        /*
         * Find selected student only if the student
         * belongs to this parent.
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


        /*
         * Invalid student selection.
         */
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
         * Load confirmed active classes.
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
