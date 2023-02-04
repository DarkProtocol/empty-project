<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Lucid\Units\Job;
use Throwable;

class ActivateUserByEmailConfirmationCodeJob extends Job
{
    public const CACHE_KEY_TEMPLATE = 'register-confirmation-%s';

    protected string $code;

    /**
     * Create a new job instance.
     *
     * @param string $code
     */
    public function __construct(
        string $code
    ) {
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return User|null
     * @throws Throwable
     */
    public function handle(): ?User
    {
        $cacheKey = sprintf(self::CACHE_KEY_TEMPLATE, $this->code);

        if (!$userId = Cache::get($cacheKey)) {
            return null;
        }

        /** @var User $user */
        if (!$user = User::find($userId)) {
            return null;
        }

        $user->activated_at = Carbon::now();
        $user->saveOrFail();

        Cache::forget($cacheKey);

        return $user;
    }
}
