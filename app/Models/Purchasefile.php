<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Purchasefile
 *
 * @property int $id
 * @property int $purchase_id
 * @property string|null $file
 *
 * @property Purchase $purchase
 *
 * @package App\Models
 */
class Purchasefile extends Model
{
    use HasAuditLogs;
    
	protected $table = 'purchasefile';
	public $timestamps = false;

	protected $casts = [
		'purchase_id' => 'int'
	];

	protected $fillable = [
		'purchase_id',
		'file'
	];

	public function purchase()
	{
		return $this->belongsTo(Purchase::class);
	}
}
