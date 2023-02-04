<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\AuthorizationToken;
use Illuminate\Database\Eloquent\Collection;
use Lucid\Units\Job;

class GetActiveAccessTokensJobByUserId extends Job
{
    public function __construct(
        protected string $userId
    ) {
    }

    public function handle(): Collection
    {
        return AuthorizationToken::where(['user_id' => $this->userId])
            ->whereNull('revoked_at')
            ->where('action', '<>', AuthorizationToken::ACTION_LOGIN_AS)
            ->where(['type' => AuthorizationToken::TYPE_ACCESS])
            ->whereRaw('expire_at >= NOW()')
            ->get();
    }
}
