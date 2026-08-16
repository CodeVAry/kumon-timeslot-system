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
                ]
            )
        ) {
            $tab = 'current';
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
            ]);


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->whereHas(
                'student',
                function ($studentQuery) use (
                    $search
                ) {

                    $studentQuery->where(
                        function ($query) use (
                            $search
                        ) {

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
        | Current / Upcoming
        |--------------------------------------------------------------------------
        */

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
        | Summary
        |--------------------------------------------------------------------------
        */

        $currentCount =
            StudentLeave::current()
                ->count();


        $upcomingCount =
            StudentLeave::upcoming()
                ->count();


        $historyCount =
            StudentLeave::completed()
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


        return view(
            'admin.leave.index',
            compact(
                'leaves',
                'tab',
                'search',
                'currentCount',
                'upcomingCount',
                'historyCount',
                'returningSoonCount'
            )
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Create Leave
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


        /*
        |--------------------------------------------------------------------------
        | Student Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $students =
                Student::where(
                    'is_active',
                    true
                )
                    ->where(
                        function ($query) use (
                            $search
                        ) {

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


        /*
        |--------------------------------------------------------------------------
        | Selected Student
        |--------------------------------------------------------------------------
        */

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
    | Store Leave
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


        /*
        |--------------------------------------------------------------------------
        | Prevent Overlapping Leave
        |--------------------------------------------------------------------------
        */

        $overlappingLeave =
            StudentLeave::where(
                'student_id',
                $validated[
                    'student_id'
                ]
            )
                ->whereNull(
                    'actual_return_date'
                )
                ->where(
                    function ($query) use (
                        $validated
                    ) {

                        $query
                            ->whereBetween(
                                'start_date',
                                [
                                    $validated[
                                        'start_date'
                                    ],

                                    $validated[
                                        'expected_return_date'
                                    ],
                                ]
                            )
                            ->orWhereBetween(
                                'expected_return_date',
                                [
                                    $validated[
                                        'start_date'
                                    ],

                                    $validated[
                                        'expected_return_date'
                                    ],
                                ]
                            )
                            ->orWhere(
                                function ($query) use (
                                    $validated
                                ) {

                                    $query
                                        ->whereDate(
                                            'start_date',
                                            '<=',
                                            $validated[
                                                'start_date'
                                            ]
                                        )
                                        ->whereDate(
                                            'expected_return_date',
                                            '>=',
                                            $validated[
                                                'expected_return_date'
                                            ]
                                        );
                                }
                            );
                    }
                )
                ->exists();


        if ($overlappingLeave) {

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
    | Edit Leave
    |--------------------------------------------------------------------------
    */
    public function edit(
        StudentLeave $leave
    ) {
        /*
         * Completed leave should not
         * normally be edited.
         */
        if ($leave->actual_return_date) {

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
    | Update / Extend Leave
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        StudentLeave $leave
    ) {
        if ($leave->actual_return_date) {

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
            ]);


        /*
        |--------------------------------------------------------------------------
        | Prevent overlap with another leave
        |--------------------------------------------------------------------------
        */

        $overlappingLeave =
            StudentLeave::where(
                'student_id',
                $leave->student_id
            )
                ->where(
                    'id',
                    '!=',
                    $leave->id
                )
                ->whereNull(
                    'actual_return_date'
                )
                ->where(
                    function ($query) use (
                        $validated
                    ) {

                        $query
                            ->whereBetween(
                                'start_date',
                                [
                                    $validated[
                                        'start_date'
                                    ],

                                    $validated[
                                        'expected_return_date'
                                    ],
                                ]
                            )
                            ->orWhereBetween(
                                'expected_return_date',
                                [
                                    $validated[
                                        'start_date'
                                    ],

                                    $validated[
                                        'expected_return_date'
                                    ],
                                ]
                            )
                            ->orWhere(
                                function ($query) use (
                                    $validated
                                ) {

                                    $query
                                        ->whereDate(
                                            'start_date',
                                            '<=',
                                            $validated[
                                                'start_date'
                                            ]
                                        )
                                        ->whereDate(
                                            'expected_return_date',
                                            '>=',
                                            $validated[
                                                'expected_return_date'
                                            ]
                                        );
                                }
                            );
                    }
                )
                ->exists();


        if ($overlappingLeave) {

            return back()
                ->withInput()
                ->withErrors([
                    'start_date' =>
                        'This leave overlaps another leave record for this student.',
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
    | Return Student / Early Return
    |--------------------------------------------------------------------------
    */
    public function returnStudent(
        Request $request,
        StudentLeave $leave
    ) {
        /*
         * Return already recorded.
         */
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


        /*
         * Do not return before
         * the leave has started.
         */
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


        /*
         * Return date cannot be
         * before leave start date.
         */
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


        /*
         * We do not allow a future
         * actual return date.
         *
         * If the return is planned for
         * the future, expected_return_date
         * should be edited instead.
         */
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


        /*
        |--------------------------------------------------------------------------
        | Determine Early Return
        |--------------------------------------------------------------------------
        */

        $returnedEarly =
            $actualReturnDate->lt(
                $leave
                    ->expected_return_date
                    ->copy()
                    ->startOfDay()
            );


        /*
        |--------------------------------------------------------------------------
        | Save Return
        |--------------------------------------------------------------------------
        */

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
            ])
                ->completed();


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->whereHas(
                'student',
                function ($studentQuery) use (
                    $search
                ) {

                    $studentQuery->where(
                        function ($query) use (
                            $search
                        ) {

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
                    'start_date'
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
