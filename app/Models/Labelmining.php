<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Labelmining
 * 
 * @property int $id
 * @property string|null $label
 * @property string $tag
 * 
 * @property Collection|Fuelpricemining[] $fuelpriceminings
 *
 * @package App\Models
 */
class Labelmining extends Model
{
	protected $table = 'labelmining';
	public $timestamps = false;

	protected $fillable = [
		'label',
		'tag'
	];

	public function fuelpriceminings()
	{
		return $this->hasMany(Fuelpricemining::class);
	}
}
