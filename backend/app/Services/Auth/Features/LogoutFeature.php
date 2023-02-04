<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Data\Models\AuthorizationToken;
use App\Domains\Auth\Jobs\RevokeTokensBySessionIdJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Lucid\Units\Feature;

class LogoutFeature extends Feature
{
    public function handle(Request $request): JsonResponse
    {
        $this->forgetCookies($request);

        /** @var AuthorizationToken $token */
        if (!$token = Auth::token()) {
            return response()->json(null, 204);
        }

        $this->run(RevokeTokensBySessionIdJob::class, [
            'sessionId' => $token->session_id,
        ]);

        return response()->json(null, 204);
    }

    /**
     * Forget cookies
     *
     * @param Request $request
     */
    protected function forgetCookies(Request $request): void
    {
        $domain = implode('.', array_slice(explode('.', $request->getHttpHost()), -2));

        /** @phpstan-ignore-next-line */
        Cookie::queue(Cookie::forget(
            AuthorizationToken::COOKIE_AUTHORIZED,
            '/',
            $domain
        ));

        /** @phpstan-ignore-next-line */
        Cookie::queue(Cookie::forget(
            AuthorizationToken::COOKIE_ACCESS,
            '/',
            $domain
        ));

        /** @phpstan-ignore-next-line */
        Cookie::queue(Cookie::forget(
            AuthorizationToken::COOKIE_REFRESH,
            route('auth.token.refresh', [], false),
            $request->getHttpHost()
        ));
    }
}
