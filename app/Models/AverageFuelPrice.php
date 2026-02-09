<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AverageFuelPrice
 * 
 * @property int $id
 * @property Carbon $month
 * @property string $product
 * @property int $zone_id
 * @property float $avg_price
 * 
 * @property Zone $zone
 *
 * @package App\Models
 */
class AverageFuelPrice extends Model
{
	protected $table = 'average_fuel_prices';
	public $timestamps = false;

	protected $casts = [
		'month' => 'datetime',
		'zone_id' => 'int',
		'avg_price' => 'float'
	];

	protected $fillable = [
		'month',
		'product',
		'zone_id',
		'avg_price'
	];

	public function zone()
	{
		return $this->belongsTo(Zone::class);
	}
}
