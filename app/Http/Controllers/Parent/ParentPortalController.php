<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Welcome Page
    |--------------------------------------------------------------------------
    */

    public function welcome()
    {
        $guardian =
            Auth::guard('parent')->user();


        /*
         * Get only students linked
         * to the logged-in guardian.
         */
        $students =
            $guardian
                ->students()
                ->where(
                    'students.is_active',
                    true
                )
                ->orderBy(
                    'students.first_name'
                )
                ->orderBy(
                    'students.last_name'
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
        $guardian =
            Auth::guard('parent')->user();


        /*
         * Security:
         * Make sure this student
         * really belongs to this guardian.
         */
        $hasStudent =
            $guardian
                ->students()
                ->where(
                    'students.id',
                    $student->id
                )
                ->exists();


        if (!$hasStudent) {

            abort(403);
        }


        /*
         * Save selected student
         * into the parent session.
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
    | Temporary Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $guardian =
            Auth::guard('parent')->user();


        $studentId =
            session('parent_student_id');


        if (!$studentId) {

            return redirect()
                ->route('parent.welcome');
        }


        /*
         * Make sure selected student
         * belongs to this guardian.
         */
        $student =
            $guardian
                ->students()
                ->where(
                    'students.id',
                    $studentId
                )
                ->first();


        if (!$student) {

            session()->forget(
                'parent_student_id'
            );

            return redirect()
                ->route('parent.welcome');
        }


        /*
         * Load confirmed active classes.
         */
        $enrolments =
            \App\Models\Admin\Enrolment::with([
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
