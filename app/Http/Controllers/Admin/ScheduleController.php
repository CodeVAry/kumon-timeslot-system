<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Schedule Page
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $context = $this->getDayContext(
            $request->input('day_id')
        );

        $days = $context['days'];
        $dayDates = $context['dayDates'];
        $selectedDay = $context['selectedDay'];
        $selectedDayId = $selectedDay?->id;
        $selectedDate = $selectedDay
            ? $dayDates[$selectedDay->id]
            : null;


        /*
         * No active days configured.
         */
        if (!$selectedDay) {

            return view(
                'admin.schedule.index',
                [
                    'days' => $days,
                    'dayDates' => $dayDates,
                    'selectedDay' => null,
                    'selectedDayId' => null,
                    'selectedDate' => null,
                    'scheduleRows' => collect(),
                    'timeOfferings' => collect(),
                    'selectedTime' => null,
                    'selectedOffering' => null,
                    'previewEnrolments' => collect(),
                    'totalClasses' => 0,
                    'totalStudents' => 0,
                    'totalWishlist' => 0,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Load all active offerings for selected day
        |--------------------------------------------------------------------------
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
            ->where(
                'day_id',
                $selectedDay->id
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy('start_time')
            ->orderBy('section_id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Daily Summary
        |--------------------------------------------------------------------------
        */

        $offeringIds = $offerings
            ->pluck('id');


        $totalClasses =
            $offerings->count();


        if ($offeringIds->isEmpty()) {

            $totalStudents = 0;
            $totalWishlist = 0;

        } else {

            /*
             * Unique students for the selected day.
             *
             * A student can have more than one class,
             * so we count unique student IDs here.
             */
            $totalStudents = Enrolment::whereIn(
                'section_offering_id',
                $offeringIds
            )
                ->where('is_active', true)
                ->where('is_wishlist', false)
                ->distinct()
                ->count('student_id');


            $totalWishlist = Enrolment::whereIn(
                'section_offering_id',
                $offeringIds
            )
                ->where('is_active', true)
                ->where('is_wishlist', true)
                ->count();
        }


        /*
        |--------------------------------------------------------------------------
        | Group Schedule By Start Time
        |--------------------------------------------------------------------------
        */

        $scheduleRows = collect();


        $groupedOfferings =
            $offerings->groupBy(
                function ($offering) {

                    return Carbon::parse(
                        $offering->start_time
                    )->format('H:i:s');
                }
            );


        foreach (
            $groupedOfferings
            as $rawTime => $group
        ) {

            $scheduleRows->push([

                'raw_time' =>
                    $rawTime,

                'time' =>
                    Carbon::parse(
                        $rawTime
                    )->format('g:i A'),

                'class_count' =>
                    $group->count(),

                'enrolment_count' =>
                    $group->sum(
                        'allocated_seats'
                    ),

                'wishlist_count' =>
                    $group->sum(
                        'wishlist_count'
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Selected Time
        |--------------------------------------------------------------------------
        */

        $selectedTime =
            $request->input(
                'time'
            );


        $validTimes =
            $scheduleRows
                ->pluck('raw_time')
                ->toArray();


        if (
            !$selectedTime ||
            !in_array(
                $selectedTime,
                $validTimes
            )
        ) {

            $selectedTime =
                $scheduleRows
                    ->first()['raw_time']
                    ?? null;
        }


        /*
        |--------------------------------------------------------------------------
        | Offerings At Selected Time
        |--------------------------------------------------------------------------
        */

        $timeOfferings =
            $offerings
                ->filter(
                    function ($offering) use (
                        $selectedTime
                    ) {

                        return Carbon::parse(
                            $offering->start_time
                        )->format('H:i:s')
                            ===
                            $selectedTime;
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Selected Offering
        |--------------------------------------------------------------------------
        */

        $requestedOfferingId =
            (int)
            $request->input(
                'offering_id'
            );


        $selectedOffering =
            $timeOfferings
                ->firstWhere(
                    'id',
                    $requestedOfferingId
                );


        if (!$selectedOffering) {

            $selectedOffering =
                $timeOfferings->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Student Preview
        |--------------------------------------------------------------------------
        */

        $previewEnrolments =
            collect();


        if ($selectedOffering) {

            $selectedOffering->load([
                'section',
                'day',

                'enrolments' => function ($query) {

                    $query
                        ->where(
                            'is_active',
                            true
                        )
                        ->where(
                            'is_wishlist',
                            false
                        )
                        ->orderBy(
                            'student_id'
                        );
                },

                'enrolments.student.studentStatus',

                'enrolments.student.guardians',
            ]);


            $previewEnrolments =
                $selectedOffering
                    ->enrolments
                    ->take(8);
        }


        return view(
            'admin.schedule.index',
            compact(
                'days',
                'dayDates',
                'selectedDay',
                'selectedDayId',
                'selectedDate',
                'scheduleRows',
                'timeOfferings',
                'selectedTime',
                'selectedOffering',
                'previewEnrolments',
                'totalClasses',
                'totalStudents',
                'totalWishlist'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Full Student List For One Class
    |--------------------------------------------------------------------------
    */
    public function classStudents(
        SectionOffering $sectionOffering
    ) {
        $sectionOffering->load([
            'section',
            'day',
        ]);


        $enrolments = Enrolment::with([
            'student.studentStatus',
            'student.guardians',
        ])
            ->where(
                'section_offering_id',
                $sectionOffering->id
            )
            ->where(
                'is_active',
                true
            )
            ->where(
                'is_wishlist',
                false
            )
            ->orderBy('student_id')
            ->paginate(20);


        $wishlistCount =
            Enrolment::where(
                'section_offering_id',
                $sectionOffering->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_wishlist',
                    true
                )
                ->count();


        return view(
            'admin.schedule.class-students',
            compact(
                'sectionOffering',
                'enrolments',
                'wishlistCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Print Whole Day
    |--------------------------------------------------------------------------
    */
    public function printDay(
        Request $request
    ) {
        $context =
            $this->getDayContext(
                $request->input(
                    'day_id'
                )
            );


        $selectedDay =
            $context['selectedDay'];


        $selectedDate =
            $selectedDay
                ? $context[
                    'dayDates'
                ][$selectedDay->id]
                : null;


        $offerings =
            collect();


        if ($selectedDay) {

            $offerings =
                SectionOffering::with([
                    'section',
                    'day',

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
                                )
                                ->orderBy(
                                    'student_id'
                                );
                        },

                    'enrolments.student.studentStatus',

                    'enrolments.student.guardians',
                ])
                    ->where(
                        'day_id',
                        $selectedDay->id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->orderBy(
                        'start_time'
                    )
                    ->orderBy(
                        'section_id'
                    )
                    ->get();
        }


        return view(
            'admin.schedule.print-day',
            compact(
                'selectedDay',
                'selectedDate',
                'offerings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Print One Class
    |--------------------------------------------------------------------------
    */
    public function printClass(
        SectionOffering $sectionOffering
    ) {
        $sectionOffering->load([
            'section',
            'day',

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
                        )
                        ->orderBy(
                            'student_id'
                        );
                },

            'enrolments.student.studentStatus',

            'enrolments.student.guardians',
        ]);


        return view(
            'admin.schedule.print-class',
            compact(
                'sectionOffering'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Day Selection Helper
    |--------------------------------------------------------------------------
    */
    private function getDayContext(
        $requestedDayId = null
    ) {
        $days =
            Day::where(
                'is_active',
                true
            )
                ->orderBy(
                    'sort_order'
                )
                ->get();


        $today =
            now()->startOfDay();


        $todayName =
            $today->format('l');


        $dayDates = [];


        foreach ($days as $day) {

            if (
                strtolower(
                    $day->day_name
                )
                ===
                strtolower(
                    $todayName
                )
            ) {

                $date =
                    $today->copy();

            } else {

                $date =
                    $today
                        ->copy()
                        ->next(
                            $day->day_name
                        );
            }


            $dayDates[
                $day->id
            ] = $date;
        }


        /*
         * Today if classes exist today.
         */
        $defaultDay =
            $days->first(
                function ($day) use (
                    $todayName
                ) {

                    return strtolower(
                        $day->day_name
                    )
                    ===
                    strtolower(
                        $todayName
                    );
                }
            );


        /*
         * Otherwise nearest available day.
         */
        if (
            !$defaultDay &&
            $days->isNotEmpty()
        ) {

            $defaultDay =
                $days
                    ->sortBy(
                        function ($day) use (
                            $dayDates
                        ) {

                            return $dayDates[
                                $day->id
                            ]->timestamp;
                        }
                    )
                    ->first();
        }


        $selectedDay =
            $days->firstWhere(
                'id',
                (int)
                $requestedDayId
            );


        if (!$selectedDay) {

            $selectedDay =
                $defaultDay;
        }


        return [
            'days' =>
                $days,

            'dayDates' =>
                $dayDates,

            'selectedDay' =>
                $selectedDay,
        ];
    }
}
