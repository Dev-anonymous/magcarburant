<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recovery
 * 
 * @property int $id
 * @property string $email
 * @property string $token
 * @property Carbon $date
 * @property bool $used
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Recovery extends Model
{
	protected $table = 'recovery';

	protected $casts = [
		'date' => 'datetime',
		'used' => 'bool'
	];

	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'email',
		'token',
		'date',
		'used'
	];
}
