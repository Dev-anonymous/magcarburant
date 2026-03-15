<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\Traits\HasAuditLogs;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Securitystockfile
 *
 * @property int $id
 * @property int $security_stock_id
 * @property string|null $file
 *
 * @property SecurityStock $security_stock
 *
 * @package App\Models
 */
class Securitystockfile extends Model
{
    use HasAuditLogs;

    protected $table = 'securitystockfile';
    public $timestamps = false;

    protected $casts = [
        'security_stock_id' => 'int'
    ];

    protected $fillable = [
        'security_stock_id',
        'file'
    ];

    public function security_stock()
    {
        return $this->belongsTo(SecurityStock::class);
    }
}
