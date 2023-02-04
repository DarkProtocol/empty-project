<?php

declare(strict_types=1);

namespace App\Data\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $user_id
 * @property string $token
 * @property string|null $create_ip
 * @property string|null $create_country
 * @property string|null $create_ua
 * @property string|null $complete_ip
 * @property string|null $complete_country
 * @property string|null $complete_ua
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 */
class PasswordReset extends Model
{
    public const TOKEN_LENGTH = 32;

    protected $primaryKey = 'token';
    public $incrementing = false;

    /** @var array<string, string> */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
