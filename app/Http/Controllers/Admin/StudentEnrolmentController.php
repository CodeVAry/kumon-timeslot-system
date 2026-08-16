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
    public function create(Student $student)
    {
        $student->load([
            'studentStatus',
            'enrolments',
        ]);

        /*
         * Get student's existing active offering IDs.
         * We do not want the same offering added twice.
         */
        $existingOfferingIds = $student->enrolments
            ->where('is_active', true)
            ->pluck('section_offering_id')
            ->toArray();

        /*
         * Get all active class offerings.
         */
        $offerings = SectionOffering::with([
            'section',
            'day',
        ])
            ->withCount([
                'enrolments as allocated_seats' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->where('is_wishlist', false);
                },

                'enrolments as wishlist_count' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->where('is_wishlist', true);
                },
            ])
            ->where('is_active', true)
            ->whereNotIn(
                'id',
                $existingOfferingIds
            )
            ->orderBy('day_id')
            ->orderBy('start_time')
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
    | Store New Class
    |--------------------------------------------------------------------------
    */
    public function store(
        Request $request,
        Student $student
    ) {
        $validated = $request->validate([
            'section_offering_id' => [
                'required',
                'integer',

                Rule::exists(
                    'section_offerings',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                }),
            ],

            'enrolment_type' => [
                'required',
                Rule::in([
                    'confirmed',
                    'wishlist',
                ]),
            ],
        ]);

        /*
         * Prevent duplicate active enrolment.
         */
        $alreadyExists = Enrolment::where(
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
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'section_offering_id' =>
                    'This student already has this class.',
            ]);
        }


        DB::transaction(function () use ($validated, $student) {

            $offering = SectionOffering::where(
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


            $isWishlist =
                $validated['enrolment_type']
                === 'wishlist';


            /*
             * Wishlist does not consume a seat.
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
                    $allocatedSeats >=
                    $offering->max_seats
                ) {
                    throw ValidationException::withMessages([
                        'section_offering_id' =>
                            'This class is full. You can add the student to the wishlist instead.',
                    ]);
                }
            }


            Enrolment::create([
                'student_id' =>
                    $student->id,

                'section_offering_id' =>
                    $offering->id,

                'enrolment_date' =>
                    now()->toDateString(),

                'is_wishlist' =>
                    $isWishlist,

                'is_active' =>
                    true,
            ]);
        });


        return redirect()
            ->route(
                'admin.students.show',
                $student
            )
            ->with(
                'success',
                $validated['enrolment_type']
                === 'wishlist'
                ? 'Class added to wishlist successfully.'
                : 'Class added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Select Schedule To Edit
    |--------------------------------------------------------------------------
    */
    public function editList(Student $student)
    {
        $student->load([
            'enrolments' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->where('is_wishlist', false);
            },

            'enrolments.sectionOffering.section',
            'enrolments.sectionOffering.day',
        ]);

        $enrolments = $student->enrolments;

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
    | Edit One Current Schedule
    |--------------------------------------------------------------------------
    */
    public function edit(
        Student $student,
        Enrolment $enrolment
    ) {
        /*
         * Make sure this enrolment
         * belongs to this student.
         */
        if ($enrolment->student_id !== $student->id) {
            abort(404);
        }

        /*
         * Only active confirmed classes
         * can be edited.
         */
        if (
            !$enrolment->is_active ||
            $enrolment->is_wishlist
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
         * Get other classes already
         * registered by this student.
         *
         * Do not allow the student
         * to move into the same class
         * they already have.
         */
        $existingOfferingIds = Enrolment::where(
            'student_id',
            $student->id
        )
            ->where('is_active', true)
            ->where('id', '!=', $enrolment->id)
            ->pluck('section_offering_id')
            ->toArray();

        /*
         * Load active offerings.
         */
        $offerings = SectionOffering::with([
            'section',
            'day',
        ])
            ->withCount([
                'enrolments as allocated_seats' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->where('is_wishlist', false);
                },
            ])
            ->where('is_active', true)
            ->whereNotIn(
                'id',
                $existingOfferingIds
            )
            ->orderBy('day_id')
            ->orderBy('start_time')
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
         * Make sure enrolment
         * belongs to student.
         */
        if ($enrolment->student_id !== $student->id) {
            abort(404);
        }

        if (
            !$enrolment->is_active ||
            $enrolment->is_wishlist
        ) {
            abort(404);
        }

        $validated = $request->validate([
            'section_offering_id' => [
                'required',
                'integer',
                Rule::exists(
                    'section_offerings',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                }),
            ],
        ]);

        /*
         * No change.
         */
        if (
            (int) $validated['section_offering_id']
            ===
            (int) $enrolment->section_offering_id
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
         * Prevent duplicate class.
         */
        $duplicate = Enrolment::where(
            'student_id',
            $student->id
        )
            ->where(
                'section_offering_id',
                $validated['section_offering_id']
            )
            ->where('is_active', true)
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

        DB::transaction(function () use ($validated, $enrolment) {
            /*
             * Lock selected offering while
             * checking capacity.
             */
            $newOffering = SectionOffering::where(
                'id',
                $validated['section_offering_id']
            )
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Count current confirmed students.
             */
            $allocatedSeats = Enrolment::where(
                'section_offering_id',
                $newOffering->id
            )
                ->where('is_active', true)
                ->where('is_wishlist', false)
                ->count();

            /*
             * Make sure a seat is available.
             */
            if (
                $allocatedSeats >=
                $newOffering->max_seats
            ) {
                throw ValidationException::withMessages([
                    'section_offering_id' =>
                        'The selected class is full. Please select another class.',
                ]);
            }

            /*
             * Move the existing enrolment.
             */
            $enrolment->update([
                'section_offering_id' =>
                    $newOffering->id,

                'is_wishlist' => false,

                'is_active' => true,
            ]);
        });

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
    public function remove(Student $student)
    {
        $student->load([
            'enrolments' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->where('is_wishlist', false);
            },

            'enrolments.sectionOffering.section',
            'enrolments.sectionOffering.day',
        ]);


        $enrolments =
            $student->enrolments;


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
            $enrolment->student_id
            !== $student->id
        ) {
            abort(404);
        }


        if (!$enrolment->is_active) {
            return redirect()
                ->route(
                    'admin.students.show',
                    $student
                );
        }

        $enrolment->update([
            'is_active' => false,
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
