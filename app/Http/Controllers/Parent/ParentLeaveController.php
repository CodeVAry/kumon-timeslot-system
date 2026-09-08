<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Guardian;
use App\Models\Admin\Student;
use App\Models\Admin\StudentLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentLeaveController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Guardian IDs
    |--------------------------------------------------------------------------
    */

    private function getParentGuardianIds()
    {
        $email =
            session(
                'parent_auth_email'
            );


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
            ->pluck('id');
    }


    /*
    |--------------------------------------------------------------------------
    | Selected Student
    |--------------------------------------------------------------------------
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
                function ($query) use ($guardianIds) {
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
    | Security Check
    |--------------------------------------------------------------------------
    */

    private function checkLeaveAccess(
        StudentLeave $leave
    ) {
        $student =
            $this->getSelectedStudent();


        if (
            !$student ||
            (int) $leave->student_id
            !==
            (int) $student->id
        ) {
            abort(403);
        }


        return $student;
    }


    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $student =
            $this->getSelectedStudent();


        if (!$student) {
            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        /*
         * All valid leave records.
         */
        $approvedLeaves =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'approved'
                )
                ->orderByDesc(
                    'start_date'
                )
                ->get();


        /*
         * Cancelled records kept as history.
         */
        $leaves =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'cancelled'
                )
                ->orderByDesc(
                    'created_at'
                )
                ->paginate(20);


        $today =
            now()->startOfDay();


        $upcomingLeaveCount =
            $approvedLeaves
                ->filter(
                    function ($leave) use ($today) {
                        return
                            !$leave->actual_return_date
                            &&
                            $leave
                                ->start_date
                                ->copy()
                                ->startOfDay()
                                ->gt($today);
                    }
                )
                ->count();


        $currentLeaveCount =
            $approvedLeaves
                ->filter(
                    function ($leave) use ($today) {
                        return
                            !$leave->actual_return_date
                            &&
                            $leave
                                ->start_date
                                ->copy()
                                ->startOfDay()
                                ->lte($today)
                            &&
                            $leave
                                ->expected_return_date
                                ->copy()
                                ->startOfDay()
                                ->gte($today);
                    }
                )
                ->count();


        $returnedEarlyCount =
            $approvedLeaves
                ->filter(
                    function ($leave) {
                        return
                            $leave->actual_return_date
                            &&
                            $leave->returned_early;
                    }
                )
                ->count();


        $completedLeaveCount =
            $approvedLeaves
                ->filter(
                    function ($leave) use ($today) {
                        if (
                            $leave->actual_return_date
                        ) {
                            return true;
                        }


                        return
                            $leave
                                ->expected_return_date
                                ->copy()
                                ->startOfDay()
                                ->lt($today);
                    }
                )
                ->count();


        $cancelledCount =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'cancelled'
                )
                ->count();


        return view(
            'parent.leave.index',
            compact(
                'student',
                'approvedLeaves',
                'leaves',
                'upcomingLeaveCount',
                'currentLeaveCount',
                'returnedEarlyCount',
                'completedLeaveCount',
                'cancelledCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $student =
            $this->getSelectedStudent();


        if (!$student) {
            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        $enrolments =
            Enrolment::with([
                'subSection',
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
            'parent.leave.create',
            compact(
                'student',
                'enrolments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Parent leave is recorded immediately.
    | There is NO pending approval stage.
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $student =
            $this->getSelectedStudent();


        if (!$student) {
            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        $validated =
            $request->validate([
                'start_date' => [
                    'required',
                    'date',
                ],

                'expected_return_date' => [
                    'required',
                    'date',
                    'after:start_date',
                ],

                'homework_requirement' => [
                    'required',
                    'in:none_required,same_as_normal,increase,decrease',
                ],

                'reason' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Prevent Overlapping Active Leave
        |--------------------------------------------------------------------------
        */

        $overlap =
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
                    $validated[
                        'expected_return_date'
                    ]
                )
                ->whereDate(
                    'expected_return_date',
                    '>=',
                    $validated[
                        'start_date'
                    ]
                )
                ->exists();


        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' =>
                        'This leave overlaps an existing leave for this student.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Create Immediately Active Leave
        |--------------------------------------------------------------------------
        */

        $leave =
            StudentLeave::create([
                'student_id' =>
                    $student->id,

                /*
                 * Internally we keep "approved"
                 * for compatibility with existing
                 * attendance/vacation logic.
                 */
                'status' =>
                    'approved',

                'requested_by_guardian_id' =>
                    Auth::guard(
                        'parent'
                    )->id(),

                /*
                 * No admin review.
                 */
                'reviewed_by_user_id' =>
                    null,

                'reviewed_at' =>
                    null,

                'review_note' =>
                    null,

                'start_date' =>
                    $validated[
                        'start_date'
                    ],

                'expected_return_date' =>
                    $validated[
                        'expected_return_date'
                    ],

                'actual_return_date' =>
                    null,

                'returned_early' =>
                    false,

                'homework_requirement' =>
                    $validated[
                        'homework_requirement'
                    ],

                'reason' =>
                    $validated[
                        'reason'
                    ]
                    ?? null,

                'notes' =>
                    $validated[
                        'notes'
                    ]
                    ?? null,

                'created_by_user_id' =>
                    null,
            ]);


        return redirect()
            ->route(
                'parent.leave.show',
                $leave
            )
            ->with(
                'success',
                'Leave information submitted successfully. The centre has been informed.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        StudentLeave $leave
    ) {
        $student =
            $this->checkLeaveAccess(
                $leave
            );


        $leave->load([
            'requestedByGuardian',
        ]);


        return view(
            'parent.leave.show',
            compact(
                'student',
                'leave'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    |
    | Parent can edit a leave before it starts.
    |--------------------------------------------------------------------------
    */

    public function edit(
        StudentLeave $leave
    ) {
        $student =
            $this->checkLeaveAccess(
                $leave
            );


        if (
            $leave->status
            !==
            'approved'
            ||
            $leave->actual_return_date
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave can no longer be edited.'
                );
        }


        if (
            today()->gte(
                $leave
                    ->start_date
                    ->copy()
                    ->startOfDay()
            )
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Leave cannot be edited after it has started.'
                );
        }


        return view(
            'parent.leave.edit',
            compact(
                'student',
                'leave'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StudentLeave $leave
    ) {
        $student =
            $this->checkLeaveAccess(
                $leave
            );


        if (
            $leave->status
            !==
            'approved'
            ||
            $leave->actual_return_date
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave can no longer be updated.'
                );
        }


        if (
            today()->gte(
                $leave
                    ->start_date
                    ->copy()
                    ->startOfDay()
            )
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Leave cannot be edited after it has started.'
                );
        }


        $validated =
            $request->validate([
                'start_date' => [
                    'required',
                    'date',
                ],

                'expected_return_date' => [
                    'required',
                    'date',
                    'after:start_date',
                ],

                'homework_requirement' => [
                    'required',
                    'in:none_required,same_as_normal,increase,decrease',
                ],

                'reason' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        $overlap =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'id',
                    '!=',
                    $leave->id
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
                    $validated[
                        'expected_return_date'
                    ]
                )
                ->whereDate(
                    'expected_return_date',
                    '>=',
                    $validated[
                        'start_date'
                    ]
                )
                ->exists();


        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' =>
                        'This leave overlaps another leave for this student.',
                ]);
        }


        $leave->update([
            'start_date' =>
                $validated[
                    'start_date'
                ],

            'expected_return_date' =>
                $validated[
                    'expected_return_date'
                ],

            'homework_requirement' =>
                $validated[
                    'homework_requirement'
                ],

            'reason' =>
                $validated[
                    'reason'
                ]
                ?? null,

            'notes' =>
                $validated[
                    'notes'
                ]
                ?? null,
        ]);


        return redirect()
            ->route(
                'parent.leave.show',
                $leave
            )
            ->with(
                'success',
                'Leave information updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    |
    | Parent may cancel an upcoming leave.
    |--------------------------------------------------------------------------
    */

    public function cancel(
        StudentLeave $leave
    ) {
        $this->checkLeaveAccess(
            $leave
        );


        if (
            $leave->status
            !==
            'approved'
            ||
            $leave->actual_return_date
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave cannot be cancelled.'
                );
        }


        if (
            today()->gte(
                $leave
                    ->start_date
                    ->copy()
                    ->startOfDay()
            )
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'A leave that has already started cannot be cancelled.'
                );
        }


        $leave->update([
            'status' =>
                'cancelled',
        ]);


        return redirect()
            ->route(
                'parent.leave.index'
            )
            ->with(
                'success',
                'Leave cancelled successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Cancelled Record
    |--------------------------------------------------------------------------
    */

    public function destroy(
        StudentLeave $leave
    ) {
        $this->checkLeaveAccess(
            $leave
        );


        if (
            $leave->status
            !==
            'cancelled'
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only cancelled leave records can be deleted.'
                );
        }


        $leave->delete();


        return redirect()
            ->route(
                'parent.leave.index'
            )
            ->with(
                'success',
                'Cancelled leave record deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Return Early
    |--------------------------------------------------------------------------
    */

    public function returnEarly(
        StudentLeave $leave
    ) {
        $this->checkLeaveAccess(
            $leave
        );


        if (
            $leave->status
            !==
            'approved'
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave is not active.'
                );
        }


        if ($leave->actual_return_date) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave has already ended.'
                );
        }


        if (
            today()->lt(
                $leave
                    ->start_date
                    ->copy()
                    ->startOfDay()
            )
        ) {
            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'The leave has not started yet.'
                );
        }


        $returnedEarly =
            today()->lt(
                $leave
                    ->expected_return_date
                    ->copy()
                    ->startOfDay()
            );


        $leave->update([
            'actual_return_date' =>
                today(),

            'returned_early' =>
                $returnedEarly,
        ]);


        return redirect()
            ->route(
                'parent.leave.show',
                $leave
            )
            ->with(
                'success',
                $returnedEarly
                    ? 'Early return recorded successfully. The centre has been informed.'
                    : 'Student return recorded successfully.'
            );
    }
}
