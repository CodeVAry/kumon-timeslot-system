<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentEnrolmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Add Class Page
    |--------------------------------------------------------------------------
    */

    public function create(
        Student $student
    ) {
        $student->load([
            'studentStatus',
            'enrolments',
        ]);


        /*
         * Existing active offering IDs.
         */
        $existingOfferingIds =
            $student
                ->enrolments
                ->where(
                    'is_active',
                    true
                )
                ->pluck(
                    'section_offering_id'
                )
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Active Offerings
        |--------------------------------------------------------------------------
        */

        $offerings =
            SectionOffering::with([
                'section',
                'day',
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
                    'is_active',
                    true
                )
                ->whereNotIn(
                    'id',
                    $existingOfferingIds
                )
                ->orderBy(
                    'day_id'
                )
                ->orderBy(
                    'start_time'
                )
                ->get();


        return view(
            'admin.student-enrolments.create',
            compact(
                'student',
                'offerings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store New Class / Direct Wishlist
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Student $student
    ) {
        $validated =
            $request->validate([
                'section_offering_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'section_offering_ids.*' => [
                    'required',
                    'integer',
                    'distinct',

                    Rule::exists(
                        'section_offerings',
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

                'enrolment_type' => [
                    'required',

                    Rule::in([
                        'confirmed',
                        'wishlist',
                    ]),
                ],
            ]);


        $selectedOfferingIds =
            collect(
                $validated[
                    'section_offering_ids'
                ]
            )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values();


        $existingOfferingIds =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->whereIn(
                    'section_offering_id',
                    $selectedOfferingIds
                )
                ->where(
                    'is_active',
                    true
                )
                ->pluck(
                    'section_offering_id'
                );


        if (
            $existingOfferingIds
                ->isNotEmpty()
        ) {

            throw ValidationException::withMessages([
                'section_offering_ids' =>
                    'One or more selected classes are already assigned to this student.',
            ]);
        }


        $isWishlist =
            $validated[
                'enrolment_type'
            ]
            ===
            'wishlist';


        DB::transaction(
            function () use (
                $selectedOfferingIds,
                $student,
                $isWishlist
            ) {

                $offerings =
                    SectionOffering::whereIn(
                        'id',
                        $selectedOfferingIds
                    )
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy(
                            'id'
                        )
                        ->lockForUpdate()
                        ->get();


                if (
                    $offerings->count()
                    !==
                    $selectedOfferingIds->count()
                ) {

                    throw ValidationException::withMessages([
                        'section_offering_ids' =>
                            'One or more selected classes are no longer available. Please refresh and try again.',
                    ]);
                }


                if (!$isWishlist) {

                    foreach (
                        $offerings
                        as $offering
                    ) {

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
                            $offering->max_seats
                        ) {

                            $offering->loadMissing([
                                'section',
                                'day',
                            ]);


                            $className =
                                $offering
                                    ->section
                                    ?->section_name
                                ??
                                'Selected class';


                            $dayName =
                                $offering
                                    ->day
                                    ?->day_name
                                ??
                                '';


                            throw ValidationException::withMessages([
                                'section_offering_ids' =>
                                    $className
                                    .
                                    (
                                        $dayName
                                            ? ' on ' . $dayName
                                            : ''
                                    )
                                    .
                                    ' is full. Choose another class or add the selections to the wishlist.',
                            ]);
                        }
                    }
                }


                foreach (
                    $offerings
                    as $offering
                ) {

                    Enrolment::create([
                        'student_id' =>
                            $student->id,

                        'section_offering_id' =>
                            $offering->id,

                        'wishlist_for_enrolment_id' =>
                            null,

                        'enrolment_date' =>
                            now()->toDateString(),

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


        $classCount =
            $selectedOfferingIds
                ->count();


        return redirect()
            ->route(
                'admin.students.show',
                $student
            )
            ->with(
                'success',
                $isWishlist
                    ? (
                        $classCount === 1
                            ? 'Student added to the wishlist successfully.'
                            : $classCount . ' classes added to the wishlist successfully.'
                    )
                    : (
                        $classCount === 1
                            ? 'Class added successfully.'
                            : $classCount . ' classes added successfully.'
                    )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Select Schedule To Edit
    |--------------------------------------------------------------------------
    */

    public function editList(
        Student $student
    ) {
        $student->load([
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
        ]);


        $enrolments =
            $student
                ->enrolments;


        return view(
            'admin.student-enrolments.edit-list',
            compact(
                'student',
                'enrolments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Current Schedule
    |--------------------------------------------------------------------------
    */

    public function edit(
        Student $student,
        Enrolment $enrolment
    ) {
        /*
         * Must belong to student.
         */
        if (
            (int)
            $enrolment
                ->student_id
            !==
            (int)
            $student->id
        ) {

            abort(404);
        }


        /*
         * Only active confirmed classes.
         */
        if (
            !$enrolment
                ->is_active
            ||
            $enrolment
                ->is_wishlist
        ) {

            abort(404);
        }


        $student->load([
            'guardians',
        ]);


        $enrolment->load([
            'sectionOffering.section',
            'sectionOffering.day',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Other Active Classes
        |--------------------------------------------------------------------------
        */

        $existingOfferingIds =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'id',
                    '!=',
                    $enrolment->id
                )
                ->pluck(
                    'section_offering_id'
                )
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Active Offerings
        |--------------------------------------------------------------------------
        */

        $offerings =
            SectionOffering::with([
                'section',
                'day',
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
                ])
                ->where(
                    'is_active',
                    true
                )
                ->whereNotIn(
                    'id',
                    $existingOfferingIds
                )
                ->orderBy(
                    'day_id'
                )
                ->orderBy(
                    'start_time'
                )
                ->get();


        return view(
            'admin.student-enrolments.edit',
            compact(
                'student',
                'enrolment',
                'offerings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Current Schedule
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Student $student,
        Enrolment $enrolment
    ) {
        /*
         * Must belong to student.
         */
        if (
            (int)
            $enrolment
                ->student_id
            !==
            (int)
            $student->id
        ) {

            abort(404);
        }


        if (
            !$enrolment
                ->is_active
            ||
            $enrolment
                ->is_wishlist
        ) {

            abort(404);
        }


        $validated =
            $request->validate([
                'section_offering_id' => [
                    'required',
                    'integer',

                    Rule::exists(
                        'section_offerings',
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
            ]);


        /*
        |--------------------------------------------------------------------------
        | No Change
        |--------------------------------------------------------------------------
        */

        if (
            (int)
            $validated[
                'section_offering_id'
            ]
            ===
            (int)
            $enrolment
                ->section_offering_id
        ) {

            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                )
                ->with(
                    'success',
                    'Schedule has not changed.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate
        |--------------------------------------------------------------------------
        */

        $duplicate =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'section_offering_id',
                    $validated[
                        'section_offering_id'
                    ]
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'id',
                    '!=',
                    $enrolment->id
                )
                ->exists();


        if ($duplicate) {

            throw ValidationException::withMessages([
                'section_offering_id' =>
                    'This student is already registered in the selected class.',
            ]);
        }


        DB::transaction(
            function () use (
                $validated,
                $enrolment
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock New Offering
                |--------------------------------------------------------------------------
                */

                $newOffering =
                    SectionOffering::where(
                        'id',
                        $validated[
                            'section_offering_id'
                        ]
                    )
                        ->where(
                            'is_active',
                            true
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | Capacity
                |--------------------------------------------------------------------------
                */

                $allocatedSeats =
                    Enrolment::where(
                        'section_offering_id',
                        $newOffering->id
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
                    $newOffering
                        ->max_seats
                ) {

                    throw ValidationException::withMessages([
                        'section_offering_id' =>
                            'The selected class is full. Please select another class.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Move Enrolment
                |--------------------------------------------------------------------------
                */

                $enrolment->update([
                    'section_offering_id' =>
                        $newOffering->id,

                    'is_wishlist' =>
                        false,

                    'wishlist_status' =>
                        null,

                    'is_active' =>
                        true,
                ]);
            }
        );


        return redirect()
            ->route(
                'admin.students.show',
                $student
            )
            ->with(
                'success',
                'Student schedule updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Select Class To Remove
    |--------------------------------------------------------------------------
    */

    public function remove(
        Student $student
    ) {
        $student->load([
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
        ]);


        $enrolments =
            $student
                ->enrolments;


        return view(
            'admin.student-enrolments.remove',
            compact(
                'student',
                'enrolments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Remove Class
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Student $student,
        Enrolment $enrolment
    ) {
        if (
            (int)
            $enrolment
                ->student_id
            !==
            (int)
            $student->id
        ) {

            abort(404);
        }


        if (
            !$enrolment
                ->is_active
        ) {

            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                );
        }


        $enrolment->update([
            'is_active' =>
                false,
        ]);


        return redirect()
            ->route(
                'admin.students.show',
                $student
            )
            ->with(
                'success',
                'Class removed successfully.'
            );
    }
}
