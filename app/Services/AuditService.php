<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log($event, $model = null, $old = null, $new = null, $title = null)
    {
        $user = Auth::user();

        AuditLog::create([
            'event'      => $event,
            'title' => $title,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model->id ?? null,
            'user_id'    => $user->id,
            'username'   => $user->name,
            'entity_id'  => $model->entity_id ?? null,
            'old_values' => is_array($old) && count($old) ? json_encode($old) : null,
            'new_values' => is_array($new) && count($new) ?  json_encode($new) : null,
            'ip_address' => Request::ip(),
            'user_agent' => ua(),
            'created_at' => nnow(),
        ]);
    }
}
