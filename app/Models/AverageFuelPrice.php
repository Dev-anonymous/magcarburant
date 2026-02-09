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
 * @property string $product
 * @property Carbon $month
 * @property float $avg_price
 *
 * @package App\Models
 */
class AverageFuelPrice extends Model
{
	protected $table = 'average_fuel_prices';
	public $timestamps = false;

	protected $casts = [
		'month' => 'datetime',
		'avg_price' => 'float'
	];

	protected $fillable = [
		'product',
		'month',
		'avg_price'
	];
}
