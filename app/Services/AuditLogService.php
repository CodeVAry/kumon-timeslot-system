<?php

namespace App\Services;

use App\Models\Admin\AuditLog;
use App\Models\Admin\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    private array $hiddenFields = [
        'password',
        'remember_token',
        'otp',
        'otp_hash',
        'normalized_email',
        'normalized_phone',
    ];


    /*
    |--------------------------------------------------------------------------
    | Create Audit Log
    |--------------------------------------------------------------------------
    */

    public function log(
        string $action,
        Model $model,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): ?AuditLog {

        /*
         * Admin/staff only.
         * Parent portal actions are intentionally excluded.
         */
        if (
            !Auth::guard('web')
                ->check()
        ) {
            return null;
        }


        $user =
            Auth::guard('web')
                ->user();


        $oldValues =
            $this->removeSensitiveFields(
                $oldValues
            );


        $newValues =
            $this->removeSensitiveFields(
                $newValues
            );


        /*
         * Do not create empty update records.
         */
        if (
            $action === 'updated'
            &&
            empty($oldValues)
            &&
            empty($newValues)
        ) {
            return null;
        }


        $auditLog =
            AuditLog::create([

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

                /*
                 * Readable permanent reference.
                 */
                'entity_reference' =>
                    $this->getEntityReference(
                        $model
                    ),

                'description' =>
                    $description
                    ??
                    $this->makeDescription(
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
         * Notification badge remains session-based.
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


    /*
    |--------------------------------------------------------------------------
    | Sensitive Fields
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Readable Entity Reference
    |--------------------------------------------------------------------------
    */

    private function getEntityReference(
        Model $model
    ): ?string {

        /*
         * Student:
         * use manually entered Student ID.
         */
        if ($model instanceof Student) {

            return $model->external_id;
        }


        /*
         * Section.
         */
        if (
            isset($model->section_name)
            &&
            $model->section_name
        ) {

            return $model->section_name;
        }


        /*
         * Student Status.
         */
        if (
            isset($model->status_name)
            &&
            $model->status_name
        ) {

            return $model->status_name;
        }


        /*
         * User.
         */
        if (
            isset($model->name)
            &&
            $model->name
        ) {

            return $model->name;
        }


        /*
         * Guardian.
         */
        if (
            isset($model->first_name)
            &&
            isset($model->last_name)
        ) {

            return trim(
                $model->first_name
                .
                ' '
                .
                $model->last_name
            );
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    */

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
                $modelName
                .
                ' created',

            'updated' =>
                $modelName
                .
                ' updated',

            'deleted' =>
                $modelName
                .
                ' deleted',

            default =>
                $modelName
                .
                ' changed',
        };
    }
}
