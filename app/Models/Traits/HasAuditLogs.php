<?php

namespace App\Models\Traits;

use App\Services\AuditService;

trait HasAuditLogs
{
    protected static function bootHasAuditLogs()
    {
        static::created(function ($model) {
            AuditService::log(
                'ajout',
                $model,
                null,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {

            $old = array_intersect_key(
                $model->getOriginal(),
                $model->getChanges()
            );

            $new = $model->getChanges();

            if (!empty($new)) {
                AuditService::log('modification', $model, $old, $new);
            }
        });

        static::deleted(function ($model) {
            AuditService::log(
                'suppresion',
                $model,
                $model->getOriginal(),
                null
            );
        });
    }
}
