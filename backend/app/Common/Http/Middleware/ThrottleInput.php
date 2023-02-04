<?php

declare(strict_types=1);

namespace App\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests as Middleware;

class ThrottleInput extends Middleware
{
    protected string $param;

    public function handle(
        $request,
        Closure $next,
        $maxAttempts = 60,
        $decayMinutes = 1,
        $prefix = ''
    ) {
        $this->param = $prefix;

        return parent::handle($request, $next, $maxAttempts, $decayMinutes);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return sha1(strtolower($request->input($this->param)));
    }
}
