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
                'current'
            );


        if (
            !in_array(
                $tab,
                [
                    'current',
                    'upcoming',
                ],
                true
            )
        ) {
            $tab =
                'current';
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
            ])
                ->where(
                    'status',
                    'approved'
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
                                        '%' .
                                        $search .
                                        '%',
                                    ]
                                );
                        }
                    );
                }
            );
        }


        if ($tab === 'current') {
            $query
                ->whereDate(
                    'start_date',
                    '<=',
                    now()->toDateString()
                )
                ->whereNull(
                    'actual_return_date'
                )
                ->whereDate(
                    'expected_return_date',
                    '>=',
                    now()->toDateString()
                );
        }


        if ($tab === 'upcoming') {
            $query
                ->whereDate(
                    'start_date',
                    '>',
                    now()->toDateString()
                )
                ->whereNull(
                    'actual_return_date'
                );
        }


        $leaves =
            $query
                ->orderBy(
                    'start_date'
                )
                ->paginate(20)
                ->withQueryString();


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
                        ->where(
                            'status',
                            'cancelled'
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
                                                    now()
                                                        ->toDateString()
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
                'currentCount',
                'upcomingCount',
                'returningSoonCount',
                'historyCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
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
                                        '%' .
                                        $search .
                                        '%',
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
    | Store
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
                        'This student already has leave during this period.',
                ]);
        }


        $leave =
            StudentLeave::create([
                'student_id' =>
                    $validated[
                        'student_id'
                    ],

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
                    ]
                    ?? null,

                'notes' =>
                    $validated[
                        'notes'
                    ]
                    ?? null,

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
    | Show
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
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        StudentLeave $leave
    ) {
        if (
            $leave->status
            !==
            'approved'
            ||
            $leave->actual_return_date
        ) {
            return redirect()
                ->route(
                    'admin.leave.show',
                    $leave
                )
                ->with(
                    'error',
                    'This leave record cannot be edited.'
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
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StudentLeave $leave
    ) {
        if (
            $leave->status
            !==
            'approved'
            ||
            $leave->actual_return_date
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
                        'This leave period overlaps another leave for this student.',
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
                'Student leave updated successfully.'
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
                    'This leave is not active.'
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
    | History
    |--------------------------------------------------------------------------
    */

    public function history(Request $request)
    {
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
                'createdBy',
                'requestedByGuardian',
            ])
                ->where(
                    function ($query) {
                        $query
                            ->where(
                                'status',
                                'cancelled'
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
                                                        now()
                                                            ->toDateString()
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
                                    '%' .
                                    $search .
                                    '%'
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    '%' .
                                    $search .
                                    '%'
                                )
                                ->orWhere(
                                    'external_id',
                                    'like',
                                    '%' .
                                    $search .
                                    '%'
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
