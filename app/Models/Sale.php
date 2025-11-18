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
 * @property string|null $product
 * @property string|null $provider
 * @property string|null $billnumber
 * @property float|null $unitprice
 * @property float|null $qtytm
 * @property float|null $qtym3
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
		'date' => 'datetime',
		'unitprice' => 'float',
		'qtytm' => 'float',
		'qtym3' => 'float'
	];

	protected $fillable = [
		'entity_id',
		'date',
		'product',
		'provider',
		'billnumber',
		'unitprice',
		'qtytm',
		'qtym3',
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
