<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Common\Http\Exceptions\UnauthorizedException;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetUserLastSeenJob;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws UnauthorizedException|AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authentication($request, $guards);

        return $next($request);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws UnauthorizedException|AuthenticationException
     */
    protected function authentication($request, ...$guards)
    {
        $this->authenticate($request, $guards);

        /** @var User $user */
        $user = $request->user();

        /** @var AuthorizationToken $token */
        $token = Auth::token();

        if ($user->is_banned || !$user->activated_at) {
            $this->unauthenticated($request, $guards);
        }

        if ($token->action === AuthorizationToken::ACTION_LOGIN_AS) {
            return;
        }

        Cache::forever(sprintf(GetUserLastSeenJob::CACHE_KEY, $user->id), time());
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws UnauthorizedException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new UnauthorizedException();
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return null;
    }
}
