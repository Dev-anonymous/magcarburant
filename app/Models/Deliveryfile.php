<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
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
    use HasAuditLogs;
    
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
