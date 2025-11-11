<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Structureprice
 * 
 * @property int $id
 * @property int $entity_id
 * @property string|null $name
 * @property Carbon|null $from
 * @property Carbon|null $to
 * 
 * @property Entity $entity
 * @property Collection|Fuelprice[] $fuelprices
 *
 * @package App\Models
 */
class Structureprice extends Model
{
	protected $table = 'structureprice';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'from' => 'datetime',
		'to' => 'datetime'
	];

	protected $fillable = [
		'entity_id',
		'name',
		'from',
		'to'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function fuelprices()
	{
		return $this->hasMany(Fuelprice::class);
	}
}
