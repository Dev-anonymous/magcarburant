<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MiningSaleFile
 *
 * @property int $id
 * @property int $mining_sale_id
 * @property string|null $file
 *
 * @property MiningSale $mining_sale
 *
 * @package App\Models
 */
class MiningSaleFile extends Model
{
    use HasAuditLogs;
    
	protected $table = 'mining_sale_files';
	public $timestamps = false;

	protected $casts = [
		'mining_sale_id' => 'int'
	];

	protected $fillable = [
		'mining_sale_id',
		'file'
	];

	public function mining_sale()
	{
		return $this->belongsTo(MiningSale::class);
	}
}
