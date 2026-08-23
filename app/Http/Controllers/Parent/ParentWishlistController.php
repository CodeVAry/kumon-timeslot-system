<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Guardian;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentWishlistController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get Guardian IDs
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
                function ($query) use (
                    $guardianIds
                ) {

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

    private function checkWishlistAccess(
        Enrolment $wishlist
    ) {
        $student =
            $this->getSelectedStudent();


        if (
            !$student
            ||
            $wishlist->student_id
                != $student->id
            ||
            !$wishlist->wishlist_status
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
                    'approved',
                    'rejected',
                    'cancelled',
                ]
            )
        ) {

            $status =
                'all';
        }


        /*
         * Any enrolment having wishlist_status
         * represents a wishlist request/history.
         */
        $query =
            Enrolment::with([
                'sectionOffering.section',
                'sectionOffering.day',
                'wishlistForEnrolment.sectionOffering.section',
                'wishlistForEnrolment.sectionOffering.day',
                'reviewedBy',
            ])
                ->where(
                    'student_id',
                    $student->id
                )
                ->whereNotNull(
                    'wishlist_status'
                );


        if (
            $status
            !==
            'all'
        ) {

            $query->where(
                'wishlist_status',
                $status
            );
        }


        $wishlists =
            $query
                ->orderByDesc(
                    'created_at'
                )
                ->paginate(20)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Counters
        |--------------------------------------------------------------------------
        */

        $pendingCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'pending'
                )
                ->count();


        $approvedCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'approved'
                )
                ->count();


        $rejectedCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'rejected'
                )
                ->count();


        $cancelledCount =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_status',
                    'cancelled'
                )
                ->count();


        return view(
            'parent.wishlist.index',
            compact(
                'student',
                'wishlists',
                'status',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
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


        /*
         * Current confirmed classes.
         */
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


        /*
         * Attach available offerings
         * to each current class.
         */
        foreach (
            $enrolments
            as $enrolment
        ) {

            $currentOffering =
                $enrolment
                    ->sectionOffering;


            if (!$currentOffering) {

                $enrolment
                    ->available_offerings =
                    collect();

                continue;
            }


            $availableOfferings =
                SectionOffering::with([
                    'section',
                    'day',
                ])
                    ->where(
                        'section_id',
                        $currentOffering
                            ->section_id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->where(
                        'id',
                        '!=',
                        $currentOffering->id
                    )
                    ->orderBy(
                        'day_id'
                    )
                    ->orderBy(
                        'start_time'
                    )
                    ->get();


            $enrolment
                ->available_offerings =
                $availableOfferings;
        }


        return view(
            'parent.wishlist.create',
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


        $validated =
            $request->validate(
                [
                    'enrolment_id' => [
                        'required',
                        'integer',
                        'exists:enrolments,id',
                    ],

                    'section_offering_id' => [
                        'required',
                        'integer',
                        'exists:section_offerings,id',
                    ],
                ],
                [
                    'enrolment_id.required' =>
                        'Please select a class.',

                    'section_offering_id.required' =>
                        'Please select your preferred class time.',
                ]
            );


        /*
         * Current confirmed enrolment.
         */
        $enrolment =
            Enrolment::with(
                'sectionOffering'
            )
                ->where(
                    'id',
                    $validated[
                        'enrolment_id'
                    ]
                )
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
                ->first();


        if (!$enrolment) {

            abort(403);
        }


        /*
         * One pending wishlist for
         * each current class.
         */
        $existingPending =
            Enrolment::where(
                'student_id',
                $student->id
            )
                ->where(
                    'wishlist_for_enrolment_id',
                    $enrolment->id
                )
                ->where(
                    'wishlist_status',
                    'pending'
                )
                ->exists();


        if ($existingPending) {

            return back()
                ->withInput()
                ->withErrors([
                    'enrolment_id' =>
                        'This class already has a pending wishlist request.',
                ]);
        }


        $currentOffering =
            $enrolment
                ->sectionOffering;


        if (!$currentOffering) {

            return back()
                ->withInput()
                ->withErrors([
                    'enrolment_id' =>
                        'The current class is unavailable.',
                ]);
        }


        $requestedOffering =
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
                ->first();


        if (!$requestedOffering) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_offering_id' =>
                        'The selected class time is unavailable.',
                ]);
        }


        /*
         * Same class only.
         */
        if (
            $requestedOffering
                ->section_id
            !=
            $currentOffering
                ->section_id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_offering_id' =>
                        'Please select another time for the same class.',
                ]);
        }


        if (
            $requestedOffering->id
            ==
            $currentOffering->id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_offering_id' =>
                        'Please select a different class time.',
                ]);
        }


        $wishlist =
            Enrolment::create([
                'student_id' =>
                    $student->id,

                'section_offering_id' =>
                    $requestedOffering->id,

                'wishlist_for_enrolment_id' =>
                    $enrolment->id,

                'enrolment_date' =>
                    now(),

                'is_wishlist' =>
                    true,

                'wishlist_status' =>
                    'pending',

                'requested_by_guardian_id' =>
                    Auth::guard(
                        'parent'
                    )->id(),

                'reviewed_by_user_id' =>
                    null,

                'reviewed_at' =>
                    null,

                'wishlist_review_note' =>
                    null,

                'is_active' =>
                    true,
            ]);


        return redirect()
            ->route(
                'parent.wishlist.show',
                $wishlist
            )
            ->with(
                'success',
                'Wishlist request submitted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        Enrolment $wishlist
    ) {
        $student =
            $this->checkWishlistAccess(
                $wishlist
            );


        $wishlist->load([
            'sectionOffering.section',
            'sectionOffering.day',
            'wishlistForEnrolment.sectionOffering.section',
            'wishlistForEnrolment.sectionOffering.day',
            'reviewedBy',
        ]);


        return view(
            'parent.wishlist.show',
            compact(
                'student',
                'wishlist'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        Enrolment $wishlist
    ) {
        $student =
            $this->checkWishlistAccess(
                $wishlist
            );


        if (
            $wishlist
                ->wishlist_status
            !==
            'pending'
        ) {

            return redirect()
                ->route(
                    'parent.wishlist.show',
                    $wishlist
                )
                ->with(
                    'error',
                    'Only pending wishlist requests can be edited.'
                );
        }


        $wishlist->load([
            'sectionOffering.section',
            'sectionOffering.day',
            'wishlistForEnrolment.sectionOffering.section',
            'wishlistForEnrolment.sectionOffering.day',
        ]);


        $sourceEnrolment =
            $wishlist
                ->wishlistForEnrolment;


        if (
            !$sourceEnrolment
            ||
            !$sourceEnrolment
                ->sectionOffering
        ) {

            return redirect()
                ->route(
                    'parent.wishlist.show',
                    $wishlist
                )
                ->with(
                    'error',
                    'The original class is no longer available.'
                );
        }


        $currentOffering =
            $sourceEnrolment
                ->sectionOffering;


        $availableOfferings =
            SectionOffering::with([
                'section',
                'day',
            ])
                ->where(
                    'section_id',
                    $currentOffering
                        ->section_id
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'id',
                    '!=',
                    $currentOffering->id
                )
                ->orderBy(
                    'day_id'
                )
                ->orderBy(
                    'start_time'
                )
                ->get();


        return view(
            'parent.wishlist.edit',
            compact(
                'student',
                'wishlist',
                'sourceEnrolment',
                'availableOfferings'
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
        Enrolment $wishlist
    ) {
        $this->checkWishlistAccess(
            $wishlist
        );


        if (
            $wishlist
                ->wishlist_status
            !==
            'pending'
        ) {

            return redirect()
                ->route(
                    'parent.wishlist.show',
                    $wishlist
                )
                ->with(
                    'error',
                    'Only pending wishlist requests can be updated.'
                );
        }


        $validated =
            $request->validate([
                'section_offering_id' => [
                    'required',
                    'integer',
                    'exists:section_offerings,id',
                ],
            ]);


        $sourceEnrolment =
            Enrolment::with(
                'sectionOffering'
            )
                ->find(
                    $wishlist
                        ->wishlist_for_enrolment_id
                );


        if (
            !$sourceEnrolment
            ||
            !$sourceEnrolment
                ->sectionOffering
        ) {

            return back()
                ->withErrors([
                    'section_offering_id' =>
                        'The original class is no longer available.',
                ]);
        }


        $requestedOffering =
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
                ->first();


        if (!$requestedOffering) {

            return back()
                ->withErrors([
                    'section_offering_id' =>
                        'The selected class time is unavailable.',
                ]);
        }


        if (
            $requestedOffering
                ->section_id
            !=
            $sourceEnrolment
                ->sectionOffering
                ->section_id
        ) {

            return back()
                ->withErrors([
                    'section_offering_id' =>
                        'Please select another time for the same class.',
                ]);
        }


        if (
            $requestedOffering->id
            ==
            $sourceEnrolment
                ->section_offering_id
        ) {

            return back()
                ->withErrors([
                    'section_offering_id' =>
                        'Please select a different class time.',
                ]);
        }


        $wishlist->update([
            'section_offering_id' =>
                $requestedOffering->id,
        ]);


        return redirect()
            ->route(
                'parent.wishlist.show',
                $wishlist
            )
            ->with(
                'success',
                'Wishlist request updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Enrolment $wishlist
    ) {
        $this->checkWishlistAccess(
            $wishlist
        );


        if (
            $wishlist
                ->wishlist_status
            !==
            'pending'
        ) {

            return redirect()
                ->route(
                    'parent.wishlist.show',
                    $wishlist
                )
                ->with(
                    'error',
                    'Only pending wishlist requests can be cancelled.'
                );
        }


        $wishlist->update([
            'wishlist_status' =>
                'cancelled',

            'is_active' =>
                false,
        ]);


        return redirect()
            ->route(
                'parent.wishlist.show',
                $wishlist
            )
            ->with(
                'success',
                'Wishlist request cancelled successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Enrolment $wishlist
    ) {
        $this->checkWishlistAccess(
            $wishlist
        );


        if (
            !in_array(
                $wishlist
                    ->wishlist_status,
                [
                    'rejected',
                    'cancelled',
                ]
            )
        ) {

            return redirect()
                ->route(
                    'parent.wishlist.show',
                    $wishlist
                )
                ->with(
                    'error',
                    'Only rejected or cancelled wishlist requests can be deleted.'
                );
        }


        $wishlist->delete();


        return redirect()
            ->route(
                'parent.wishlist.index'
            )
            ->with(
                'success',
                'Wishlist request deleted successfully.'
            );
    }
}