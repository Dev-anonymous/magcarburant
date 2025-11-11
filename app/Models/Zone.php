<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Zone
 * 
 * @property int $id
 * @property string|null $zone
 * 
 * @property Collection|Fuelprice[] $fuelprices
 *
 * @package App\Models
 */
class Zone extends Model
{
	protected $table = 'zone';
	public $timestamps = false;

	protected $fillable = [
		'zone'
	];

	public function fuelprices()
	{
		return $this->hasMany(Fuelprice::class);
	}
}
