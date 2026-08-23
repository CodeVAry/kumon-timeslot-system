<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Student;
use App\Models\Admin\StudentLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Leave Management
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $tab =
            $request->input(
                'tab',
                'pending'
            );


        if (
            !in_array(
                $tab,
                [
                    'pending',
                    'current',
                    'upcoming',
                ]
            )
        ) {
            $tab = 'pending';
        }


        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );


        $query =
            StudentLeave::with([
                'student.studentStatus',

                'student.enrolments' =>
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

                'student.enrolments.sectionOffering.section',
                'student.enrolments.sectionOffering.day',

                'createdBy',
                'requestedByGuardian',
                'reviewedBy',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->whereHas(
                'student',
                function ($studentQuery) use ($search) {

                    $studentQuery->where(
                        function ($query) use ($search) {

                            $query
                                ->where(
                                    'first_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'external_id',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    [
                                        '%' . $search . '%',
                                    ]
                                );
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Tabs
        |--------------------------------------------------------------------------
        */

        if ($tab === 'pending') {

            $query->where(
                'status',
                'pending'
            );
        }


        if ($tab === 'current') {

            $query->current();
        }


        if ($tab === 'upcoming') {

            $query->upcoming();
        }


        $leaves =
            $query
                ->orderBy(
                    'start_date'
                )
                ->paginate(20)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Counters
        |--------------------------------------------------------------------------
        */

        $pendingCount =
            StudentLeave::where(
                'status',
                'pending'
            )
                ->count();


        $currentCount =
            StudentLeave::current()
                ->count();


        $upcomingCount =
            StudentLeave::upcoming()
                ->count();


        $returningSoonCount =
            StudentLeave::current()
                ->whereDate(
                    'expected_return_date',
                    '<=',
                    now()
                        ->copy()
                        ->addDays(7)
                        ->toDateString()
                )
                ->count();


        $historyCount =
            StudentLeave::where(
                function ($query) {

                    $query
                        ->whereIn(
                            'status',
                            [
                                'rejected',
                                'cancelled',
                            ]
                        )
                        ->orWhere(
                            function ($query) {

                                $query
                                    ->where(
                                        'status',
                                        'approved'
                                    )
                                    ->where(
                                        function ($query) {

                                            $query
                                                ->whereNotNull(
                                                    'actual_return_date'
                                                )
                                                ->orWhereDate(
                                                    'expected_return_date',
                                                    '<',
                                                    now()->toDateString()
                                                );
                                        }
                                    );
                            }
                        );
                }
            )
                ->count();


        return view(
            'admin.leave.index',
            compact(
                'leaves',
                'tab',
                'search',
                'pendingCount',
                'currentCount',
                'upcomingCount',
                'returningSoonCount',
                'historyCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Admin Leave
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );


        $students =
            collect();


        if ($search !== '') {

            $students =
                Student::where(
                    'is_active',
                    true
                )
                    ->where(
                        function ($query) use ($search) {

                            $query
                                ->where(
                                    'first_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'external_id',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    [
                                        '%' . $search . '%',
                                    ]
                                );
                        }
                    )
                    ->orderBy(
                        'first_name'
                    )
                    ->orderBy(
                        'last_name'
                    )
                    ->limit(20)
                    ->get([
                        'id',
                        'external_id',
                        'first_name',
                        'last_name',
                    ]);
        }


        $selectedStudent =
            null;


        if (
            $request->filled(
                'student_id'
            )
        ) {

            $selectedStudent =
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
                        'is_active',
                        true
                    )
                    ->findOrFail(
                        $request->student_id
                    );
        }


        return view(
            'admin.leave.create',
            compact(
                'search',
                'students',
                'selectedStudent'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Admin Leave
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated =
            $request->validate([
                'student_id' => [
                    'required',
                    'exists:students,id',
                ],

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

                    Rule::in([
                        'none_required',
                        'same_as_normal',
                        'increase',
                        'decrease',
                    ]),
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
                $validated['student_id']
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
                        'This student already has approved leave during this period.',
                ]);
        }


        $leave =
            StudentLeave::create([
                'student_id' =>
                    $validated[
                        'student_id'
                    ],

                /*
                 * Staff-created leave is
                 * approved immediately.
                 */
                'status' =>
                    'approved',

                'requested_by_guardian_id' =>
                    null,

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
                    auth()->id(),
            ]);


        return redirect()
            ->route(
                'admin.leave.show',
                $leave
            )
            ->with(
                'success',
                'Student leave created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Details
    |--------------------------------------------------------------------------
    */

    public function show(
        StudentLeave $leave
    ) {
        $leave->load([
            'student.studentStatus',
            'student.guardians',

            'student.enrolments' =>
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

            'student.enrolments.sectionOffering.section',
            'student.enrolments.sectionOffering.day',

            'createdBy',
            'requestedByGuardian',
            'reviewedBy',
        ]);


        return view(
            'admin.leave.show',
            compact(
                'leave'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve Parent Request
    |--------------------------------------------------------------------------
    */

    public function approve(
        Request $request,
        StudentLeave $leave
    ) {
        if (
            $leave->status
            !==
            'pending'
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only pending leave requests can be approved.'
                );
        }


        $validated =
            $request->validate([
                'review_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        /*
         * Always recheck overlap
         * at the moment of approval.
         */
        $overlap =
            StudentLeave::where(
                'student_id',
                $leave->student_id
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
                    $leave
                        ->expected_return_date
                        ->toDateString()
                )
                ->whereDate(
                    'expected_return_date',
                    '>=',
                    $leave
                        ->start_date
                        ->toDateString()
                )
                ->exists();


        if ($overlap) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This request overlaps with another approved leave.'
                );
        }


        $leave->update([
            'status' =>
                'approved',

            'reviewed_by_user_id' =>
                auth()->id(),

            'reviewed_at' =>
                now(),

            'review_note' =>
                $validated[
                    'review_note'
                ] ?? null,
        ]);


        return redirect()
            ->route(
                'admin.leave.show',
                $leave
            )
            ->with(
                'success',
                'Parent leave request approved successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject Parent Request
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        StudentLeave $leave
    ) {
        if (
            $leave->status
            !==
            'pending'
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Only pending leave requests can be rejected.'
                );
        }


        $validated =
            $request->validate(
                [
                    'review_note' => [
                        'required',
                        'string',
                        'max:2000',
                    ],
                ],
                [
                    'review_note.required' =>
                        'Please enter a reason or review note before rejecting the request.',
                ]
            );


        $leave->update([
            'status' =>
                'rejected',

            'reviewed_by_user_id' =>
                auth()->id(),

            'reviewed_at' =>
                now(),

            'review_note' =>
                $validated[
                    'review_note'
                ],
        ]);


        return redirect()
            ->route(
                'admin.leave.show',
                $leave
            )
            ->with(
                'success',
                'Parent leave request rejected.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Leave
    |--------------------------------------------------------------------------
    |
    | Admin may edit:
    |
    | - pending request
    | - approved current/upcoming leave
    |
    | Admin may NOT edit:
    |
    | - rejected
    | - cancelled
    | - completed
    |
    */

    public function edit(
        StudentLeave $leave
    ) {
        if (
            !in_array(
                $leave->status,
                [
                    'pending',
                    'approved',
                ]
            )
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Rejected or cancelled leave requests cannot be edited.'
                );
        }


        if (
            $leave->status === 'approved'
            &&
            $leave->actual_return_date
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Completed leave cannot be edited.'
                );
        }


        $leave->load([
            'student.studentStatus',

            'student.enrolments' =>
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

            'student.enrolments.sectionOffering.section',
            'student.enrolments.sectionOffering.day',
        ]);


        return view(
            'admin.leave.edit',
            compact(
                'leave'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Leave
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StudentLeave $leave
    ) {
        if (
            !in_array(
                $leave->status,
                [
                    'pending',
                    'approved',
                ]
            )
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave record cannot be updated.'
                );
        }


        if (
            $leave->status === 'approved'
            &&
            $leave->actual_return_date
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Completed leave cannot be updated.'
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

                    Rule::in([
                        'none_required',
                        'same_as_normal',
                        'increase',
                        'decrease',
                    ]),
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

                'review_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        /*
         * Only approved leave records
         * should block another leave.
         *
         * If current record is pending,
         * it can overlap another pending
         * request but not approved leave.
         */
        $overlap =
            StudentLeave::where(
                'student_id',
                $leave->student_id
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
                        'This leave period overlaps another approved leave for this student.',
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

            'review_note' =>
                $validated[
                    'review_note'
                ]
                ??
                $leave->review_note,
        ]);


        return redirect()
            ->route(
                'admin.leave.show',
                $leave
            )
            ->with(
                'success',
                $leave->status === 'pending'
                    ? 'Leave request updated successfully.'
                    : 'Student leave updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Leave / Request
    |--------------------------------------------------------------------------
    |
    | For audit/history protection:
    |
    | Allowed:
    | - pending
    | - rejected
    | - cancelled
    |
    | Not allowed:
    | - approved
    | - completed approved leave
    |
    */

    public function destroy(
        StudentLeave $leave
    ) {
        if (
            !in_array(
                $leave->status,
                [
                    'pending',
                    'rejected',
                    'cancelled',
                ]
            )
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'Approved leave records cannot be deleted because they are part of the student leave history.'
                );
        }


        $leave->delete();


        return redirect()
            ->route(
                'admin.leave.index',
                [
                    'tab' =>
                        'pending',
                ]
            )
            ->with(
                'success',
                'Leave request deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Return Student
    |--------------------------------------------------------------------------
    */

    public function returnStudent(
        Request $request,
        StudentLeave $leave
    ) {
        if (
            $leave->status
            !==
            'approved'
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
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
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This student has already returned.'
                );
        }


        if (
            now()
                ->startOfDay()
                ->lt(
                    $leave
                        ->start_date
                        ->copy()
                        ->startOfDay()
                )
        ) {

            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave has not started yet.'
                );
        }


        $validated =
            $request->validate([
                'actual_return_date' => [
                    'required',
                    'date',
                ],
            ]);


        $actualReturnDate =
            Carbon::parse(
                $validated[
                    'actual_return_date'
                ]
            )
                ->startOfDay();


        if (
            $actualReturnDate->lt(
                $leave
                    ->start_date
                    ->copy()
                    ->startOfDay()
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'actual_return_date' =>
                        'Return date cannot be before the leave start date.',
                ]);
        }


        if (
            $actualReturnDate->gt(
                now()->startOfDay()
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'actual_return_date' =>
                        'Actual return date cannot be in the future.',
                ]);
        }


        $returnedEarly =
            $actualReturnDate->lt(
                $leave
                    ->expected_return_date
                    ->copy()
                    ->startOfDay()
            );


        $leave->update([
            'actual_return_date' =>
                $actualReturnDate
                    ->toDateString(),

            'returned_early' =>
                $returnedEarly,
        ]);


        return redirect()
            ->route(
                'admin.leave.show',
                $leave
            )
            ->with(
                'success',

                $returnedEarly
                    ? 'Student early return recorded successfully.'
                    : 'Student return recorded successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Leave History
    |--------------------------------------------------------------------------
    */

    public function history(
        Request $request
    ) {
        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );


        $query =
            StudentLeave::with([
                'student',

                'student.enrolments' =>
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

                'student.enrolments.sectionOffering.section',
                'student.enrolments.sectionOffering.day',

                'createdBy',
                'requestedByGuardian',
                'reviewedBy',
            ])
                ->where(
                    function ($query) {

                        $query
                            ->whereIn(
                                'status',
                                [
                                    'rejected',
                                    'cancelled',
                                ]
                            )
                            ->orWhere(
                                function ($query) {

                                    $query
                                        ->where(
                                            'status',
                                            'approved'
                                        )
                                        ->where(
                                            function ($query) {

                                                $query
                                                    ->whereNotNull(
                                                        'actual_return_date'
                                                    )
                                                    ->orWhereDate(
                                                        'expected_return_date',
                                                        '<',
                                                        now()->toDateString()
                                                    );
                                            }
                                        );
                                }
                            );
                    }
                );


        if ($search !== '') {

            $query->whereHas(
                'student',
                function ($studentQuery) use ($search) {

                    $studentQuery->where(
                        function ($query) use ($search) {

                            $query
                                ->where(
                                    'first_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'external_id',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    [
                                        '%' . $search . '%',
                                    ]
                                );
                        }
                    );
                }
            );
        }


        $leaves =
            $query
                ->orderByDesc(
                    'created_at'
                )
                ->paginate(20)
                ->withQueryString();


        return view(
            'admin.leave.history',
            compact(
                'leaves',
                'search'
            )
        );
    }
}
