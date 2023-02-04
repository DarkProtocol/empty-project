<?php

declare(strict_types=1);

namespace App\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests as Middleware;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use RuntimeException;

class ThrottleRequests extends Middleware
{
    /**
     * The identifier to be used in the throttle signature.
     *
     * @var string|null
     */
    protected ?string $slug;

    public function handle(
        $request,
        Closure $next,
        $maxAttempts = 60,
        $decayMinutes = 1,
        $prefix = ''
    ) {
        $this->slug = $prefix === '' ? null : $prefix;

        return parent::handle($request, $next, $maxAttempts, $decayMinutes);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        if (!$route = $request->route()) {
            throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
        }

        if (!App::environment('production') && !env('DEBUG_THROTTLE')) {
            return sha1(Str::random(64));
        }

        if ($this->slug) {
            return $this->generateGroupSignature($request);
        }

        return $this->generateRouteSignature($request, $route);
    }

    protected function generateGroupSignature(Request $request): string
    {
        $identifier = ($user = $request->user())
            ? $user->getAuthIdentifier()
            : $request->ip();

        return sha1($this->slug . '|' . $identifier);
    }

    protected function generateRouteSignature(Request $request, Route $route): string
    {
        $identifier = ($user = $request->user())
            ? $user->getAuthIdentifier()
            : $request->ip();

        return sha1(
            implode(
                '|',
                array_merge(
                    $route->methods(),
                    [
                        $route->getDomain(),
                        $route->uri(),
                        $identifier
                    ]
                )
            )
        );
    }
}
