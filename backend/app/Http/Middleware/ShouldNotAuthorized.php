<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Common\Http\Exceptions\NotAllowedException;
use App\Common\Http\Exceptions\UnauthorizedException;
use Closure;

class ShouldNotAuthorized extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authentication($request, $guards);
        } catch (UnauthorizedException $e) {
            return $next($request);
        }

        throw new NotAllowedException();
    }
}
