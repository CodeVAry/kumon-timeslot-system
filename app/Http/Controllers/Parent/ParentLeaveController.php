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
    | Get Parent Guardian IDs
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
    | Get Selected Student
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
            !$student
            ||
            $leave->student_id
                != $student->id
        ) {

            abort(403);
        }


        return $student;
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Index
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {
        $student =
            $this->getSelectedStudent();


        if (!$student) {

            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Request Filter
        |--------------------------------------------------------------------------
        |
        | Approved leave is displayed separately in
        | the Leave Status table.
        |
        */

        $status =
            $request->input(
                'status',
                'all'
            );


        if (
            !in_array(
                $status,
                [
                    'all',
                    'pending',
                    'rejected',
                    'cancelled',
                ]
            )
        ) {

            $status =
                'all';
        }


        /*
        |--------------------------------------------------------------------------
        | Approved Leave
        |--------------------------------------------------------------------------
        |
        | These records will be shown as:
        |
        | Upcoming
        | Current Leave
        | Returned Early
        | Completed
        |
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
        |--------------------------------------------------------------------------
        | Parent Requests / Request History
        |--------------------------------------------------------------------------
        */

        $requestQuery =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->whereIn(
                    'status',
                    [
                        'pending',
                        'rejected',
                        'cancelled',
                    ]
                );


        if (
            $status
            !==
            'all'
        ) {

            $requestQuery->where(
                'status',
                $status
            );
        }


        $leaves =
            $requestQuery
                ->orderByDesc(
                    'created_at'
                )
                ->paginate(20)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Summary Counts
        |--------------------------------------------------------------------------
        */

        $pendingCount =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'pending'
                )
                ->count();


        $approvedCount =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'approved'
                )
                ->count();


        $rejectedCount =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'rejected'
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


        /*
        |--------------------------------------------------------------------------
        | Operational Leave Counts
        |--------------------------------------------------------------------------
        */

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
                            &&
                            !$leave->returned_early
                        ) {

                            return true;
                        }


                        return
                            !$leave->actual_return_date
                            &&
                            $leave
                                ->expected_return_date
                                ->copy()
                                ->startOfDay()
                                ->lt($today);
                    }
                )
                ->count();


        return view(
            'parent.leave.index',
            compact(
                'student',
                'approvedLeaves',
                'leaves',
                'status',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'cancelledCount',
                'upcomingLeaveCount',
                'currentLeaveCount',
                'returnedEarlyCount',
                'completedLeaveCount'
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


        /*
         * Only one pending request at a time.
         */

        $pendingRequest =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'pending'
                )
                ->first();


        if ($pendingRequest) {

            return redirect()
                ->route(
                    'parent.leave.show',
                    $pendingRequest
                )
                ->with(
                    'error',
                    'This student already has a pending leave request.'
                );
        }


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
    */

    public function store(
        Request $request
    ) {
        $student =
            $this->getSelectedStudent();


        if (!$student) {

            return redirect()
                ->route(
                    'parent.welcome'
                );
        }


        $alreadyPending =
            StudentLeave::where(
                'student_id',
                $student->id
            )
                ->where(
                    'status',
                    'pending'
                )
                ->exists();


        if ($alreadyPending) {

            return back()
                ->withInput()
                ->withErrors([
                    'leave' =>
                        'This student already has a pending leave request.',
                ]);
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
                    'in:same_as_normal,increase,decrease',
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
        | Prevent Overlap With Approved Leave
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
                        'This leave request overlaps an existing approved leave.',
                ]);
        }


        $leave =
            StudentLeave::create([
                'student_id' =>
                    $student->id,

                'status' =>
                    'pending',

                'requested_by_guardian_id' =>
                    Auth::guard(
                        'parent'
                    )->id(),

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
                    ] ?? null,

                'notes' =>
                    $validated[
                        'notes'
                    ] ?? null,

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
                'Leave request submitted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Details
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
            'reviewedBy',
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
    | Edit Pending Request
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
            'pending'
        ) {

            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only pending leave requests can be edited.'
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
    | Update Pending Request
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
            'pending'
        ) {

            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only pending requests can be updated.'
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
                    'in:same_as_normal,increase,decrease',
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
                        'This leave request overlaps an existing approved leave.',
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
                ] ?? null,

            'notes' =>
                $validated[
                    'notes'
                ] ?? null,
        ]);


        return redirect()
            ->route(
                'parent.leave.show',
                $leave
            )
            ->with(
                'success',
                'Leave request updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Pending Request
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
            'pending'
        ) {

            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only pending leave requests can be cancelled.'
                );
        }


        $leave->update([
            'status' =>
                'cancelled',
        ]);


        return redirect()
            ->route(
                'parent.leave.show',
                $leave
            )
            ->with(
                'success',
                'Leave request cancelled successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Rejected / Cancelled Request
    |--------------------------------------------------------------------------
    */

    public function destroy(
        StudentLeave $leave
    ) {
        $this->checkLeaveAccess(
            $leave
        );


        if (
            !in_array(
                $leave->status,
                [
                    'rejected',
                    'cancelled',
                ]
            )
        ) {

            return redirect()
                ->route(
                    'parent.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only rejected or cancelled leave requests can be deleted.'
                );
        }


        $leave->delete();


        return redirect()
            ->route(
                'parent.leave.index'
            )
            ->with(
                'success',
                'Leave request deleted successfully.'
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
                    'Only approved leave can record a return.'
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


        /*
         * Leave must already have started.
         */

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
                    ? 'Early return recorded successfully.'
                    : 'Student return recorded successfully.'
            );
    }
}
