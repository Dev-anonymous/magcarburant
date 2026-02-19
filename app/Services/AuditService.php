<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log($event, $model = null, $old = null, $new = null)
    {
        $user = Auth::user();
        AuditLog::create([
            'event'      => $event,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model->id ?? null,
            'user_id'    => $user->id,
            'username'   => $user->name,
            'entity_id'  => $model->entity_id ?? null,
            'old_values' => json_encode($old),
            'new_values' => json_encode($new),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => nnow(),
        ]);
    }
}
