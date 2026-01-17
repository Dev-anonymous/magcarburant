<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Deliveryfile
 * 
 * @property int $id
 * @property int $delivery_id
 * @property string $file
 * 
 * @property Delivery $delivery
 *
 * @package App\Models
 */
class Deliveryfile extends Model
{
	protected $table = 'deliveryfile';
	public $timestamps = false;

	protected $casts = [
		'delivery_id' => 'int'
	];

	protected $fillable = [
		'delivery_id',
		'file'
	];

	public function delivery()
	{
		return $this->belongsTo(Delivery::class);
	}
}
