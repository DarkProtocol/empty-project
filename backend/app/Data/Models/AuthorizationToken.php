<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Common\Traits\AutoUUID;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $token
 * @property string $session_id
 * @property int $type
 * @property string|null $create_ip
 * @property string|null $create_country
 * @property string|null $create_ua
 * @property string|null $created_by
 * @property string|null $action
 * @property Carbon $expire_at
 * @property Carbon|null $revoked_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 */
class AuthorizationToken extends Model
{
    use AutoUUID;

    public const TYPE_ACCESS = 'access';
    public const TYPE_REFRESH = 'refresh';

    public const TOKEN_LENGTH = 64;

    public const LIFETIME_ACCESS = 86400; // 24h;
    public const LIFETIME_REFRESH = 604800; // 1w;

    public const COOKIE_ACCESS = 'access_token';
    public const COOKIE_REFRESH = 'refresh_token';
    public const COOKIE_AUTHORIZED = 'authorized';

    public const ACTION_LOGIN = 'login';
    public const ACTION_REFRESH = 'refresh';
    public const ACTION_PASSWORD_RESET = 'password-reset';
    public const ACTION_USER_ACTIVATE = 'user-activate';
    public const ACTION_LOGIN_AS = 'login-as';

    protected $keyType = 'string';
    public $incrementing = false;

    /** @var array<string, string> */
    protected $casts = [
        'expire_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
