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
        | Counts
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
                ->count();


        $availableWishlist =
            Enrolment::where(
                'is_wishlist',
                true
            )
                ->where(
                    'is_active',
                    true
                )
                ->with('sectionOffering')
                ->get()
                ->filter(
                    function ($wishlist) {

                        $offering =
                            $wishlist->sectionOffering;


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
                            $offering->max_seats;
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
        |--------------------------------------------------------------------------
        | Validate Current Confirmed Enrolment
        |--------------------------------------------------------------------------
        */

        if (
            !$enrolment->is_active
            ||
            $enrolment->is_wishlist
        ) {

            return back()->with(
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
            $enrolment->sectionOffering;


        if (!$currentOffering) {

            return back()->with(
                'error',
                'Current class offering could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Other Offerings Of Same Section
        |--------------------------------------------------------------------------
        */

        $availableOfferings =
            SectionOffering::with([
                'section',
                'day',
            ])
                ->where(
                    'section_id',
                    $currentOffering->section_id
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
        | Find Existing Wishlist
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
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Support Old Wishlist Records
        |--------------------------------------------------------------------------
        |
        | Older wishlist rows may have wishlist_for_enrolment_id = NULL.
        | Find them using the same student + same section.
        |--------------------------------------------------------------------------
        */

        if (!$existingWishlist) {

            $existingWishlist =
                Enrolment::with([
                    'sectionOffering.section',
                    'sectionOffering.day',
                ])
                    ->where(
                        'student_id',
                        $enrolment->student_id
                    )
                    ->where(
                        'is_wishlist',
                        true
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->whereNull(
                        'wishlist_for_enrolment_id'
                    )
                    ->whereHas(
                        'sectionOffering',
                        function ($query) use ($currentOffering) {

                            $query->where(
                                'section_id',
                                $currentOffering->section_id
                            );
                        }
                    )
                    ->first();


            /*
            |--------------------------------------------------------------------------
            | Repair Old Wishlist Record
            |--------------------------------------------------------------------------
            */

            if ($existingWishlist) {

                $existingWishlist->update([

                    'wishlist_for_enrolment_id' =>
                        $enrolment->id,

                ]);
            }
        }


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
        /*
        |--------------------------------------------------------------------------
        | Validate Current Enrolment
        |--------------------------------------------------------------------------
        */

        if (
            !$enrolment->is_active
            ||
            $enrolment->is_wishlist
        ) {

            return back()->with(
                'error',
                'Invalid enrolment.'
            );
        }


        $validated =
            $request->validate([

                'section_offering_id' => [
                    'required',
                    'exists:section_offerings,id',
                ],

            ]);


        $enrolment->load([
            'student',
            'sectionOffering',
        ]);


        $currentOffering =
            $enrolment->sectionOffering;


        if (!$currentOffering) {

            return back()->with(
                'error',
                'Current class offering could not be found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Target Offering
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
        |--------------------------------------------------------------------------
        | Must Stay In Same Section
        |--------------------------------------------------------------------------
        */

        if (
            $targetOffering->section_id
            !==
            $currentOffering->section_id
        ) {

            return back()
                ->withInput()
                ->withErrors([

                    'section_offering_id' =>
                        'Wishlist must be for the same class.',

                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Cannot Select Current Offering
        |--------------------------------------------------------------------------
        */

        if (
            $targetOffering->id
            ===
            $enrolment->section_offering_id
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
        | Find Existing Wishlist
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
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Update Existing Wishlist
        |--------------------------------------------------------------------------
        */

        if ($existingWishlist) {

            $existingWishlist->update([

                'section_offering_id' =>
                    $targetOffering->id,

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
        | Create New Wishlist
        |--------------------------------------------------------------------------
        |
        | Wishlist does not consume a seat.
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
    | Approve Wishlist / Move Student
    |--------------------------------------------------------------------------
    */
    public function approve(
        Enrolment $wishlist
    ) {
        /*
        |--------------------------------------------------------------------------
        | Validate Wishlist
        |--------------------------------------------------------------------------
        */

        if (
            !$wishlist->is_wishlist
            ||
            !$wishlist->is_active
        ) {

            return back()->with(
                'error',
                'This wishlist request is no longer active.'
            );
        }


        try {

            DB::transaction(
                function () use ($wishlist) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Wishlist Record
                    |--------------------------------------------------------------------------
                    */

                    $lockedWishlist =
                        Enrolment::where(
                            'id',
                            $wishlist->id
                        )
                            ->lockForUpdate()
                            ->firstOrFail();


                    if (
                        !$lockedWishlist->is_wishlist
                        ||
                        !$lockedWishlist->is_active
                    ) {

                        throw new \Exception(
                            'Wishlist is no longer active.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Source Confirmed Enrolment
                    |--------------------------------------------------------------------------
                    */

                    $sourceEnrolment =
                        Enrolment::where(
                            'id',
                            $lockedWishlist
                                ->wishlist_for_enrolment_id
                        )
                            ->lockForUpdate()
                            ->first();


                    if (
                        !$sourceEnrolment
                        ||
                        !$sourceEnrolment->is_active
                        ||
                        $sourceEnrolment->is_wishlist
                    ) {

                        throw new \Exception(
                            'The original enrolment is no longer active.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Lock Target Offering
                    |--------------------------------------------------------------------------
                    */

                    $targetOffering =
                        SectionOffering::where(
                            'id',
                            $lockedWishlist
                                ->section_offering_id
                        )
                            ->lockForUpdate()
                            ->firstOrFail();


                    /*
                    |--------------------------------------------------------------------------
                    | Re-check Capacity
                    |--------------------------------------------------------------------------
                    */

                    $confirmedCount =
                        Enrolment::where(
                            'section_offering_id',
                            $targetOffering->id
                        )
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'is_wishlist',
                                false
                            )
                            ->lockForUpdate()
                            ->count();


                    if (
                        $confirmedCount
                        >=
                        $targetOffering->max_seats
                    ) {

                        throw new \Exception(
                            'The requested class is currently full.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Extra Safety:
                    | Student Cannot Already Be Confirmed In Target Offering
                    |--------------------------------------------------------------------------
                    */

                    $alreadyConfirmed =
                        Enrolment::where(
                            'student_id',
                            $lockedWishlist->student_id
                        )
                            ->where(
                                'section_offering_id',
                                $targetOffering->id
                            )
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'is_wishlist',
                                false
                            )
                            ->exists();


                    if ($alreadyConfirmed) {

                        throw new \Exception(
                            'Student is already enrolled in the requested class.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Deactivate Old Enrolment
                    |--------------------------------------------------------------------------
                    */

                    $sourceEnrolment->update([

                        'is_active' =>
                            false,

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Convert Wishlist Into Confirmed Enrolment
                    |--------------------------------------------------------------------------
                    */

                    $lockedWishlist->update([

                        'wishlist_for_enrolment_id' =>
                            null,

                        'enrolment_date' =>
                            now()->toDateString(),

                        'is_wishlist' =>
                            false,

                        'is_active' =>
                            true,

                    ]);
                }
            );


            return redirect()
                ->route(
                    'admin.wishlist.index'
                )
                ->with(
                    'success',
                    'Wishlist approved. Student moved successfully.'
                );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
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

            return back()->with(
                'error',
                'Wishlist request is already inactive.'
            );
        }


        $wishlist->update([

            'is_active' =>
                false,

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
