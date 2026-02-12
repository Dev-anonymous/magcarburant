<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 *
 * @property int $id
 * @property int|null $entity_id
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property float|null $usd_cdf
 * @property float|null $cdf_usd
 *
 * @property Entity|null $entity
 *
 * @package App\Models
 */
class Rate extends Model
{
    protected $table = 'rates';
    public $timestamps = false;

    protected $casts = [
        'entity_id' => 'int',
        'from' => 'datetime',
        'to' => 'datetime',
        'usd_cdf' => 'float',
        'cdf_usd' => 'float'
    ];

    protected $fillable = [
        'entity_id',
        'from',
        'to',
        'usd_cdf',
        'cdf_usd'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
