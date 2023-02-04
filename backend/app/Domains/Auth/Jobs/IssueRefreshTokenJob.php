<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Common\Support\Str;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use Carbon\Carbon;
use App\Common\Support\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Lucid\Units\Job;
use Throwable;

class IssueRefreshTokenJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $sessionId
     * @param string|null $requestCountry
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
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function handle(Request $request): string
    {
        $token = new AuthorizationToken();
        $token->type = AuthorizationToken::TYPE_REFRESH;
        $token->token = Str::random(AuthorizationToken::TOKEN_LENGTH);
        $token->user_id = $this->user->id;
        $token->session_id = $this->sessionId;
        $token->create_ip = $request->ip();
        $token->create_country = $this->requestCountry;
        $token->action = $this->action;
        $token->created_by = $this->createdBy->id;
        $token->create_ua = $request->userAgent();
        $token->expire_at = Carbon::now()->addSeconds(AuthorizationToken::LIFETIME_REFRESH);
        $token->saveOrFail();

        $expireMinutes = Carbon::now()->diffInMinutes($token->expire_at, true);

        /** @phpstan-ignore-next-line */
        Cookie::queue(Cookie::make(
            AuthorizationToken::COOKIE_REFRESH,
            $token->token,
            $expireMinutes,
            route('auth.token.refresh', [], false),
            $request->getHttpHost(),
            !App::environment('local'),
            true,
            false,
            'Strict'
        ));

        return $token->token;
    }
}
