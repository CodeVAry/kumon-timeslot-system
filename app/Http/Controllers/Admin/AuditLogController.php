<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
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

        $query =
            AuditLog::query()
                ->with('user');

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
                        );
                }
            );
        }

        if (!empty($action)) {
            $query->where(
                'action',
                $action
            );
        }

        $auditLogs =
            $query
                ->latest('created_at')
                ->paginate(20)
                ->withQueryString();

        /*
         * Clear only the session notification.
         * Audit rows remain in DB.
         */
        session()->forget(
            'audit_notification_count'
        );

        return view(
            'admin.audit-logs.index',
            compact(
                'auditLogs',
                'search',
                'action'
            )
        );
    }

    public function show(
        AuditLog $auditLog
    ) {
        return view(
            'admin.audit-logs.show',
            compact(
                'auditLog'
            )
        );
    }
}
