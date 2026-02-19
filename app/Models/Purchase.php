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
 * Class Purchase
 *
 * @property int $id
 * @property int $entity_id
 * @property Carbon|null $date
 * @property string $way
 * @property string|null $product
 * @property string|null $provider
 * @property string|null $billnumber
 * @property float|null $unitprice
 * @property float|null $qtytm
 * @property float|null $qtym3
 * @property float|null $density
 * @property bool $from_state
 *
 * @property Entity $entity
 * @property Collection|Purchasefile[] $purchasefiles
 *
 * @package App\Models
 */
class Purchase extends Model
{
    use HasAccountingLock, HasAuditLogs;

    protected $table = 'purchase';
    public $timestamps = false;

    protected $casts = [
        'entity_id' => 'int',
        'date' => 'datetime',
        'unitprice' => 'float',
        'qtytm' => 'float',
        'qtym3' => 'float',
        'density' => 'float',
        'from_state' => 'bool'
    ];

    protected $fillable = [
        'entity_id',
        'date',
        'way',
        'product',
        'provider',
        'billnumber',
        'unitprice',
        'qtytm',
        'qtym3',
        'density',
        'from_state'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function purchasefiles()
    {
        return $this->hasMany(Purchasefile::class);
    }
}
