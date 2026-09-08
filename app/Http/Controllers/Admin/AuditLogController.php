<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Audit Log List
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {
        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );


        $action =
            $request->input(
                'action'
            );


        $entity =
            $request->input(
                'entity'
            );


        $timeRange =
            $request->input(
                'time_range',
                '28_days'
            );


        $fromDate =
            $request->input(
                'from_date'
            );


        $toDate =
            $request->input(
                'to_date'
            );


        /*
        |--------------------------------------------------------------------------
        | Hard Retention Window
        |--------------------------------------------------------------------------
        |
        | Even if cleanup command has not run yet,
        | users only see records from last 28 days.
        |--------------------------------------------------------------------------
        */

        $retentionStart =
            now()
                ->subDays(28)
                ->startOfDay();


        $query =
            AuditLog::query()
                ->with('user')
                ->where(
                    'created_at',
                    '>=',
                    $retentionStart
                );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->where(
                function ($query) use ($search) {

                    $query
                        ->where(
                            'user_name',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'user_email',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'description',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'entity_type',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'entity_reference',
                            'like',
                            '%' . $search . '%'
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Action
        |--------------------------------------------------------------------------
        */

        if (!empty($action)) {

            $query->where(
                'action',
                $action
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Entity / Module
        |--------------------------------------------------------------------------
        */

        if (!empty($entity)) {

            $query->where(
                'entity_type',
                $entity
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Time Range
        |--------------------------------------------------------------------------
        */

        switch ($timeRange) {

            case 'today':

                $query->whereDate(
                    'created_at',
                    today()
                );

                break;


            case '7_days':

                $query->where(
                    'created_at',
                    '>=',
                    now()
                        ->subDays(7)
                        ->startOfDay()
                );

                break;


            case '14_days':

                $query->where(
                    'created_at',
                    '>=',
                    now()
                        ->subDays(14)
                        ->startOfDay()
                );

                break;


            case 'custom':

                if ($fromDate) {

                    $customFrom =
                        Carbon::parse(
                            $fromDate
                        )
                            ->startOfDay();


                    /*
                     * Never allow UI to go
                     * beyond retention period.
                     */
                    if (
                        $customFrom->lt(
                            $retentionStart
                        )
                    ) {

                        $customFrom =
                            $retentionStart
                                ->copy();
                    }


                    $query->where(
                        'created_at',
                        '>=',
                        $customFrom
                    );
                }


                if ($toDate) {

                    $query->where(
                        'created_at',
                        '<=',
                        Carbon::parse(
                            $toDate
                        )->endOfDay()
                    );
                }

                break;


            case '28_days':

            default:

                /*
                 * Already restricted
                 * by retentionStart.
                 */
                break;
        }


        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $auditLogs =
            $query
                ->latest('created_at')
                ->paginate(20)
                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        |
        | Stats respect the same filters except action,
        | so Created / Updated / Deleted remain useful.
        |--------------------------------------------------------------------------
        */

        $statsQuery =
            clone $query;


        if (!empty($action)) {

            /*
             * Rebuild stats independently,
             * because the main query already has
             * the action restriction.
             */
            $statsQuery =
                AuditLog::query()
                    ->where(
                        'created_at',
                        '>=',
                        $retentionStart
                    );


            /*
             * Search
             */
            if ($search !== '') {

                $statsQuery->where(
                    function ($query) use ($search) {

                        $query
                            ->where(
                                'user_name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'user_email',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'description',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'entity_type',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'entity_reference',
                                'like',
                                '%' . $search . '%'
                            );
                    }
                );
            }


            if (!empty($entity)) {

                $statsQuery->where(
                    'entity_type',
                    $entity
                );
            }


            switch ($timeRange) {

                case 'today':

                    $statsQuery
                        ->whereDate(
                            'created_at',
                            today()
                        );

                    break;


                case '7_days':

                    $statsQuery
                        ->where(
                            'created_at',
                            '>=',
                            now()
                                ->subDays(7)
                                ->startOfDay()
                        );

                    break;


                case '14_days':

                    $statsQuery
                        ->where(
                            'created_at',
                            '>=',
                            now()
                                ->subDays(14)
                                ->startOfDay()
                        );

                    break;


                case 'custom':

                    if ($fromDate) {

                        $customFrom =
                            Carbon::parse(
                                $fromDate
                            )
                                ->startOfDay();


                        if (
                            $customFrom->lt(
                                $retentionStart
                            )
                        ) {

                            $customFrom =
                                $retentionStart
                                    ->copy();
                        }


                        $statsQuery
                            ->where(
                                'created_at',
                                '>=',
                                $customFrom
                            );
                    }


                    if ($toDate) {

                        $statsQuery
                            ->where(
                                'created_at',
                                '<=',
                                Carbon::parse(
                                    $toDate
                                )->endOfDay()
                            );
                    }

                    break;
            }
        }


        $stats = [

            'total' =>
                (clone $statsQuery)
                    ->count(),

            'created' =>
                (clone $statsQuery)
                    ->where(
                        'action',
                        'created'
                    )
                    ->count(),

            'updated' =>
                (clone $statsQuery)
                    ->where(
                        'action',
                        'updated'
                    )
                    ->count(),

            'deleted' =>
                (clone $statsQuery)
                    ->where(
                        'action',
                        'deleted'
                    )
                    ->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | Entity Filter Options
        |--------------------------------------------------------------------------
        */

        $entities =
            AuditLog::query()
                ->where(
                    'created_at',
                    '>=',
                    $retentionStart
                )
                ->select('entity_type')
                ->distinct()
                ->orderBy('entity_type')
                ->pluck('entity_type');


        /*
         * Clear session notification badge.
         */
        session()->forget(
            'audit_notification_count'
        );


        return view(
            'admin.audit-logs.index',
            compact(
                'auditLogs',
                'search',
                'action',
                'entity',
                'timeRange',
                'fromDate',
                'toDate',
                'stats',
                'entities'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        AuditLog $auditLog
    ) {
        /*
         * Audit details older than
         * retention period are unavailable.
         */
        if (
            $auditLog
                ->created_at
                ->lt(
                    now()
                        ->subDays(28)
                        ->startOfDay()
                )
        ) {

            abort(404);
        }


        $auditLog->load('user');


        return view(
            'admin.audit-logs.show',
            compact(
                'auditLog'
            )
        );
    }
}
