<?php

namespace App\Observers;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        app(AuditLogService::class)->log(
            'created',
            $model,
            [],
            $model->getAttributes()
        );
    }

    public function updated(Model $model): void
    {
        $changedFields = array_keys(
            $model->getChanges()
        );

        $changedFields = array_values(
            array_filter(
                $changedFields,
                fn ($field) => $field !== 'updated_at'
            )
        );

        if (empty($changedFields)) {
            return;
        }

        $oldValues = [];
        $newValues = [];

        foreach ($changedFields as $field) {
            $oldValues[$field] =
                $model->getOriginal($field);

            $newValues[$field] =
                $model->getAttribute($field);
        }

        app(AuditLogService::class)->log(
            'updated',
            $model,
            $oldValues,
            $newValues
        );
    }

    public function deleted(Model $model): void
    {
        app(AuditLogService::class)->log(
            'deleted',
            $model,
            $model->getOriginal(),
            []
        );
    }
}
