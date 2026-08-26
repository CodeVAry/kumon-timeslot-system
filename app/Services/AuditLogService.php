<?php

namespace App\Services;

use App\Models\Admin\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    private array $hiddenFields = [
        'password',
        'remember_token',
        'otp',
        'otp_hash',
    ];

    public function log(
        string $action,
        Model $model,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): ?AuditLog {
        /*
         * Staff/admin web guard only.
         * Parent changes are not logged.
         */
        if (!Auth::guard('web')->check()) {
            return null;
        }

        $user = Auth::guard('web')->user();

        $oldValues =
            $this->removeSensitiveFields(
                $oldValues
            );

        $newValues =
            $this->removeSensitiveFields(
                $newValues
            );

        if (
            $action === 'updated'
            &&
            empty($oldValues)
            &&
            empty($newValues)
        ) {
            return null;
        }

        $auditLog = AuditLog::create([
            'user_id' =>
                $user->id,

            'user_name' =>
                $user->name,

            'user_email' =>
                $user->email,

            'action' =>
                $action,

            'entity_type' =>
                get_class($model),

            'entity_id' =>
                $model->getKey(),

            'description' =>
                $description
                ?? $this->makeDescription(
                    $action,
                    $model
                ),

            'old_values' =>
                !empty($oldValues)
                    ? $oldValues
                    : null,

            'new_values' =>
                !empty($newValues)
                    ? $newValues
                    : null,

            'ip_address' =>
                request()->ip(),

            'user_agent' =>
                request()->userAgent(),

            'created_at' =>
                now(),
        ]);

        /*
         * Session only keeps notification count.
         */
        session([
            'audit_notification_count' =>
                session(
                    'audit_notification_count',
                    0
                ) + 1,
        ]);

        return $auditLog;
    }

    private function removeSensitiveFields(
        array $values
    ): array {
        foreach (
            $this->hiddenFields
            as $field
        ) {
            unset(
                $values[$field]
            );
        }

        return $values;
    }

    private function makeDescription(
        string $action,
        Model $model
    ): string {
        $modelName =
            class_basename(
                $model
            );

        return match ($action) {
            'created' =>
                $modelName . ' created',

            'updated' =>
                $modelName . ' updated',

            'deleted' =>
                $modelName . ' deleted',

            default =>
                $modelName . ' changed',
        };
    }
}
