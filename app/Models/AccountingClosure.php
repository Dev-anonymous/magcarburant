<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountingClosure
 *
 * @property int $id
 * @property int $entity_id
 * @property Carbon $closed_until
 * @property int $closed_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 * @property Entity $entity
 *
 * @package App\Models
 */
class AccountingClosure extends Model
{
    protected $table = 'accounting_closures';

    protected $casts = [
        'entity_id' => 'int',
        'closed_until' => 'datetime',
        'closed_by' => 'int'
    ];

    protected $fillable = [
        'entity_id',
        'closed_until',
        'closed_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public static function lastClosedDate($entityId)
    {
        return self::where('entity_id', $entityId)
            ->max('closed_until');
    }
}
