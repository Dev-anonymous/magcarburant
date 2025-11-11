<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Fuelprice
 * 
 * @property int $id
 * @property int $structureprice_id
 * @property int $fuel_id
 * @property int $label_id
 * @property int $zone_id
 * @property float|null $amount
 * @property string|null $currency
 * 
 * @property Fuel $fuel
 * @property Label $label
 * @property Structureprice $structureprice
 * @property Zone $zone
 *
 * @package App\Models
 */
class Fuelprice extends Model
{
	protected $table = 'fuelprice';
	public $timestamps = false;

	protected $casts = [
		'structureprice_id' => 'int',
		'fuel_id' => 'int',
		'label_id' => 'int',
		'zone_id' => 'int',
		'amount' => 'float'
	];

	protected $fillable = [
		'structureprice_id',
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

	public function structureprice()
	{
		return $this->belongsTo(Structureprice::class);
	}

	public function zone()
	{
		return $this->belongsTo(Zone::class);
	}
}
