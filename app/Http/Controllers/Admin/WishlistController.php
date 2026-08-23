<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Wishlist List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );


        $query =
            Enrolment::with([
                'student.studentStatus',

                'sectionOffering.section',
                'sectionOffering.day',

                'wishlistForEnrolment.sectionOffering.section',
                'wishlistForEnrolment.sectionOffering.day',
            ])
                ->where(
                    'is_wishlist',
                    true
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    function ($query) {

                        /*
                         * New wishlist records use:
                         *
                         * wishlist_status = pending
                         *
                         * Old wishlist records may still
                         * have wishlist_status = NULL.
                         */
                        $query
                            ->where(
                                'wishlist_status',
                                'pending'
                            )
                            ->orWhereNull(
                                'wishlist_status'
                            );
                    }
                );


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


        $wishlists =
            $query
                ->latest()
                ->paginate(20)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Active Wishlist Count
        |--------------------------------------------------------------------------
        */

        $totalWishlist =
            Enrolment::where(
                'is_wishlist',
                true
            )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    function ($query) {

                        $query
                            ->where(
                                'wishlist_status',
                                'pending'
                            )
                            ->orWhereNull(
                                'wishlist_status'
                            );
                    }
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | Wishlist With Available Seat
        |--------------------------------------------------------------------------
        */

        $availableWishlist =
            Enrolment::where(
                'is_wishlist',
                true
            )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    function ($query) {

                        $query
                            ->where(
                                'wishlist_status',
                                'pending'
                            )
                            ->orWhereNull(
                                'wishlist_status'
                            );
                    }
                )
                ->with(
                    'sectionOffering'
                )
                ->get()
                ->filter(
                    function ($wishlist) {

                        $offering =
                            $wishlist
                                ->sectionOffering;


                        if (!$offering) {

                            return false;
                        }


                        $confirmedCount =
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


                        return
                            $confirmedCount
                            <
                            $offering
                                ->max_seats;
                    }
                )
                ->count();


        return view(
            'admin.wishlist.index',
            compact(
                'wishlists',
                'search',
                'totalWishlist',
                'availableWishlist'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create / Edit Wishlist
    |--------------------------------------------------------------------------
    */

    public function create(
        Enrolment $enrolment
    ) {
        /*
         * Wishlist move request must start
         * from an active confirmed class.
         */
        if (
            !$enrolment->is_active
            ||
            $enrolment->is_wishlist
        ) {

            return back()
                ->with(
                    'error',
                    'Wishlist can only be created from an active confirmed enrolment.'
                );
        }


        $enrolment->load([
            'student',
            'sectionOffering.section',
            'sectionOffering.day',
        ]);


        $currentOffering =
            $enrolment
                ->sectionOffering;


        if (!$currentOffering) {

            return back()
                ->with(
                    'error',
                    'Current class offering could not be found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Other Offerings Of Same Class
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Existing Wishlist For Current Enrolment
        |--------------------------------------------------------------------------
        */

        $existingWishlist =
            Enrolment::with([
                'sectionOffering.section',
                'sectionOffering.day',
            ])
                ->where(
                    'wishlist_for_enrolment_id',
                    $enrolment->id
                )
                ->where(
                    'is_wishlist',
                    true
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    function ($query) {

                        $query
                            ->where(
                                'wishlist_status',
                                'pending'
                            )
                            ->orWhereNull(
                                'wishlist_status'
                            );
                    }
                )
                ->first();


        return view(
            'admin.wishlist.create',
            compact(
                'enrolment',
                'availableOfferings',
                'existingWishlist'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store / Update Wishlist
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Enrolment $enrolment
    ) {
        if (
            !$enrolment->is_active
            ||
            $enrolment->is_wishlist
        ) {

            return back()
                ->with(
                    'error',
                    'Invalid enrolment.'
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


        $enrolment->load([
            'student',
            'sectionOffering',
        ]);


        $currentOffering =
            $enrolment
                ->sectionOffering;


        if (!$currentOffering) {

            return back()
                ->with(
                    'error',
                    'Current class offering could not be found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Requested Offering
        |--------------------------------------------------------------------------
        */

        $targetOffering =
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
                ->firstOrFail();


        /*
         * Must stay in same class/section.
         */
        if (
            (int)
            $targetOffering
                ->section_id
            !==
            (int)
            $currentOffering
                ->section_id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_offering_id' =>
                        'Wishlist must be for the same class.',
                ]);
        }


        /*
         * Cannot select existing class.
         */
        if (
            (int)
            $targetOffering->id
            ===
            (int)
            $enrolment
                ->section_offering_id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_offering_id' =>
                        'Please select a different class time.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Existing Pending Wishlist
        |--------------------------------------------------------------------------
        */

        $existingWishlist =
            Enrolment::where(
                'wishlist_for_enrolment_id',
                $enrolment->id
            )
                ->where(
                    'is_wishlist',
                    true
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    function ($query) {

                        $query
                            ->where(
                                'wishlist_status',
                                'pending'
                            )
                            ->orWhereNull(
                                'wishlist_status'
                            );
                    }
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Update Existing Request
        |--------------------------------------------------------------------------
        */

        if ($existingWishlist) {

            $existingWishlist->update([
                'section_offering_id' =>
                    $targetOffering->id,

                'wishlist_status' =>
                    'pending',

                'reviewed_by_user_id' =>
                    null,

                'reviewed_at' =>
                    null,

                'wishlist_review_note' =>
                    null,
            ]);


            return redirect()
                ->route(
                    'admin.students.show',
                    $enrolment->student
                )
                ->with(
                    'success',
                    'Wishlist updated successfully.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Create New Move Wishlist
        |--------------------------------------------------------------------------
        */

        Enrolment::create([
            'student_id' =>
                $enrolment->student_id,

            'section_offering_id' =>
                $targetOffering->id,

            'wishlist_for_enrolment_id' =>
                $enrolment->id,

            'enrolment_date' =>
                now()->toDateString(),

            'is_wishlist' =>
                true,

            'wishlist_status' =>
                'pending',

            'is_active' =>
                true,
        ]);


        return redirect()
            ->route(
                'admin.students.show',
                $enrolment->student
            )
            ->with(
                'success',
                'Wishlist added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve Wishlist
    |--------------------------------------------------------------------------
    |
    | Supports:
    |
    | 1. Move wishlist
    |    Current class -> requested class
    |
    | 2. Direct wishlist
    |    No current class -> requested class
    |
    */

    public function approve(
        Request $request,
        Enrolment $wishlist
    ) {
        /*
        |--------------------------------------------------------------------------
        | Pending Check
        |--------------------------------------------------------------------------
        |
        | NULL is accepted for old wishlist records.
        |
        */

        if (
            !in_array(
                $wishlist
                    ->wishlist_status,
                [
                    null,
                    'pending',
                ],
                true
            )
        ) {

            return back()
                ->with(
                    'error',
                    'Only pending wishlist requests can be approved.'
                );
        }


        if (
            !$wishlist
                ->is_wishlist
        ) {

            return back()
                ->with(
                    'error',
                    'This wishlist request has already been processed.'
                );
        }


        if (
            !$wishlist
                ->is_active
        ) {

            return back()
                ->with(
                    'error',
                    'This wishlist request is no longer active.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Review Note
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'wishlist_review_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Load Relationships
        |--------------------------------------------------------------------------
        */

        $wishlist->load([
            'student',
            'sectionOffering',
            'wishlistForEnrolment',
        ]);


        $requestedOffering =
            $wishlist
                ->sectionOffering;


        if (!$requestedOffering) {

            return back()
                ->with(
                    'error',
                    'The requested class is no longer available.'
                );
        }


        if (
            !$requestedOffering
                ->is_active
        ) {

            return back()
                ->with(
                    'error',
                    'The requested class is currently inactive.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Capacity Check
        |--------------------------------------------------------------------------
        */

        $occupiedSeats =
            Enrolment::where(
                'section_offering_id',
                $requestedOffering->id
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
            $occupiedSeats
            >=
            $requestedOffering
                ->max_seats
        ) {

            return back()
                ->with(
                    'error',
                    'The requested class is currently full.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Approve
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $wishlist,
                $validated
            ) {

                /*
                 * MOVE REQUEST
                 *
                 * If the wishlist is connected
                 * to an existing confirmed class,
                 * deactivate the old class.
                 */
                $sourceEnrolment =
                    $wishlist
                        ->wishlistForEnrolment;


                if (
                    $sourceEnrolment
                    &&
                    $sourceEnrolment
                        ->is_active
                    &&
                    !$sourceEnrolment
                        ->is_wishlist
                ) {

                    $sourceEnrolment->update([
                        'is_active' =>
                            false,
                    ]);
                }


                /*
                 * Convert wishlist row into
                 * confirmed enrolment.
                 */
                $wishlist->update([
                    'is_wishlist' =>
                        false,

                    'wishlist_status' =>
                        'approved',

                    'is_active' =>
                        true,

                    'reviewed_by_user_id' =>
                        auth()->id(),

                    'reviewed_at' =>
                        now(),

                    'wishlist_review_note' =>
                        $validated[
                            'wishlist_review_note'
                        ] ?? null,
                ]);
            }
        );


        return redirect()
            ->route(
                'admin.wishlist.index'
            )
            ->with(
                'success',
                'Wishlist request approved successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject Wishlist
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        Enrolment $wishlist
    ) {
        /*
         * Support old NULL-status wishlist records.
         */
        if (
            !in_array(
                $wishlist
                    ->wishlist_status,
                [
                    null,
                    'pending',
                ],
                true
            )
        ) {

            return redirect()
                ->route(
                    'admin.wishlist.index'
                )
                ->with(
                    'error',
                    'Only pending wishlist requests can be rejected.'
                );
        }


        if (
            !$wishlist
                ->is_wishlist
        ) {

            return redirect()
                ->route(
                    'admin.wishlist.index'
                )
                ->with(
                    'error',
                    'This wishlist request has already been processed.'
                );
        }


        $validated =
            $request->validate(
                [
                    'wishlist_review_note' => [
                        'required',
                        'string',
                        'max:2000',
                    ],
                ],
                [
                    'wishlist_review_note.required' =>
                        'Please enter a reason before rejecting the wishlist request.',
                ]
            );


        $wishlist->update([
            'wishlist_status' =>
                'rejected',

            'is_active' =>
                false,

            'reviewed_by_user_id' =>
                auth()->id(),

            'reviewed_at' =>
                now(),

            'wishlist_review_note' =>
                $validated[
                    'wishlist_review_note'
                ],
        ]);


        return redirect()
            ->route(
                'admin.wishlist.index'
            )
            ->with(
                'success',
                'Wishlist request rejected successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Wishlist
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Enrolment $wishlist
    ) {
        if (
            !$wishlist->is_wishlist
            ||
            !$wishlist->is_active
        ) {

            return back()
                ->with(
                    'error',
                    'Wishlist request is already inactive.'
                );
        }


        if (
            !in_array(
                $wishlist
                    ->wishlist_status,
                [
                    null,
                    'pending',
                ],
                true
            )
        ) {

            return back()
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

            'reviewed_by_user_id' =>
                auth()->id(),

            'reviewed_at' =>
                now(),
        ]);


        return redirect()
            ->route(
                'admin.wishlist.index'
            )
            ->with(
                'success',
                'Wishlist request cancelled successfully.'
            );
    }
}
