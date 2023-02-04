<?php

declare(strict_types=1);

namespace App\Services\Auth\Operations;

use App\Common\Http\Exceptions\ApiException;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetActiveAccessTokensJobByUserId;
use App\Domains\Auth\Jobs\IssueAccessTokenJob;
use App\Domains\Auth\Jobs\IssueRefreshTokenJob;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Lucid\Units\Operation;

class IssueTokensOperation extends Operation
{
    /**
     * Create a new operation instance.
     *
     * @return void
     */
    public function __construct(
        protected User $user,
        protected string $action,
        protected User $createdBy,
        protected ?string $sessionId = null,
        protected bool $withResponse = true
    ) {
    }

    /**
     * Execute the operation.
     *
     * @return JsonResponse|null
     * @throws ApiException
     */
    public function handle(): ?JsonResponse
    {
        if ($this->action !== AuthorizationToken::ACTION_LOGIN_AS) {
            // login-as forced

            /** @var Collection $activeTokens */
            $activeTokens = $this->run(GetActiveAccessTokensJobByUserId::class, [
                'userId' => $this->user->id,
            ]);

            if ($activeTokens->count() >= 5) {
                throw new ApiException(['email' => __('auth.many-devices')]);
            }
        }

        $this->sessionId = $this->sessionId ?: Str::orderedUuid()->toString();

        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        $this->run(IssueAccessTokenJob::class, [
            'user' => $this->user,
            'sessionId' => $this->sessionId,
            'requestCountry' => $requestCountry,
            'action' => $this->action,
            'createdBy' => $this->createdBy,
        ]);

        $this->run(IssueRefreshTokenJob::class, [
            'user' => $this->user,
            'sessionId' => $this->sessionId,
            'requestCountry' => $requestCountry,
            'action' => $this->action,
            'createdBy' => $this->createdBy,
        ]);

        return $this->withResponse ? response()->json(null, 204) : null;
    }
}
