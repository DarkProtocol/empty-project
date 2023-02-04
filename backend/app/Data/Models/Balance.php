<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Common\Traits\AutoUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $currency_id
 * @property string $value
 * @property-read User $user
 * @property-read Currency $currency
 */
class Balance extends Model
{
    use AutoUUID;

    protected $keyType = 'string';
    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
