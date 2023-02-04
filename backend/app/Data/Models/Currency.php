<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Common\Traits\AutoUUID;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $symbol
 * @property int $decimals
 */
class Currency extends Model
{
    use AutoUUID;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
