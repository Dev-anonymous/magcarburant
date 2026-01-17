<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Delivery
 * 
 * @property int $id
 * @property string|null $terminal
 * @property int $entity_id
 * @property Carbon|null $date
 * @property string|null $locality
 * @property string|null $way
 * @property string|null $product
 * @property string|null $delivery_note
 * @property string|null $delivery_program
 * @property string|null $client
 * @property float|null $qtym3
 * @property float|null $unitprice
 * 
 * @property Entity $entity
 * @property Collection|Deliveryfile[] $deliveryfiles
 *
 * @package App\Models
 */
class Delivery extends Model
{
	protected $table = 'delivery';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'date' => 'datetime',
		'qtym3' => 'float',
		'unitprice' => 'float'
	];

	protected $fillable = [
		'terminal',
		'entity_id',
		'date',
		'locality',
		'way',
		'product',
		'delivery_note',
		'delivery_program',
		'client',
		'qtym3',
		'unitprice'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function deliveryfiles()
	{
		return $this->hasMany(Deliveryfile::class);
	}
}
