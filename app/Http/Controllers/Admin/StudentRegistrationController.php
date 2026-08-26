<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Guardian;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use App\Models\Admin\StudentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentRegistrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Step 1: Student Details
    |--------------------------------------------------------------------------
    */

    public function studentStep()
    {
        $studentStatuses =
            StudentStatus::where(
                'is_active',
                true
            )
                ->orderBy(
                    'status_name',
                    'asc'
                )
                ->get();


        $studentData =
            session(
                'student_registration.student',
                []
            );


        return view(
            'admin.student-registration.student',
            compact(
                'studentStatuses',
                'studentData'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Step 1: Store Student Details In Session
    |--------------------------------------------------------------------------
    */

    public function storeStudentStep(
        Request $request
    ) {
        $validated =
            $request->validate([
                'external_id' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:students,external_id',
                ],

                'student_status_id' => [
                    'required',
                    'integer',
                    'exists:student_statuses,id',
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

                'date_of_birth' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Check Status Is Active
        |--------------------------------------------------------------------------
        */

        $statusAvailable =
            StudentStatus::where(
                'id',
                $validated[
                    'student_status_id'
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->exists();


        if (!$statusAvailable) {

            return back()
                ->withInput()
                ->withErrors([
                    'student_status_id' =>
                        'The selected student status is unavailable.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Clean Student Data
        |--------------------------------------------------------------------------
        */

        $validated[
            'external_id'
        ] =
            trim(
                $validated[
                    'external_id'
                ]
            );


        $validated[
            'first_name'
        ] =
            trim(
                $validated[
                    'first_name'
                ]
            );


        $validated[
            'last_name'
        ] =
            trim(
                $validated[
                    'last_name'
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Store In Session
        |--------------------------------------------------------------------------
        */

        session([
            'student_registration.student' =>
                $validated,
        ]);


        return redirect()
            ->route(
                'admin.student-registration.guardians'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Step 2: Guardian Details
    |--------------------------------------------------------------------------
    */

    public function guardianStep()
    {
        if (
            !session()->has(
                'student_registration.student'
            )
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.student'
                )
                ->with(
                    'error',
                    'Complete the student details first.'
                );
        }


        $guardianData =
            session(
                'student_registration.guardians',
                [
                    'new' => [],
                    'primary_guardian' =>
                        'new:0',
                ]
            );


        return view(
            'admin.student-registration.guardians',
            compact(
                'guardianData'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Step 2: Store Guardian Details In Session
    |--------------------------------------------------------------------------
    */

    public function storeGuardianStep(
        Request $request
    ) {
        if (
            !session()->has(
                'student_registration.student'
            )
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.student'
                );
        }


        $validated =
            $request->validate([
                'new_guardians' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'new_guardians.*.first_name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'new_guardians.*.last_name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'new_guardians.*.email' => [
                    'nullable',
                    'email',
                    'max:150',
                ],

                'new_guardians.*.phone' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'new_guardians.*.relationship' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'new_guardians.*.address' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                'new_guardians.*.is_emergency_contact' => [
                    'nullable',
                    'boolean',
                ],

                'primary_guardian' => [
                    'required',
                    'string',
                    'regex:/^new:[0-9]+$/',
                ],
            ]);


        $newGuardians =
            $validated[
                'new_guardians'
            ];


        $primaryGuardian =
            $validated[
                'primary_guardian'
            ];


        $primaryIndex =
            (int) str_replace(
                'new:',
                '',
                $primaryGuardian
            );


        if (
            !array_key_exists(
                $primaryIndex,
                $newGuardians
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'primary_guardian' =>
                        'Please select a valid primary guardian.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Clean Guardian Data
        |--------------------------------------------------------------------------
        */

        $guardianData = [];


        foreach (
            $newGuardians
            as $index => $guardian
        ) {

            $guardianData[] = [

                'first_name' =>
                    trim(
                        $guardian[
                            'first_name'
                        ]
                    ),

                'last_name' =>
                    trim(
                        $guardian[
                            'last_name'
                        ]
                    ),

                'email' =>
                    !empty(
                        $guardian[
                            'email'
                        ]
                    )
                        ? trim(
                            $guardian[
                                'email'
                            ]
                        )
                        : null,

                'phone' =>
                    trim(
                        $guardian[
                            'phone'
                        ]
                    ),

                'relationship' =>
                    !empty(
                        $guardian[
                            'relationship'
                        ]
                    )
                        ? trim(
                            $guardian[
                                'relationship'
                            ]
                        )
                        : null,

                'address' =>
                    !empty(
                        $guardian[
                            'address'
                        ]
                    )
                        ? trim(
                            $guardian[
                                'address'
                            ]
                        )
                        : null,

                'is_primary' =>
                    $primaryGuardian
                    ===
                    'new:' . $index,

                'is_emergency_contact' =>
                    !empty(
                        $guardian[
                            'is_emergency_contact'
                        ]
                    ),
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Store Guardian Data
        |--------------------------------------------------------------------------
        */

        session([
            'student_registration.guardians' => [

                'new' =>
                    $guardianData,

                'primary_guardian' =>
                    $primaryGuardian,
            ],
        ]);


        return redirect()
            ->route(
                'admin.student-registration.enrolments'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Step 3: Class Enrolment
    |--------------------------------------------------------------------------
    */

    public function enrolmentStep()
    {
        if (
            !session()->has(
                'student_registration.student'
            )
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.student'
                )
                ->with(
                    'error',
                    'Complete the student details first.'
                );
        }


        if (
            !session()->has(
                'student_registration.guardians'
            )
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.guardians'
                )
                ->with(
                    'error',
                    'Complete the guardian details first.'
                );
        }


        $studentData =
            session(
                'student_registration.student'
            );


        $guardianData =
            session(
                'student_registration.guardians'
            );


        /*
        |--------------------------------------------------------------------------
        | Load Active Class Offerings
        |--------------------------------------------------------------------------
        */

        $sectionOfferings =
            SectionOffering::query()
                ->select(
                    'section_offerings.*'
                )
                ->join(
                    'days',
                    'days.id',
                    '=',
                    'section_offerings.day_id'
                )
                ->join(
                    'sections',
                    'sections.id',
                    '=',
                    'section_offerings.section_id'
                )
                ->with([
                    'day',
                    'section',
                ])
                ->withCount([
                    'enrolments as allocated_seats' =>
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

                    'enrolments as wishlist_count' =>
                        function ($query) {

                            $query
                                ->where(
                                    'is_active',
                                    true
                                )
                                ->where(
                                    'is_wishlist',
                                    true
                                );
                        },
                ])
                ->where(
                    'section_offerings.is_active',
                    true
                )
                ->where(
                    'days.is_active',
                    true
                )
                ->where(
                    'sections.is_active',
                    true
                )
                ->orderBy(
                    'days.sort_order',
                    'asc'
                )
                ->orderBy(
                    'sections.section_name',
                    'asc'
                )
                ->orderBy(
                    'section_offerings.start_time',
                    'asc'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Calculate Available Seats
        |--------------------------------------------------------------------------
        */

        foreach (
            $sectionOfferings
            as $offering
        ) {

            $offering
                ->available_seats =
                max(
                    0,
                    $offering
                        ->max_seats
                    -
                    $offering
                        ->allocated_seats
                );
        }


        return view(
            'admin.student-registration.enrolments',
            compact(
                'studentData',
                'guardianData',
                'sectionOfferings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Complete Registration
    |--------------------------------------------------------------------------
    */

    public function complete(
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | Check Wizard Session
        |--------------------------------------------------------------------------
        */

        if (
            !session()->has(
                'student_registration.student'
            )
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.student'
                );
        }


        if (
            !session()->has(
                'student_registration.guardians'
            )
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.guardians'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Selections
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([

                'confirmed_ids' => [
                    'nullable',
                    'array',
                ],

                'confirmed_ids.*' => [
                    'integer',
                    'distinct',
                    'exists:section_offerings,id',
                ],

                'wishlist_ids' => [
                    'nullable',
                    'array',
                ],

                'wishlist_ids.*' => [
                    'integer',
                    'distinct',
                    'exists:section_offerings,id',
                ],
            ]);


        $confirmedIds =
            array_map(
                'intval',
                $validated[
                    'confirmed_ids'
                ] ?? []
            );


        $wishlistIds =
            array_map(
                'intval',
                $validated[
                    'wishlist_ids'
                ] ?? []
            );


        /*
        |--------------------------------------------------------------------------
        | Require At Least One Selection
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $confirmedIds
            )
            &&
            empty(
                $wishlistIds
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'Select at least one class or wishlist option.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Same Offering Cannot Be Confirmed And Wishlist
        |--------------------------------------------------------------------------
        */

        $duplicateIds =
            array_intersect(
                $confirmedIds,
                $wishlistIds
            );


        if (
            !empty(
                $duplicateIds
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'A class offering cannot be selected as both confirmed and wishlist.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Combined Selected Offering IDs
        |--------------------------------------------------------------------------
        */

        $selectedIds =
            array_values(
                array_unique(
                    array_merge(
                        $confirmedIds,
                        $wishlistIds
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Load Selected Offerings
        |--------------------------------------------------------------------------
        |
        | This is also used to enforce:
        |
        | ONE OFFERING PER CLASS / SECTION.
        |
        | Example:
        | English Monday 6 PM + English Tuesday 6 PM = NOT allowed.
        |
        | English + 3A Math = allowed.
        |--------------------------------------------------------------------------
        */

        $selectedOfferings =
            SectionOffering::with([
                'section',
                'day',
            ])
                ->whereIn(
                    'id',
                    $selectedIds
                )
                ->where(
                    'is_active',
                    true
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Make Sure Every Selected Offering Is Active
        |--------------------------------------------------------------------------
        */

        if (
            $selectedOfferings->count()
            !==
            count(
                $selectedIds
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'One or more selected classes are unavailable.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Only One Time / Offering Per Class
        |--------------------------------------------------------------------------
        */

        $duplicateSections =
            $selectedOfferings
                ->groupBy(
                    'section_id'
                )
                ->filter(
                    function ($offerings) {

                        return
                            $offerings->count()
                            >
                            1;
                    }
                );


        if (
            $duplicateSections
                ->isNotEmpty()
        ) {

            $duplicateClassNames =
                $duplicateSections
                    ->map(
                        function ($offerings) {

                            return
                                $offerings
                                    ->first()
                                    ->section
                                    ?->section_name
                                ??
                                'Class';
                        }
                    )
                    ->unique()
                    ->implode(', ');


            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'Only one class time can be selected for each class. Please choose only one offering for: '
                        .
                        $duplicateClassNames
                        .
                        '.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Load Session Data
        |--------------------------------------------------------------------------
        */

        $studentData =
            session(
                'student_registration.student'
            );


        $guardianData =
            session(
                'student_registration.guardians'
            );


        /*
        |--------------------------------------------------------------------------
        | Recheck Student ID
        |--------------------------------------------------------------------------
        */

        $studentIdExists =
            Student::where(
                'external_id',
                $studentData[
                    'external_id'
                ]
            )
                ->exists();


        if (
            $studentIdExists
        ) {

            return redirect()
                ->route(
                    'admin.student-registration.student'
                )
                ->withErrors([
                    'external_id' =>
                        'This student ID has already been used.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Recheck Status
        |--------------------------------------------------------------------------
        */

        $statusAvailable =
            StudentStatus::where(
                'id',
                $studentData[
                    'student_status_id'
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->exists();


        if (!$statusAvailable) {

            return redirect()
                ->route(
                    'admin.student-registration.student'
                )
                ->withErrors([
                    'student_status_id' =>
                        'The selected student status is unavailable.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Save Everything
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $studentData,
                $guardianData,
                $wishlistIds,
                $selectedIds
            ) {

                /*
                |--------------------------------------------------------------------------
                | Create Student
                |--------------------------------------------------------------------------
                */

                $student =
                    Student::create([

                        'external_id' =>
                            $studentData[
                                'external_id'
                            ],

                        'student_status_id' =>
                            $studentData[
                                'student_status_id'
                            ],

                        'first_name' =>
                            $studentData[
                                'first_name'
                            ],

                        'last_name' =>
                            $studentData[
                                'last_name'
                            ],

                        'email' =>
                            null,

                        'phone' =>
                            null,

                        'date_of_birth' =>
                            $studentData[
                                'date_of_birth'
                            ],

                        'address' =>
                            null,

                        'can_leave_alone' =>
                            false,

                        'notes' =>
                            null,

                        'join_date' =>
                            now()
                                ->toDateString(),

                        'is_active' =>
                            true,

                        'inactive_since' =>
                            null,
                    ]);


                /*
                |--------------------------------------------------------------------------
                | Create Guardians
                |--------------------------------------------------------------------------
                */

                foreach (
                    $guardianData[
                        'new'
                    ] ?? []
                    as $newGuardian
                ) {

                    $guardian =
                        Guardian::create([

                            'user_id' =>
                                null,

                            'first_name' =>
                                $newGuardian[
                                    'first_name'
                                ],

                            'last_name' =>
                                $newGuardian[
                                    'last_name'
                                ],

                            'email' =>
                                $newGuardian[
                                    'email'
                                ] ?? null,

                            'phone' =>
                                $newGuardian[
                                    'phone'
                                ],

                            'address' =>
                                $newGuardian[
                                    'address'
                                ] ?? null,

                            'is_active' =>
                                true,
                        ]);


                    $student
                        ->guardians()
                        ->attach(
                            $guardian->id,
                            [

                                'relationship' =>
                                    $newGuardian[
                                        'relationship'
                                    ] ?? null,

                                'is_primary' =>
                                    $newGuardian[
                                        'is_primary'
                                    ] ?? false,

                                'is_emergency_contact' =>
                                    $newGuardian[
                                        'is_emergency_contact'
                                    ] ?? false,
                            ]
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | Create Enrolments
                |--------------------------------------------------------------------------
                */

                foreach (
                    $selectedIds
                    as $offeringId
                ) {

                    $isWishlist =
                        in_array(
                            $offeringId,
                            $wishlistIds,
                            true
                        );


                    /*
                     * Lock offering during
                     * capacity check.
                     */
                    $offering =
                        SectionOffering::where(
                            'id',
                            $offeringId
                        )
                            ->where(
                                'is_active',
                                true
                            )
                            ->lockForUpdate()
                            ->first();


                    if (!$offering) {

                        throw ValidationException::withMessages([
                            'classes' =>
                                'A selected class is no longer available.',
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Capacity Check
                    |--------------------------------------------------------------------------
                    */

                    if (!$isWishlist) {

                        $allocatedSeats =
                            Enrolment::where(
                                'section_offering_id',
                                $offering->id
                            )
                                ->where(
                                    'is_active',
                                    true
                                )
                                ->where(
                                    'is_wishlist',
                                    false
                                )
                                ->count();


                        if (
                            $allocatedSeats
                            >=
                            $offering
                                ->max_seats
                        ) {

                            throw ValidationException::withMessages([
                                'classes' =>
                                    'One selected class is now full. Select it as wishlist instead.',
                            ]);
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Create Enrolment
                    |--------------------------------------------------------------------------
                    */

                    Enrolment::create([

                        'student_id' =>
                            $student->id,

                        'section_offering_id' =>
                            $offering->id,

                        'wishlist_for_enrolment_id' =>
                            null,

                        'enrolment_date' =>
                            now()
                                ->toDateString(),

                        'is_wishlist' =>
                            $isWishlist,

                        'wishlist_status' =>
                            $isWishlist
                                ? 'pending'
                                : null,

                        'is_active' =>
                            true,
                    ]);
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Clear Registration Session
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'student_registration'
        );


        return redirect()
            ->route(
                'admin.students.index'
            )
            ->with(
                'success',
                'Student, guardian and class enrolment details were created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Registration
    |--------------------------------------------------------------------------
    */

    public function cancel()
    {
        session()->forget(
            'student_registration'
        );


        return redirect()
            ->route(
                'dashboard'
            )
            ->with(
                'success',
                'Student registration cancelled.'
            );
    }
}
