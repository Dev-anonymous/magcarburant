<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $id
 * @property int $users_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Collection|Permission[] $permissions
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'roles';

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

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'role_has_permission')
					->withPivot('id')
					->withTimestamps();
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
