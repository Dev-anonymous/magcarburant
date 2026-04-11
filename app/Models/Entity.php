<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Entity
 * 
 * @property int $id
 * @property int $users_id
 * @property string $shortname
 * @property string $longname
 * @property string|null $logo
 * 
 * @property User $user
 * @property Collection|AccountingClosure[] $accounting_closures
 * @property Collection|Delivery[] $deliveries
 * @property Collection|MiningSale[] $mining_sales
 * @property Collection|Purchase[] $purchases
 * @property Collection|Rate[] $rates
 * @property Collection|Sale[] $sales
 * @property Collection|SecurityStock[] $security_stocks
 * @property Collection|Structureprice[] $structureprices
 * @property Collection|Structurepricemining[] $structurepriceminings
 * @property Collection|Workingzone[] $workingzones
 *
 * @package App\Models
 */
class Entity extends Model
{
	protected $table = 'entity';
	public $timestamps = false;

	protected $casts = [
		'users_id' => 'int'
	];

	protected $fillable = [
		'users_id',
		'shortname',
		'longname',
		'logo'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'users_id');
	}

	public function accounting_closures()
	{
		return $this->hasMany(AccountingClosure::class);
	}

	public function deliveries()
	{
		return $this->hasMany(Delivery::class);
	}

	public function mining_sales()
	{
		return $this->hasMany(MiningSale::class);
	}

	public function purchases()
	{
		return $this->hasMany(Purchase::class);
	}

	public function rates()
	{
		return $this->hasMany(Rate::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}

	public function security_stocks()
	{
		return $this->hasMany(SecurityStock::class);
	}

	public function structureprices()
	{
		return $this->hasMany(Structureprice::class);
	}

	public function structurepriceminings()
	{
		return $this->hasMany(Structurepricemining::class);
	}

	public function workingzones()
	{
		return $this->hasMany(Workingzone::class);
	}
}
