<?php

declare(strict_types=1);

namespace App\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsApp
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ua = $request->userAgent();

        if (!$ua || !str_starts_with(strtolower($ua), 'knb')) {
            return response()->json(null, 403);
        }

        return $next($request);
    }
}
