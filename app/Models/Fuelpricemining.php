<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Fuelpricemining
 * 
 * @property int $id
 * @property int $structurepricemining_id
 * @property int $fuel_id
 * @property int $labelmining_id
 * @property int $zone_id
 * @property float|null $amount
 * @property string|null $currency
 * 
 * @property Fuel $fuel
 * @property Labelmining $labelmining
 * @property Structurepricemining $structurepricemining
 * @property Zone $zone
 *
 * @package App\Models
 */
class Fuelpricemining extends Model
{
	protected $table = 'fuelpricemining';
	public $timestamps = false;

	protected $casts = [
		'structurepricemining_id' => 'int',
		'fuel_id' => 'int',
		'labelmining_id' => 'int',
		'zone_id' => 'int',
		'amount' => 'float'
	];

	protected $fillable = [
		'structurepricemining_id',
		'fuel_id',
		'labelmining_id',
		'zone_id',
		'amount',
		'currency'
	];

	public function fuel()
	{
		return $this->belongsTo(Fuel::class);
	}

	public function labelmining()
	{
		return $this->belongsTo(Labelmining::class);
	}

	public function structurepricemining()
	{
		return $this->belongsTo(Structurepricemining::class);
	}

	public function zone()
	{
		return $this->belongsTo(Zone::class);
	}
}
