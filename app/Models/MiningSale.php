<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MiningSale
 *
 * @property int $id
 * @property int $entity_id
 * @property string|null $terminal
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
 * @property bool $from_state
 * @property bool $from_mutuality
 * @property int|null $parent_id
 *
 * @property Entity $entity
 * @property MiningSale|null $mining_sale
 * @property Collection|MiningSale[] $mining_sales
 * @property Collection|MiningSaleFile[] $mining_sale_files
 *
 * @package App\Models
 */
class MiningSale extends Model
{
    use HasAuditLogs;
    
	protected $table = 'mining_sale';
	public $timestamps = false;

	protected $casts = [
		'entity_id' => 'int',
		'date' => 'datetime',
		'lata' => 'float',
		'l15' => 'float',
		'density' => 'float',
		'from_state' => 'bool',
		'from_mutuality' => 'bool',
		'parent_id' => 'int'
	];

	protected $fillable = [
		'entity_id',
		'terminal',
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
		'from_state',
		'from_mutuality',
		'parent_id'
	];

	public function entity()
	{
		return $this->belongsTo(Entity::class);
	}

	public function mining_sale()
	{
		return $this->belongsTo(MiningSale::class, 'parent_id');
	}

	public function mining_sales()
	{
		return $this->hasMany(MiningSale::class, 'parent_id');
	}

	public function mining_sale_files()
	{
		return $this->hasMany(MiningSaleFile::class);
	}
}
