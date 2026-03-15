<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * 
 * @property int $id
 * @property int|null $users_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|Role[] $roles
 *
 * @package App\Models
 */
class Permission extends Model
{
	protected $table = 'permissions';

	protected $casts = [
		'users_id' => 'int'
	];

	protected $fillable = [
		'users_id',
		'name'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'users_id');
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_has_permission')
					->withPivot('id')
					->withTimestamps();
	}
}
