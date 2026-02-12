<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StateRate
 * 
 * @property int $id
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property float|null $usd_cdf
 * @property float|null $cdf_usd
 *
 * @package App\Models
 */
class StateRate extends Model
{
	protected $table = 'state_rates';
	public $timestamps = false;

	protected $casts = [
		'from' => 'datetime',
		'to' => 'datetime',
		'usd_cdf' => 'float',
		'cdf_usd' => 'float'
	];

	protected $fillable = [
		'from',
		'to',
		'usd_cdf',
		'cdf_usd'
	];
}
