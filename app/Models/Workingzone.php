<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Workingzone
 * 
 * @property int $id
 * @property int $entity_id
 * @property int $zone_id
 * 
 * @property Entity $entity
 * @property Zone $zone
 *
 * @package App\Models
 */
class Workingzone extends Model
{
	protected $table = 'workingzones';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'zone_id' => 'int'
	];

	protected $fillable = [
		'entity_id',
		'zone_id'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function zone()
	{
		return $this->belongsTo(Zone::class);
	}
}
