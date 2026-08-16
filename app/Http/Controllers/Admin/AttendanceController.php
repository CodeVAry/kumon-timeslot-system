<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Attendance Overview
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $days = Day::where(
            'is_active',
            true
        )
            ->orderBy('sort_order')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Build Actual Dates
        |--------------------------------------------------------------------------
        */

        $today = now()->startOfDay();

        $todayName = $today->format('l');

        $dayDates = [];


        foreach ($days as $day) {

            if (
                strtolower($day->day_name)
                ===
                strtolower($todayName)
            ) {

                $date = $today->copy();

            } else {

                $date = $today
                    ->copy()
                    ->next(
                        $day->day_name
                    );
            }


            $dayDates[$day->id] =
                $date;
        }


        /*
        |--------------------------------------------------------------------------
        | Default Selected Day
        |--------------------------------------------------------------------------
        */

        $todayDay = $days->first(
            function ($day) use ($todayName) {

                return strtolower(
                    $day->day_name
                )
                ===
                strtolower(
                    $todayName
                );
            }
        );


        if (!$todayDay) {

            $todayDay = $days
                ->sortBy(
                    function ($day) use ($dayDates) {

                        return $dayDates[
                            $day->id
                        ]->timestamp;
                    }
                )
                ->first();
        }


        $selectedDayId =
            (int) $request->input(
                'day_id',
                $todayDay?->id
            );


        $selectedDay =
            $days->firstWhere(
                'id',
                $selectedDayId
            );


        if (!$selectedDay) {

            $selectedDay =
                $todayDay;

            $selectedDayId =
                $selectedDay?->id;
        }


        $selectedDate =
            $selectedDay
                ? $dayDates[
                    $selectedDay->id
                ]
                : null;


        /*
        |--------------------------------------------------------------------------
        | Load Offerings
        |--------------------------------------------------------------------------
        */

        $offerings = collect();


        if ($selectedDay) {

            $offerings =
                SectionOffering::with([
                    'section',
                    'day',
                ])
                    ->withCount([
                        'enrolments as enrolled_count' =>
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
                    ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Attendance Counts
        |--------------------------------------------------------------------------
        */

        $totalClasses =
            $offerings->count();


        $offeringIds =
            $offerings->pluck('id');


        $studentsEnrolled = 0;

        $presentCount = 0;

        $absentCount = 0;

        $vacationCount = 0;


        if (
            $selectedDate &&
            $offeringIds->isNotEmpty()
        ) {

            $studentsEnrolled =
                Enrolment::whereIn(
                    'section_offering_id',
                    $offeringIds
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


            $attendanceQuery =
                Attendance::whereDate(
                    'attendance_date',
                    $selectedDate
                        ->toDateString()
                )
                    ->whereHas(
                        'enrolment',
                        function ($query) use (
                            $offeringIds
                        ) {

                            $query->whereIn(
                                'section_offering_id',
                                $offeringIds
                            );
                        }
                    );


            $presentCount =
                (clone $attendanceQuery)
                    ->where(
                        'status',
                        'present'
                    )
                    ->count();


            $absentCount =
                (clone $attendanceQuery)
                    ->where(
                        'status',
                        'absent'
                    )
                    ->count();


            $vacationCount =
                (clone $attendanceQuery)
                    ->where(
                        'status',
                        'vacation'
                    )
                    ->count();
        }


        /*
        |--------------------------------------------------------------------------
        | Group Classes By Time
        |--------------------------------------------------------------------------
        */

        $classTimes =
            $offerings
                ->groupBy(
                    function ($offering) {

                        return Carbon::parse(
                            $offering->start_time
                        )->format('H:i:s');
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | Selected Time
        |--------------------------------------------------------------------------
        */

        $selectedTime =
            $request->input(
                'time'
            );


        if (
            !$selectedTime ||
            !$classTimes->has(
                $selectedTime
            )
        ) {

            $selectedTime =
                $classTimes
                    ->keys()
                    ->first();
        }


        $selectedTimeOfferings =
            $selectedTime
                ? $classTimes->get(
                    $selectedTime,
                    collect()
                )
                : collect();


        return view(
            'admin.attendance.index',
            compact(
                'days',
                'dayDates',
                'selectedDay',
                'selectedDayId',
                'selectedDate',
                'offerings',
                'classTimes',
                'selectedTime',
                'selectedTimeOfferings',
                'totalClasses',
                'studentsEnrolled',
                'presentCount',
                'absentCount',
                'vacationCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Take Attendance Page
    |--------------------------------------------------------------------------
    */
    public function takeAttendance(
        Request $request,
        SectionOffering $sectionOffering
    ) {
        $validated =
            $request->validate([
                'date' => [
                    'required',
                    'date',
                ],
            ]);


        $attendanceDate =
            Carbon::parse(
                $validated['date']
            );


        $sectionOffering->load([
            'section',
            'day',
        ]);


        $enrolments =
            Enrolment::with([
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
                ->orderBy(
                    'student_id'
                )
                ->get();


        /*
         * Existing attendance records.
         */
        $attendanceRecords =
            Attendance::whereIn(
                'enrolment_id',
                $enrolments->pluck('id')
            )
                ->whereDate(
                    'attendance_date',
                    $attendanceDate
                        ->toDateString()
                )
                ->get()
                ->keyBy(
                    'enrolment_id'
                );


        return view(
            'admin.attendance.takeAttendance',
            compact(
                'sectionOffering',
                'attendanceDate',
                'enrolments',
                'attendanceRecords'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Save Attendance
    |--------------------------------------------------------------------------
    */
    public function store(
        Request $request,
        SectionOffering $sectionOffering
    ) {
        $validated =
            $request->validate([

                'attendance_date' => [
                    'required',
                    'date',
                ],

                'attendance' => [
                    'required',
                    'array',
                ],

                'attendance.*' => [
                    'required',
                    Rule::in([
                        'present',
                        'absent',
                        'vacation',
                    ]),
                ],
            ]);


        /*
         * Get valid enrolments for
         * this exact class.
         */
        $validEnrolmentIds =
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
                    false
                )
                ->pluck('id')
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->toArray();


        DB::transaction(
            function () use (
                $validated,
                $validEnrolmentIds
            ) {

                foreach (
                    $validated['attendance']
                    as $enrolmentId => $status
                ) {

                    $enrolmentId =
                        (int) $enrolmentId;


                    /*
                     * Prevent someone from
                     * submitting an enrolment
                     * from another class.
                     */
                    if (
                        !in_array(
                            $enrolmentId,
                            $validEnrolmentIds
                        )
                    ) {
                        continue;
                    }


                    Attendance::updateOrCreate(
                        [
                            'enrolment_id' =>
                                $enrolmentId,

                            'attendance_date' =>
                                $validated[
                                    'attendance_date'
                                ],
                        ],
                        [
                            'status' =>
                                $status,
                        ]
                    );
                }
            }
        );


        return redirect()
            ->route(
                'admin.attendance.takeAttendance',
                [
                    'sectionOffering' =>
                        $sectionOffering->id,

                    'date' =>
                        $validated[
                            'attendance_date'
                        ],
                ]
            )
            ->with(
                'success',
                'Attendance saved successfully.'
            );
    }
}
