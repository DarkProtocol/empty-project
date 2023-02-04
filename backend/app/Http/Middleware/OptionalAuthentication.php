<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Common\Http\Exceptions\UnauthorizedException;
use Closure;

class OptionalAuthentication extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authentication($request, $guards);
        } catch (UnauthorizedException $e) {
            // don't do anything
        }

        return $next($request);
    }
}
