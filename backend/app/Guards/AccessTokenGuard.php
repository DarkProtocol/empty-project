<?php

declare(strict_types=1);

namespace App\Guards;

use App\Data\Models\AuthorizationToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Throwable;

class AccessTokenGuard implements Guard
{
    protected ?Authenticatable $user = null;
    protected ?AuthorizationToken $token = null;

    public function __construct(
        protected UserProvider $userProvider
    ) {
        $tokenValue = Cookie::get(AuthorizationToken::COOKIE_ACCESS);

        if (!$tokenValue) {
            return;
        }

        /** @var AuthorizationToken|null $token */
        $this->token = AuthorizationToken::where(['token' => $tokenValue])
            ->whereNull('revoked_at')
            ->where(['type' => AuthorizationToken::TYPE_ACCESS])
            ->whereRaw('expire_at >= NOW()')
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function check(): bool
    {
        if (!is_null($this->user)) {
            return true;
        }

        if (is_null($this->token)) {
            return false;
        }

        $this->user = $this->userProvider->retrieveById($this->token->user_id);

        return !is_null($this->user);
    }

    /**
     * @inheritDoc
     */
    public function guest(): bool
    {
        return is_null($this->user);
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Get token
     *
     * @return object|null
     */
    public function token(): ?object
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        return !is_null($this->user) ? $this->user->getAuthIdentifier() : null;
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasUser(): bool
    {
        return !is_null($this->user);
    }

    /**
     * @inheritDoc
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function logout(): JsonResponse
    {
        $this->user = null;
        $this->token = null;

        $response = response()->json(null, 401);

        try {
            $request = resolve(Request::class);

            $domain = implode('.', array_slice(explode('.', $request->getHttpHost()), -2));

            $response->headers->clearCookie(AuthorizationToken::COOKIE_AUTHORIZED, '/', $domain);
            $response->headers->clearCookie(AuthorizationToken::COOKIE_ACCESS, '/', $domain);
            $response->headers->clearCookie(
                AuthorizationToken::COOKIE_REFRESH,
                route('auth.token.refresh', [], false),
                $request->getHttpHost()
            );
        } catch (Throwable $e) {
            // Call from console?
        }

        return $response->send();
    }
}
