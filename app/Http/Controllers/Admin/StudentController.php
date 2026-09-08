<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Day;
use App\Models\Admin\Section;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use App\Models\Admin\StudentLeave;
use App\Models\Admin\StudentStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Student List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $studentsQuery =
            Student::with([
                'studentStatus',
                'guardians',
                'enrolments.subSection',
                'enrolments.sectionOffering.section',
                'enrolments.sectionOffering.day',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Search Student
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search =
                trim(
                    $request->input('search')
                );


            $studentsQuery->where(
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


        /*
        |--------------------------------------------------------------------------
        | Student Status Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'student_status_id'
            )
        ) {

            $studentsQuery->where(
                'student_status_id',
                $request->input(
                    'student_status_id'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Guardian Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'guardian'
            )
        ) {

            $guardianSearch =
                trim(
                    $request->input(
                        'guardian'
                    )
                );


            $studentsQuery->whereHas(
                'guardians',
                function ($query) use ($guardianSearch) {

                    $query->where(
                        function ($guardianQuery) use ($guardianSearch) {

                            $guardianQuery
                                ->where(
                                    'first_name',
                                    'like',
                                    '%' .
                                    $guardianSearch .
                                    '%'
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    '%' .
                                    $guardianSearch .
                                    '%'
                                )
                                ->orWhere(
                                    'phone',
                                    'like',
                                    '%' .
                                    $guardianSearch .
                                    '%'
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    '%' .
                                    $guardianSearch .
                                    '%'
                                )
                                ->orWhereRaw(
                                    "CONCAT(first_name, ' ', last_name) LIKE ?",
                                    [
                                        '%' .
                                        $guardianSearch .
                                        '%',
                                    ]
                                );
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Day / Time / Section Filters
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('day_id')
            ||
            $request->filled('timeslot')
            ||
            $request->filled('section_id')
        ) {

            $studentsQuery->whereHas(
                'enrolments',
                function ($enrolmentQuery) use ($request) {

                    $enrolmentQuery
                        ->where(
                            'is_active',
                            true
                        )
                        ->where(
                            'is_wishlist',
                            false
                        )
                        ->whereHas(
                            'sectionOffering',
                            function ($offeringQuery) use ($request) {

                                if (
                                    $request->filled(
                                        'day_id'
                                    )
                                ) {

                                    $offeringQuery->where(
                                        'day_id',
                                        $request->input(
                                            'day_id'
                                        )
                                    );
                                }


                                if (
                                    $request->filled(
                                        'timeslot'
                                    )
                                ) {

                                    $offeringQuery->where(
                                        'start_time',
                                        $request->input(
                                            'timeslot'
                                        )
                                    );
                                }


                                if (
                                    $request->filled(
                                        'section_id'
                                    )
                                ) {

                                    $offeringQuery->where(
                                        'section_id',
                                        $request->input(
                                            'section_id'
                                        )
                                    );
                                }
                            }
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $students =
            $studentsQuery
                ->orderBy(
                    'first_name'
                )
                ->orderBy(
                    'last_name'
                )
                ->paginate(20)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Vacation Students
        |--------------------------------------------------------------------------
        |
        | Vacation overrides the displayed Student Status only.
        |
        | It does NOT update student_status_id.
        |--------------------------------------------------------------------------
        */

        $studentIds =
            $students
                ->getCollection()
                ->pluck('id');


        $vacationStudentIds =
            collect();


        if ($studentIds->isNotEmpty()) {

            $vacationStudentIds =
                StudentLeave::activeOnDate(
                    now()->toDateString()
                )
                    ->whereIn(
                        'student_id',
                        $studentIds
                    )
                    ->pluck(
                        'student_id'
                    )
                    ->map(
                        fn ($id) => (int) $id
                    )
                    ->unique()
                    ->values();
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Values
        |--------------------------------------------------------------------------
        */

        $studentStatuses =
            StudentStatus::where(
                'is_active',
                true
            )
                ->orderBy(
                    'status_name'
                )
                ->get();


        $days =
            Day::where(
                'is_active',
                true
            )
                ->orderBy(
                    'sort_order'
                )
                ->get();


        $sections =
            Section::where(
                'is_active',
                true
            )
                ->orderBy(
                    'section_name'
                )
                ->get();


        $timeslots =
            SectionOffering::where(
                'is_active',
                true
            )
                ->select(
                    'start_time'
                )
                ->distinct()
                ->orderBy(
                    'start_time'
                )
                ->pluck(
                    'start_time'
                );


        return view(
            'admin.students.index',
            compact(
                'students',
                'studentStatuses',
                'days',
                'sections',
                'timeslots',
                'vacationStudentIds'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Profile
    |--------------------------------------------------------------------------
    */

    public function show(
        Student $student
    ) {
        $student->load([
            'studentStatus',
            'guardians',
            'enrolments.subSection',
            'enrolments.sectionOffering.section',
            'enrolments.sectionOffering.day',
        ]);


        $primaryGuardian =
            $student
                ->guardians
                ->first(
                    function ($guardian) {

                        return (bool)
                            $guardian
                                ->pivot
                                ->is_primary;
                    }
                );


        if (!$primaryGuardian) {

            $primaryGuardian =
                $student
                    ->guardians
                    ->first();
        }


        $confirmedEnrolments =
            $student
                ->enrolments
                ->filter(
                    function ($enrolment) {

                        return
                            $enrolment
                                ->is_active
                            &&
                            !$enrolment
                                ->is_wishlist;
                    }
                )
                ->values();


        $wishlistEnrolments =
            $student
                ->enrolments
                ->filter(
                    function ($enrolment) {

                        return
                            $enrolment
                                ->is_active
                            &&
                            $enrolment
                                ->is_wishlist;
                    }
                )
                ->values();


        return view(
            'admin.students.show',
            compact(
                'student',
                'primaryGuardian',
                'confirmedEnrolments',
                'wishlistEnrolments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Student
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        Student $student
    ) {
        $student->load([
            'studentStatus',
            'guardians',
        ]);


        $studentStatuses =
            StudentStatus::where(
                'is_active',
                true
            )
                ->orderBy(
                    'status_name'
                )
                ->get();


        $guardians =
            $student
                ->guardians()
                ->orderByDesc(
                    'guardian_student.is_primary'
                )
                ->orderBy(
                    'guardians.first_name'
                )
                ->orderBy(
                    'guardians.last_name'
                )
                ->get();


        $section =
            $request->input(
                'section',
                'information'
            );


        $allowedSections = [
            'information',
            'guardian',
            'status',
            'notes',
        ];


        if (
            !in_array(
                $section,
                $allowedSections,
                true
            )
        ) {

            $section =
                'information';
        }


        return view(
            'admin.students.edit',
            compact(
                'student',
                'studentStatuses',
                'guardians',
                'section'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Student
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Student $student
    ) {
        $section =
            $request->input(
                'section'
            );


        /*
        |--------------------------------------------------------------------------
        | Student Information
        |--------------------------------------------------------------------------
        */

        if (
            $section
            ===
            'information'
        ) {

            $validated =
                $request->validate([
                    'external_id' => [
                        'required',
                        'string',
                        'max:50',

                        Rule::unique(
                            'students',
                            'external_id'
                        )->ignore(
                            $student->id
                        ),
                    ],

                    'first_name' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'last_name' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'email' => [
                        'required',
                        'email',
                        'max:150',
                    ],

                    'phone' => [
                        'required',
                        'string',
                        'max:30',
                    ],

                    'date_of_birth' => [
                        'required',
                        'date',
                        'before_or_equal:today',
                    ],

                    'address' => [
                        'required',
                        'string',
                        'max:1000',
                    ],

                    'can_leave_alone' => [
                        'required',
                        'boolean',
                    ],
                ]);


            $student->update([
                'external_id' =>
                    trim(
                        $validated[
                            'external_id'
                        ]
                    ),

                'first_name' =>
                    trim(
                        $validated[
                            'first_name'
                        ]
                    ),

                'last_name' =>
                    trim(
                        $validated[
                            'last_name'
                        ]
                    ),

                'email' =>
                    trim(
                        $validated[
                            'email'
                        ]
                    ),

                'phone' =>
                    trim(
                        $validated[
                            'phone'
                        ]
                    ),

                'date_of_birth' =>
                    $validated[
                        'date_of_birth'
                    ],

                'address' =>
                    trim(
                        $validated[
                            'address'
                        ]
                    ),

                'can_leave_alone' =>
                    $validated[
                        'can_leave_alone'
                    ],
            ]);


            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                )
                ->with(
                    'success',
                    'Student information updated successfully.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Guardian Information
        |--------------------------------------------------------------------------
        */

        if (
            $section
            ===
            'guardian'
        ) {

            $validated =
                $request->validate([
                    'guardians' => [
                        'required',
                        'array',
                        'min:1',
                    ],

                    'guardians.*.id' => [
                        'required',
                        'integer',
                        'exists:guardians,id',
                    ],

                    'guardians.*.first_name' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'guardians.*.last_name' => [
                        'required',
                        'string',
                        'max:100',
                    ],

                    'guardians.*.email' => [
                        'nullable',
                        'email',
                        'max:150',
                    ],

                    'guardians.*.phone' => [
                        'required',
                        'string',
                        'max:30',
                    ],

                    'guardians.*.address' => [
                        'nullable',
                        'string',
                        'max:1000',
                    ],

                    'guardians.*.relationship' => [
                        'nullable',
                        'string',
                        'max:50',
                    ],

                    'guardians.*.is_emergency_contact' => [
                        'required',
                        'boolean',
                    ],

                    'primary_guardian_id' => [
                        'nullable',
                        'integer',
                        'exists:guardians,id',
                    ],
                ]);


            $primaryGuardianId =
                $validated[
                    'primary_guardian_id'
                ]
                ?? null;


            if ($primaryGuardianId) {

                $primaryGuardianExists =
                    $student
                        ->guardians()
                        ->where(
                            'guardians.id',
                            $primaryGuardianId
                        )
                        ->exists();


                if (!$primaryGuardianExists) {

                    abort(403);
                }
            }


            foreach (
                $validated[
                    'guardians'
                ]
                as $guardianData
            ) {

                $guardian =
                    $student
                        ->guardians()
                        ->where(
                            'guardians.id',
                            $guardianData[
                                'id'
                            ]
                        )
                        ->first();


                if (!$guardian) {

                    abort(403);
                }


                $guardian->update([
                    'first_name' =>
                        trim(
                            $guardianData[
                                'first_name'
                            ]
                        ),

                    'last_name' =>
                        trim(
                            $guardianData[
                                'last_name'
                            ]
                        ),

                    'email' =>
                        !empty(
                            $guardianData[
                                'email'
                            ]
                        )
                            ? trim(
                                $guardianData[
                                    'email'
                                ]
                            )
                            : null,

                    'phone' =>
                        trim(
                            $guardianData[
                                'phone'
                            ]
                        ),

                    'address' =>
                        !empty(
                            $guardianData[
                                'address'
                            ]
                        )
                            ? trim(
                                $guardianData[
                                    'address'
                                ]
                            )
                            : null,
                ]);


                $student
                    ->guardians()
                    ->updateExistingPivot(
                        $guardian->id,
                        [
                            'relationship' =>
                                !empty(
                                    $guardianData[
                                        'relationship'
                                    ]
                                )
                                    ? trim(
                                        $guardianData[
                                            'relationship'
                                        ]
                                    )
                                    : null,

                            'is_primary' =>
                                $primaryGuardianId
                                &&
                                (int)
                                $primaryGuardianId
                                ===
                                (int)
                                $guardian->id,

                            'is_emergency_contact' =>
                                (bool)
                                $guardianData[
                                    'is_emergency_contact'
                                ],
                        ]
                    );
            }


            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                )
                ->with(
                    'success',
                    'Guardian information updated successfully.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Student Status
        |--------------------------------------------------------------------------
        */

        if (
            $section
            ===
            'status'
        ) {

            $validated =
                $request->validate([
                    'student_status_id' => [
                        'required',
                        'integer',

                        Rule::exists(
                            'student_statuses',
                            'id'
                        )->where(
                            function ($query) {

                                $query->where(
                                    'is_active',
                                    true
                                );
                            }
                        ),
                    ],

                    'is_active' => [
                        'required',
                        'boolean',
                    ],
                ]);


            $isActive =
                (bool)
                $validated[
                    'is_active'
                ];


            $inactiveSince =
                null;


            if (!$isActive) {

                $inactiveSince =
                    $student
                        ->inactive_since
                    ??
                    now()
                        ->toDateString();
            }


            $student->update([
                'student_status_id' =>
                    $validated[
                        'student_status_id'
                    ],

                'is_active' =>
                    $isActive,

                'inactive_since' =>
                    $inactiveSince,
            ]);


            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                )
                ->with(
                    'success',
                    'Student status updated successfully.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Notes
        |--------------------------------------------------------------------------
        */

        if (
            $section
            ===
            'notes'
        ) {

            $validated =
                $request->validate([
                    'notes' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],
                ]);


            $student->update([
                'notes' =>
                    !empty(
                        $validated[
                            'notes'
                        ]
                    )
                        ? trim(
                            $validated[
                                'notes'
                            ]
                        )
                        : null,
            ]);


            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                )
                ->with(
                    'success',
                    'Student notes updated successfully.'
                );
        }


        return redirect()
            ->route(
                'admin.students.show',
                $student
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Student $student
    ) {
        try {

            $studentName =
                $student->first_name
                .
                ' '
                .
                $student->last_name;


            $student->delete();


            return redirect()
                ->route(
                    'admin.students.index'
                )
                ->with(
                    'success',
                    $studentName
                    .
                    ' has been deleted successfully.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route(
                    'admin.students.index'
                )
                ->with(
                    'error',
                    'This student cannot be deleted because related records still exist.'
                );
        }
    }
}
