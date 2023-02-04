<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Common\Support\Str;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Lucid\Units\Job;

class IssueAccessTokenJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected User $user,
        protected string $sessionId,
        protected string $action,
        protected User $createdBy,
        protected ?string $requestCountry = null
    ) {
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle(Request $request): string
    {
        $token = new AuthorizationToken();
        $token->type = AuthorizationToken::TYPE_ACCESS;
        $token->token = Str::random(AuthorizationToken::TOKEN_LENGTH);
        $token->user_id = $this->user->id;
        $token->session_id = $this->sessionId;
        $token->create_ip = $request->ip();
        $token->create_country = $this->requestCountry;
        $token->action = $this->action;
        $token->created_by = $this->createdBy->id;
        $token->create_ua = $request->userAgent();
        $token->expire_at = Carbon::now()->addSeconds(AuthorizationToken::LIFETIME_ACCESS);
        $token->saveOrFail();

        // Access token was set to root domain to cover all services
        $domain = implode('.', array_slice(explode('.', $request->getHttpHost()), -2));
        $secure = !App::environment('local');

        $expireMinutes = Carbon::now()->diffInMinutes($token->expire_at, true);

        // 'authorized' cookie used by frontend to determine that
        // jwt token already present in cookies, because direct access
        // to jwt cookie does not allowed for JavaScript by HttpOnly flag
        /** @phpstan-ignore-next-line */
        Cookie::queue(Cookie::make(
            AuthorizationToken::COOKIE_AUTHORIZED,
            '1',
            $expireMinutes,
            '/',
            $domain,
            App::environment('production'),
            false,
            false,
            'Strict'
        ));

        /** @phpstan-ignore-next-line */
        Cookie::queue(Cookie::make(
            AuthorizationToken::COOKIE_ACCESS,
            $token->token,
            $expireMinutes,
            '/',
            $domain,
            $secure,
            true,
            false,
            'Strict'
        ));

        return $token->token;
    }
}
