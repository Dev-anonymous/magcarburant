<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sale
 * 
 * @property int $id
 * @property int $entity_id
 * @property Carbon|null $date
 * @property string|null $locality
 * @property string|null $way
 * @property string|null $product
 * @property string|null $delivery_note
 * @property string|null $delivery_program
 * @property string|null $client
 * @property string|null $lata
 * @property string|null $l15
 * @property string|null $density
 * 
 * @property Entity $entity
 * @property Collection|Salefile[] $salefiles
 *
 * @package App\Models
 */
class Sale extends Model
{
	protected $table = 'sale';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'date' => 'datetime'
	];

	protected $fillable = [
		'entity_id',
		'date',
		'locality',
		'way',
		'product',
		'delivery_note',
		'delivery_program',
		'client',
		'lata',
		'l15',
		'density'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function salefiles()
	{
		return $this->hasMany(Salefile::class);
	}
}
