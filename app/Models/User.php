<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $user_role
 * @property int|null $role_id
 *
 * @property Role|null $role
 * @property User|null $user
 * @property Collection|AccountingClosure[] $accounting_closures
 * @property Collection|Entity[] $entities
 * @property Collection|Permission[] $permissions
 * @property Collection|Role[] $roles
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasAuditLogs;

    protected $table = 'users';

    protected $casts = [
        'user_id' => 'int',
        'email_verified_at' => 'datetime',
        'role_id' => 'int'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'user_role',
        'role_id'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accounting_closures()
    {
        return $this->hasMany(AccountingClosure::class, 'closed_by');
    }

    public function entities()
    {
        return $this->hasMany(Entity::class, 'users_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'users_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'users_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
