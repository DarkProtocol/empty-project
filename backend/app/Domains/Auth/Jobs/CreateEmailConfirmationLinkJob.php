<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lucid\Units\Job;

class CreateEmailConfirmationLinkJob extends Job
{
    protected const LIFETIME = 60 * 60 * 24 * 7;

    protected User $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(
        User $user
    ) {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle(): string
    {
        $code = Str::random(64);

        $link = sprintf(
            '%s/sign-up/email-verification?code=%s',
            env('FRONTEND_URL'),
            $code
        );

        $cacheKey = sprintf(ActivateUserByEmailConfirmationCodeJob::CACHE_KEY_TEMPLATE, $code);

        Cache::put($cacheKey, $this->user->id, self::LIFETIME);

        Log::info('Created email confirmation link', [
            'email' => $this->user->email,
            'link' => $link,
        ]);

        return $link;
    }
}
