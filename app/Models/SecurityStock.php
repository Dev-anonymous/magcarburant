<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SecurityStock
 *
 * @property int $id
 * @property Carbon $month
 * @property int $entity_id
 * @property float $amount
 * @property bool $from_state
 *
 * @property Entity $entity
 *
 * @package App\Models
 */
class SecurityStock extends Model
{
    use HasAuditLogs;

    protected $table = 'security_stock';
    public $timestamps = false;

    protected $casts = [
        'month' => 'datetime',
        'entity_id' => 'int',
        'amount' => 'float',
        'from_state' => 'bool'
    ];

    protected $fillable = [
        'month',
        'entity_id',
        'amount',
        'from_state'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
