<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StateStructureprice
 * 
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property float|null $usd_cdf
 * @property float|null $cdf_usd
 * 
 * @property Collection|StateFuelprice[] $state_fuelprices
 *
 * @package App\Models
 */
class StateStructureprice extends Model
{
	protected $table = 'state_structureprice';
	public $timestamps = false;

	protected $casts = [
		'from' => 'datetime',
		'to' => 'datetime',
		'usd_cdf' => 'float',
		'cdf_usd' => 'float'
	];

	protected $fillable = [
		'name',
		'from',
		'to',
		'usd_cdf',
		'cdf_usd'
	];

	public function state_fuelprices()
	{
		return $this->hasMany(StateFuelprice::class);
	}
}
