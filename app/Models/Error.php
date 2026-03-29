<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Error
 * 
 * @property int $id
 * @property string|null $type
 * @property string|null $message
 * @property string|null $source
 * @property int|null $line
 * @property int|null $column
 * @property string|null $stack
 * @property string|null $url
 * @property string|null $user_agent
 * @property int|null $user_id
 * @property string|null $ip
 * @property string|null $payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Error extends Model
{
	protected $table = 'errors';

	protected $casts = [
		'line' => 'int',
		'column' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'type',
		'message',
		'source',
		'line',
		'column',
		'stack',
		'url',
		'user_agent',
		'user_id',
		'ip',
		'payload'
	];
}
