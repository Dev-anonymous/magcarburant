<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AuditLog
 * 
 * @property int $id
 * @property string $event
 * @property string|null $title
 * @property string|null $model_type
 * @property int|null $model_id
 * @property int|null $user_id
 * @property string $username
 * @property int|null $entity_id
 * @property string|null $old_values
 * @property string|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class AuditLog extends Model
{
	protected $table = 'audit_logs';
	public $timestamps = false;

	protected $casts = [
		'model_id' => 'int',
		'user_id' => 'int',
		'entity_id' => 'int'
	];

	protected $fillable = [
		'event',
		'title',
		'model_type',
		'model_id',
		'user_id',
		'username',
		'entity_id',
		'old_values',
		'new_values',
		'ip_address',
		'user_agent'
	];
}
