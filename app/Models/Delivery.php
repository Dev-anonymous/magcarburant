<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAccountingLock;
use App\Models\Traits\HasAuditLogs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Delivery
 *
 * @property int $id
 * @property string|null $terminal
 * @property int $entity_id
 * @property Carbon|null $date
 * @property string|null $locality
 * @property string|null $way
 * @property string|null $product
 * @property string|null $delivery_note
 * @property string|null $delivery_program
 * @property string|null $client
 * @property float|null $lata
 * @property float|null $unitprice
 * @property bool $from_state
 *
 * @property Entity $entity
 * @property Collection|Deliveryfile[] $deliveryfiles
 *
 * @package App\Models
 */
class Delivery extends Model
{
    use HasAccountingLock, HasAuditLogs;

    protected $table = 'delivery';
    public $timestamps = false;

    protected $casts = [
        'entity_id' => 'int',
        'date' => 'datetime',
        'lata' => 'float',
        'unitprice' => 'float',
        'from_state' => 'bool'
    ];

    protected $fillable = [
        'terminal',
        'entity_id',
        'date',
        'locality',
        'way',
        'product',
        'delivery_note',
        'delivery_program',
        'client',
        'lata',
        'unitprice',
        'from_state'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function deliveryfiles()
    {
        return $this->hasMany(Deliveryfile::class);
    }
}
