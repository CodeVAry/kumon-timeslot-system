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
     * Step 1: Show student details page.
     */
    public function studentStep()
    {
        $studentStatuses = StudentStatus::where(
            'is_active',
            true
        )
            ->orderBy('status_name', 'asc')
            ->get();

        $studentData = session(
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
     * Step 1: Validate and store student details
     * temporarily in the session.
     */
    public function storeStudentStep(Request $request)
    {
        $validated = $request->validate([
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

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $statusAvailable = StudentStatus::where(
            'id',
            $validated['student_status_id']
        )
            ->where('is_active', true)
            ->exists();

        if (!$statusAvailable) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_status_id' =>
                        'The selected student status is unavailable.',
                ]);
        }

        $validated['external_id'] = trim(
            $validated['external_id']
        );

        $validated['first_name'] = trim(
            $validated['first_name']
        );

        $validated['last_name'] = trim(
            $validated['last_name']
        );

        $validated['email'] = trim(
            $validated['email']
        );

        $validated['phone'] = trim(
            $validated['phone']
        );

        $validated['address'] = trim(
            $validated['address']
        );

        if (!empty($validated['notes'])) {
            $validated['notes'] = trim(
                $validated['notes']
            );
        }

        session([
            'student_registration.student' =>
                $validated,
        ]);

        return redirect()->route(
            'admin.student-registration.guardians'
        );
    }

    /*
     * Step 2: Show guardian details page.
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

        $guardianData = session(
            'student_registration.guardians',
            [
                'new' => [],
                'primary_guardian' => 'new:0',
            ]
        );

        return view(
            'admin.student-registration.guardians',
            compact('guardianData')
        );
    }

    /*
     * Step 2: Validate and store guardian details
     * temporarily in the session.
     */
    public function storeGuardianStep(Request $request)
    {
        if (
            !session()->has(
                'student_registration.student'
            )
        ) {
            return redirect()->route(
                'admin.student-registration.student'
            );
        }

        $validated = $request->validate([
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
            $validated['new_guardians'];

        $primaryGuardian =
            $validated['primary_guardian'];

        $primaryIndex = (int) str_replace(
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

        $guardianData = [];

        foreach (
            $newGuardians as
            $index => $guardian
        ) {
            $guardianData[] = [
                'first_name' =>
                    trim($guardian['first_name']),

                'last_name' =>
                    trim($guardian['last_name']),

                'email' =>
                    !empty($guardian['email'])
                        ? trim($guardian['email'])
                        : null,

                'phone' =>
                    trim($guardian['phone']),

                'relationship' =>
                    !empty($guardian['relationship'])
                        ? trim(
                            $guardian['relationship']
                        )
                        : null,

                'address' =>
                    !empty($guardian['address'])
                        ? trim($guardian['address'])
                        : null,

                'is_primary' =>
                    $primaryGuardian ===
                    'new:' . $index,

                'is_emergency_contact' =>
                    !empty(
                        $guardian[
                            'is_emergency_contact'
                        ]
                    ),
            ];
        }

        session([
            'student_registration.guardians' => [
                'new' => $guardianData,
                'primary_guardian' =>
                    $primaryGuardian,
            ],
        ]);

        return redirect()->route(
            'admin.student-registration.enrolments'
        );
    }

    /*
     * Step 3: Show available classes.
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

        $studentData = session(
            'student_registration.student'
        );

        $guardianData = session(
            'student_registration.guardians'
        );

        /*
         * Count only confirmed active enrolments.
         * Wishlist records do not use seats.
         */
        $sectionOfferings = SectionOffering::query()
            ->select('section_offerings.*')
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
                            ->where('is_active', true)
                            ->where(
                                'is_wishlist',
                                false
                            );
                    },

                'enrolments as wishlist_count' =>
                    function ($query) {
                        $query
                            ->where('is_active', true)
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
                'section_offerings.start_time',
                'asc'
            )
            ->orderBy(
                'sections.section_name',
                'asc'
            )
            ->get();

        foreach (
            $sectionOfferings as $offering
        ) {
            $offering->available_seats = max(
                0,
                $offering->max_seats -
                $offering->allocated_seats
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
     * Complete registration and save everything.
     */
    public function complete(Request $request)
    {
        if (
            !session()->has(
                'student_registration.student'
            )
        ) {
            return redirect()->route(
                'admin.student-registration.student'
            );
        }

        if (
            !session()->has(
                'student_registration.guardians'
            )
        ) {
            return redirect()->route(
                'admin.student-registration.guardians'
            );
        }

        $validated = $request->validate([
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

        $confirmedIds = array_map(
            'intval',
            $validated['confirmed_ids'] ?? []
        );

        $wishlistIds = array_map(
            'intval',
            $validated['wishlist_ids'] ?? []
        );

        if (
            empty($confirmedIds) &&
            empty($wishlistIds)
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'Select at least one class or wishlist option.',
                ]);
        }

        /*
         * One class cannot be selected as both
         * confirmed and wishlist.
         */
        $duplicateIds = array_intersect(
            $confirmedIds,
            $wishlistIds
        );

        if (!empty($duplicateIds)) {
            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'A class cannot be selected as both confirmed and wishlist.',
                ]);
        }

        $selectedIds = array_values(
            array_unique(
                array_merge(
                    $confirmedIds,
                    $wishlistIds
                )
            )
        );

        /*
         * Always process classes in the same order.
         */
        sort($selectedIds);

        $activeOfferingCount =
            SectionOffering::whereIn(
                'id',
                $selectedIds
            )
                ->where('is_active', true)
                ->count();

        if (
            $activeOfferingCount !==
            count($selectedIds)
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'classes' =>
                        'One or more selected classes are unavailable.',
                ]);
        }

        $studentData = session(
            'student_registration.student'
        );

        $guardianData = session(
            'student_registration.guardians'
        );

        /*
         * Check that the student ID is still available.
         */
        $studentIdExists = Student::where(
            'external_id',
            $studentData['external_id']
        )->exists();

        if ($studentIdExists) {
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
         * Check that the student status is still active.
         */
        $statusAvailable =
            StudentStatus::where(
                'id',
                $studentData[
                    'student_status_id'
                ]
            )
                ->where('is_active', true)
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
         * Student, guardians and enrolments
         * are saved together.
         */
        DB::transaction(function () use (
            $studentData,
            $guardianData,
            $wishlistIds,
            $selectedIds
        ) {
            /*
             * Create student.
             */
            $student = Student::create([
                'external_id' =>
                    $studentData['external_id'],

                'student_status_id' =>
                    $studentData[
                        'student_status_id'
                    ],

                'first_name' =>
                    $studentData['first_name'],

                'last_name' =>
                    $studentData['last_name'],

                'email' =>
                    $studentData['email'],

                'phone' =>
                    $studentData['phone'],

                'date_of_birth' =>
                    $studentData['date_of_birth'],

                'address' =>
                    $studentData['address'],

                'can_leave_alone' =>
                    $studentData[
                        'can_leave_alone'
                    ],

                'notes' =>
                    $studentData['notes'] ?? null,

                'join_date' =>
                    now()->toDateString(),

                'is_active' =>
                    true,

                'inactive_since' =>
                    null,
            ]);

            /*
             * Create guardians and link them
             * to the student.
             */
            foreach (
                $guardianData['new'] ?? []
                as $newGuardian
            ) {
                $guardian = Guardian::create([
                    'user_id' => null,

                    'first_name' =>
                        $newGuardian['first_name'],

                    'last_name' =>
                        $newGuardian['last_name'],

                    'email' =>
                        $newGuardian['email']
                        ?? null,

                    'phone' =>
                        $newGuardian['phone'],

                    'address' =>
                        $newGuardian['address']
                        ?? null,

                    'is_active' =>
                        true,
                ]);

                $student->guardians()->attach(
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
             * Create confirmed or wishlist enrolments.
             */
            foreach (
                $selectedIds as $offeringId
            ) {
                $isWishlist = in_array(
                    $offeringId,
                    $wishlistIds,
                    true
                );

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
                 * Only confirmed enrolments use seats.
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
                            'classes' =>
                                'One selected class is now full. Select it as wishlist instead.',
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
            }
        });

        /*
         * Clear the temporary wizard information.
         */
        session()->forget(
            'student_registration'
        );

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Student, guardian and class enrolment details were created successfully.'
            );
    }

    /*
     * Cancel registration.
     */
    public function cancel()
    {
        session()->forget(
            'student_registration'
        );

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Student registration cancelled.'
            );
    }
}
