<?php

declare(strict_types=1);

namespace App\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
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
        $locale = mb_strtolower(substr($request->header('Accept-Language', 'ru'), 0, 2));

        App::setLocale($locale ?: config('app.locale'));

        return $next($request);
    }
}
