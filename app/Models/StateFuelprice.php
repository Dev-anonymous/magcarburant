<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StateFuelprice
 * 
 * @property int $id
 * @property int $state_structureprice_id
 * @property int $fuel_id
 * @property int $label_id
 * @property int $zone_id
 * @property float|null $amount
 * @property string|null $currency
 * 
 * @property Fuel $fuel
 * @property Label $label
 * @property StateStructureprice $state_structureprice
 * @property Zone $zone
 *
 * @package App\Models
 */
class StateFuelprice extends Model
{
	protected $table = 'state_fuelprice';
	public $timestamps = false;

	protected $casts = [
		'state_structureprice_id' => 'int',
		'fuel_id' => 'int',
		'label_id' => 'int',
		'zone_id' => 'int',
		'amount' => 'float'
	];

	protected $fillable = [
		'state_structureprice_id',
		'fuel_id',
		'label_id',
		'zone_id',
		'amount',
		'currency'
	];

	public function fuel()
	{
		return $this->belongsTo(Fuel::class);
	}

	public function label()
	{
		return $this->belongsTo(Label::class);
	}

	public function state_structureprice()
	{
		return $this->belongsTo(StateStructureprice::class);
	}

	public function zone()
	{
		return $this->belongsTo(Zone::class);
	}
}
