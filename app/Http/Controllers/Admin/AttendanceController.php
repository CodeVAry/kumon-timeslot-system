<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Attendance;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\StudentLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Attendance Overview
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $days =
            Day::where(
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

        $today =
            now()->startOfDay();


        $todayName =
            $today->format('l');


        $dayDates = [];


        foreach ($days as $day) {

            if (
                strtolower($day->day_name)
                ===
                strtolower($todayName)
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
            ] =
                $date;
        }


        /*
        |--------------------------------------------------------------------------
        | Default Selected Day
        |--------------------------------------------------------------------------
        */

        $todayDay =
            $days->first(
                function ($day) use ($todayName) {

                    return
                        strtolower(
                            $day->day_name
                        )
                        ===
                        strtolower(
                            $todayName
                        );
                }
            );


        if (!$todayDay) {

            $todayDay =
                $days
                    ->sortBy(
                        function (
                            $day
                        ) use (
                            $dayDates
                        ) {

                            return
                                $dayDates[
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

        $offerings =
            collect();


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
            $selectedDate
            &&
            $offeringIds
                ->isNotEmpty()
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
                        function (
                            $query
                        ) use (
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
                            $offering
                                ->start_time
                        )->format(
                            'H:i:s'
                        );
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
            !$selectedTime
            ||
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
    | Take Attendance
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
                $validated[
                    'date'
                ]
            )
                ->startOfDay();


        $sectionOffering->load([
            'section',
            'day',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Confirmed Active Enrolments
        |--------------------------------------------------------------------------
        */

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
        |--------------------------------------------------------------------------
        | Students Automatically On Vacation
        |--------------------------------------------------------------------------
        |
        | An approved/current leave automatically makes attendance Vacation.
        |
        | If actual_return_date is null:
        |     leave remains active until expected_return_date.
        |
        | If actual_return_date exists:
        |     the actual return date itself is treated as a returned day,
        |     therefore Vacation only applies BEFORE actual_return_date.
        |--------------------------------------------------------------------------
        */

        $vacationStudentIds =
            $this->getVacationStudentIds(
                $enrolments
                    ->pluck(
                        'student_id'
                    )
                    ->unique()
                    ->values(),
                $attendanceDate
            );


        /*
        |--------------------------------------------------------------------------
        | Convert Student IDs To Enrolment IDs
        |--------------------------------------------------------------------------
        */

        $vacationEnrolmentIds =
            $enrolments
                ->filter(
                    function (
                        $enrolment
                    ) use (
                        $vacationStudentIds
                    ) {

                        return
                            $vacationStudentIds
                                ->contains(
                                    $enrolment
                                        ->student_id
                                );
                    }
                )
                ->pluck(
                    'id'
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Existing Attendance
        |--------------------------------------------------------------------------
        */

        $attendanceRecords =
            Attendance::whereIn(
                'enrolment_id',
                $enrolments
                    ->pluck('id')
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
                'attendanceRecords',
                'vacationEnrolmentIds'
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


        $attendanceDate =
            Carbon::parse(
                $validated[
                    'attendance_date'
                ]
            )
                ->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | Get Valid Enrolments For This Class
        |--------------------------------------------------------------------------
        */

        $validEnrolments =
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
                ->get([
                    'id',
                    'student_id',
                ]);


        /*
        |--------------------------------------------------------------------------
        | Automatically Determine Vacation Students
        |--------------------------------------------------------------------------
        */

        $vacationStudentIds =
            $this->getVacationStudentIds(
                $validEnrolments
                    ->pluck(
                        'student_id'
                    )
                    ->unique()
                    ->values(),
                $attendanceDate
            );


        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $validated,
                $validEnrolments,
                $vacationStudentIds,
                $attendanceDate
            ) {

                foreach (
                    $validEnrolments
                    as $enrolment
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Force Vacation From Leave
                    |--------------------------------------------------------------------------
                    |
                    | Even if someone manually changes the HTML request,
                    | an approved/current leave cannot be changed to Present
                    | or Absent.
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $vacationStudentIds
                            ->contains(
                                $enrolment
                                    ->student_id
                            )
                    ) {

                        $status =
                            'vacation';

                    } else {

                        $status =
                            $validated[
                                'attendance'
                            ][
                                $enrolment->id
                            ]
                            ?? null;


                        /*
                         * Backend protection.
                         * Every non-vacation student
                         * must still have attendance.
                         */
                        if (!$status) {

                            throw ValidationException::withMessages([
                                'attendance' =>
                                    'Please mark attendance for every student.',
                            ]);
                        }
                    }


                    Attendance::updateOrCreate(
                        [
                            'enrolment_id' =>
                                $enrolment->id,

                            'attendance_date' =>
                                $attendanceDate
                                    ->toDateString(),
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
                        $attendanceDate
                            ->toDateString(),
                ]
            )
            ->with(
                'success',
                'Attendance saved successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Students On Vacation
    |--------------------------------------------------------------------------
    |
    | Only approved leave is used.
    |
    | NULL status is also accepted for older/admin-created leave records
    | created before leave approval status was introduced.
    |--------------------------------------------------------------------------
    */

    private function getVacationStudentIds(
        $studentIds,
        Carbon $attendanceDate
    ) {
        if (
            $studentIds
                ->isEmpty()
        ) {

            return collect();
        }


        $date =
            $attendanceDate
                ->toDateString();


        return StudentLeave::whereIn(
            'student_id',
            $studentIds
        )

            /*
             * Do not use Pending, Rejected
             * or Cancelled parent requests.
             */
            ->where(
                function ($query) {

                    $query
                        ->where(
                            'status',
                            'approved'
                        )

                        /*
                         * Supports older/admin-created
                         * leave rows if status was NULL.
                         */
                        ->orWhereNull(
                            'status'
                        );
                }
            )

            /*
             * Leave must already have started.
             */
            ->whereDate(
                'start_date',
                '<=',
                $date
            )

            /*
             * Determine the effective end.
             */
            ->where(
                function ($query) use (
                    $date
                ) {

                    /*
                     * No actual return yet:
                     * use expected return date.
                     */
                    $query->where(
                        function ($query) use (
                            $date
                        ) {

                            $query
                                ->whereNull(
                                    'actual_return_date'
                                )
                                ->whereDate(
                                    'expected_return_date',
                                    '>=',
                                    $date
                                );
                        }
                    )

                    /*
                     * Returned already:
                     * vacation applies only before
                     * the return date.
                     */
                    ->orWhere(
                        function ($query) use (
                            $date
                        ) {

                            $query
                                ->whereNotNull(
                                    'actual_return_date'
                                )
                                ->whereDate(
                                    'actual_return_date',
                                    '>',
                                    $date
                                );
                        }
                    );
                }
            )
            ->pluck(
                'student_id'
            )
            ->unique()
            ->values();
    }
}
