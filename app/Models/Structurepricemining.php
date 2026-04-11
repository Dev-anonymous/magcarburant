<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Structurepricemining
 * 
 * @property int $id
 * @property int $entity_id
 * @property string|null $name
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property float|null $usd_cdf
 * 
 * @property Entity $entity
 * @property Collection|Fuelpricemining[] $fuelpriceminings
 *
 * @package App\Models
 */
class Structurepricemining extends Model
{
	protected $table = 'structurepricemining';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'from' => 'datetime',
		'to' => 'datetime',
		'usd_cdf' => 'float'
	];

	protected $fillable = [
		'entity_id',
		'name',
		'from',
		'to',
		'usd_cdf'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function fuelpriceminings()
	{
		return $this->hasMany(Fuelpricemining::class);
	}
}
