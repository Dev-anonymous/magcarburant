<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Fuel
 * 
 * @property int $id
 * @property string $fuel
 * 
 * @property Collection|Fuelprice[] $fuelprices
 *
 * @package App\Models
 */
class Fuel extends Model
{
	protected $table = 'fuel';
	public $timestamps = false;

	protected $fillable = [
		'fuel'
	];

	public function fuelprices()
	{
		return $this->hasMany(Fuelprice::class);
	}
}
