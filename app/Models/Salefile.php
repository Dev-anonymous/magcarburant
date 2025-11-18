<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Salefile
 * 
 * @property int $id
 * @property int $sale_id
 * @property string|null $file
 * 
 * @property Sale $sale
 *
 * @package App\Models
 */
class Salefile extends Model
{
	protected $table = 'salefile';
	public $timestamps = false;

	protected $casts = [
		'sale_id' => 'int'
	];

	protected $fillable = [
		'sale_id',
		'file'
	];

	public function sale()
	{
		return $this->belongsTo(Sale::class);
	}
}
