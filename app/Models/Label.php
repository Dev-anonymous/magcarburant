<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Label
 * 
 * @property int $id
 * @property string|null $label
 * @property string $tag
 * 
 * @property Collection|Fuelprice[] $fuelprices
 *
 * @package App\Models
 */
class Label extends Model
{
	protected $table = 'label';
	public $timestamps = false;

	protected $fillable = [
		'label',
		'tag'
	];

	public function fuelprices()
	{
		return $this->hasMany(Fuelprice::class);
	}
}
