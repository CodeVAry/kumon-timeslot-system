<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Day;
use App\Models\Admin\Enrolment;
use App\Models\Admin\SectionOffering;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Current Date / Time
        |--------------------------------------------------------------------------
        |
        | Make sure config/app.php uses:
        |
        | 'timezone' => 'Australia/Hobart'
        |
        */

        $dashboardDate = now();

        $todayName =
            $dashboardDate->format('l');

        $currentTime =
            $dashboardDate->format('H:i:s');


        /*
        |--------------------------------------------------------------------------
        | Find Today's Day Record
        |--------------------------------------------------------------------------
        */

        $todayDay = Day::where(
            'day_name',
            $todayName
        )
            ->where(
                'is_active',
                true
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Defaults
        |--------------------------------------------------------------------------
        */

        $studentsToday = 0;

        $currentlyAttending = 0;

        $absentToday = 0;

        $wishlistCount = 0;

        $currentClassTime = null;

        $currentClassStudents = 0;

        $nextClassTime = null;

        $nextClassStudents = 0;

        $todayClasses = collect();

        $absentStudents = collect();

        $adminReminders = collect();


        /*
        |--------------------------------------------------------------------------
        | Overall Wishlist Count
        |--------------------------------------------------------------------------
        |
        | Wishlist is not restricted to today.
        |
        */

        $wishlistCount = Enrolment::where(
            'is_active',
            true
        )
            ->where(
                'is_wishlist',
                true
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | No Classes Today
        |--------------------------------------------------------------------------
        */

        if (!$todayDay) {

            return view(
                'dashboard',
                compact(
                    'dashboardDate',
                    'studentsToday',
                    'currentlyAttending',
                    'absentToday',
                    'wishlistCount',
                    'currentClassTime',
                    'currentClassStudents',
                    'nextClassTime',
                    'nextClassStudents',
                    'todayClasses',
                    'absentStudents',
                    'adminReminders'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Today's Active Class Offerings
        |--------------------------------------------------------------------------
        */

        $offerings = SectionOffering::with([
            'section',

            'enrolments' => function ($query) {

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
                $todayDay->id
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy(
                'start_time'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | No Offerings Today
        |--------------------------------------------------------------------------
        */

        if ($offerings->isEmpty()) {

            return view(
                'dashboard',
                compact(
                    'dashboardDate',
                    'studentsToday',
                    'currentlyAttending',
                    'absentToday',
                    'wishlistCount',
                    'currentClassTime',
                    'currentClassStudents',
                    'nextClassTime',
                    'nextClassStudents',
                    'todayClasses',
                    'absentStudents',
                    'adminReminders'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Students Today
        |--------------------------------------------------------------------------
        |
        | One student may have multiple enrolments.
        | Therefore count unique student IDs.
        |
        */

        $studentsToday = $offerings
            ->flatMap(
                function ($offering) {

                    return $offering
                        ->enrolments
                        ->pluck(
                            'student_id'
                        );
                }
            )
            ->unique()
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Group Offerings By Start Time
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 3:45 PM
        |   English
        |   3A Math
        |   B-D Math
        |   E+ Math
        |
        */

        $groupedOfferings =
            $offerings->groupBy(
                function ($offering) {

                    return Carbon::parse(
                        $offering->start_time
                    )->format('H:i:s');
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Build Today's Classes Table
        |--------------------------------------------------------------------------
        */

        foreach (
            $groupedOfferings
            as $startTime => $classOfferings
        ) {

            /*
             * Start DateTime
             */
            $startDateTime =
                Carbon::parse(
                    $dashboardDate
                        ->format('Y-m-d')
                    .
                    ' '
                    .
                    $startTime
                );


            /*
             * Find the latest ending time
             * among classes starting together.
             */
            $latestEndTime =
                $classOfferings
                    ->max(
                        'end_time'
                    );


            $endDateTime =
                Carbon::parse(
                    $dashboardDate
                        ->format('Y-m-d')
                    .
                    ' '
                    .
                    $latestEndTime
                );


            /*
            |--------------------------------------------------------------------------
            | Student IDs
            |--------------------------------------------------------------------------
            */

            $allStudentIds =
                collect();


            $regularStudentIds =
                collect();


            $interactiveStudentIds =
                collect();


            foreach (
                $classOfferings
                as $offering
            ) {

                $studentIds =
                    $offering
                        ->enrolments
                        ->pluck(
                            'student_id'
                        );


                $allStudentIds =
                    $allStudentIds
                        ->merge(
                            $studentIds
                        );


                /*
                 * TEMPORARY classification.
                 *
                 * Later we will replace this
                 * with section_groups.
                 *
                 * For now only the Interactive
                 * section is separated.
                 */
                $sectionName =
                    strtolower(
                        trim(
                            $offering
                                ->section
                                ?->section_name
                            ?? ''
                        )
                    );


                if (
                    $sectionName
                    ===
                    'interactive'
                ) {

                    $interactiveStudentIds =
                        $interactiveStudentIds
                            ->merge(
                                $studentIds
                            );

                } else {

                    $regularStudentIds =
                        $regularStudentIds
                            ->merge(
                                $studentIds
                            );
                }
            }


            $regularStudents =
                $regularStudentIds
                    ->unique()
                    ->count();


            $interactiveStudents =
                $interactiveStudentIds
                    ->unique()
                    ->count();


            $totalStudents =
                $allStudentIds
                    ->unique()
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            if (
                $dashboardDate->greaterThanOrEqualTo(
                    $startDateTime
                )
                &&
                $dashboardDate->lessThan(
                    $endDateTime
                )
            ) {

                $status =
                    'In Progress';

            } elseif (
                $dashboardDate->greaterThanOrEqualTo(
                    $endDateTime
                )
            ) {

                $status =
                    'Completed';

            } elseif (
                $dashboardDate->lessThan(
                    $startDateTime
                )
                &&
                $dashboardDate
                    ->diffInMinutes(
                        $startDateTime
                    )
                    <= 30
            ) {

                $status =
                    'Starting Soon';

            } else {

                $status =
                    'Upcoming';
            }


            /*
            |--------------------------------------------------------------------------
            | Add Table Row
            |--------------------------------------------------------------------------
            */

            $todayClasses->push([

                'raw_time' =>
                    $startTime,

                'class_time' =>
                    $startDateTime
                        ->format(
                            'g:i A'
                        ),

                'regular_students' =>
                    $regularStudents,

                'interactive_students' =>
                    $interactiveStudents,

                'total_students' =>
                    $totalStudents,

                'status' =>
                    $status,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Current Class
        |--------------------------------------------------------------------------
        */

        $currentClass =
            $todayClasses
                ->first(
                    function ($class) {

                        return
                            $class['status']
                            ===
                            'In Progress';
                    }
                );


        if ($currentClass) {

            $currentClassTime =
                $currentClass[
                    'class_time'
                ];


            $currentClassStudents =
                $currentClass[
                    'total_students'
                ];


            /*
             * Until attendance is implemented,
             * this means students scheduled
             * in the current class.
             */
            $currentlyAttending =
                $currentClassStudents;
        }


        /*
        |--------------------------------------------------------------------------
        | Next Class
        |--------------------------------------------------------------------------
        */

        $nextClass =
            $todayClasses
                ->first(
                    function ($class) use (
                        $currentTime
                    ) {

                        return
                            $class[
                                'raw_time'
                            ]
                            >
                            $currentTime;
                    }
                );


        if ($nextClass) {

            $nextClassTime =
                $nextClass[
                    'class_time'
                ];


            $nextClassStudents =
                $nextClass[
                    'total_students'
                ];
        }


        /*
        |--------------------------------------------------------------------------
        | Absence
        |--------------------------------------------------------------------------
        |
        | Absence module is not implemented yet.
        |
        | Do not create fake absence information.
        |
        */

        $absentToday = 0;

        $absentStudents =
            collect();


        /*
        |--------------------------------------------------------------------------
        | Admin Reminders
        |--------------------------------------------------------------------------
        |
        | No reminder module yet.
        |
        */

        $adminReminders =
            collect();


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard',
            compact(
                'dashboardDate',
                'studentsToday',
                'currentlyAttending',
                'absentToday',
                'wishlistCount',
                'currentClassTime',
                'currentClassStudents',
                'nextClassTime',
                'nextClassStudents',
                'todayClasses',
                'absentStudents',
                'adminReminders'
            )
        );
    }
}
