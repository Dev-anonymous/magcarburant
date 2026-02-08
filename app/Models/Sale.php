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
 * @property string|null $terminal
 * @property int $entity_id
 * @property Carbon|null $date
 * @property string|null $locality
 * @property string|null $way
 * @property string|null $product
 * @property string|null $delivery_note
 * @property string|null $delivery_program
 * @property string|null $client
 * @property float|null $lata
 * @property float|null $l15
 * @property float|null $density
 * @property bool $from_mutuality
 * @property int|null $parent_id
 * @property bool $from_state
 * 
 * @property Entity $entity
 * @property Sale|null $sale
 * @property Collection|Sale[] $sales
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
		'date' => 'datetime',
		'lata' => 'float',
		'l15' => 'float',
		'density' => 'float',
		'from_mutuality' => 'bool',
		'parent_id' => 'int',
		'from_state' => 'bool'
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
		'lata',
		'l15',
		'density',
		'from_mutuality',
		'parent_id',
		'from_state'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function sale()
	{
		return $this->belongsTo(Sale::class, 'parent_id');
	}

	public function sales()
	{
		return $this->hasMany(Sale::class, 'parent_id');
	}

	public function salefiles()
	{
		return $this->hasMany(Salefile::class);
	}
}
