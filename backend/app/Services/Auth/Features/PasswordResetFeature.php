<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\PasswordReset;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\CreatePasswordResetJob;
use App\Domains\Auth\Jobs\GetUserByEmailJob;
use App\Domains\Auth\Requests\PasswordResetRequest;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use App\Services\Auth\Operations\SendPasswordResetMailOperation;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\App;
use Lucid\Units\Feature;
use Illuminate\Support\Facades\RateLimiter;
use Exception;

class PasswordResetFeature extends Feature
{
    public const ATTEMPT_NAME = 'password-reset';
    public const MAX_ATTEMPTS = 5;

    public function handle(PasswordResetRequest $request, Logger $logger): mixed
    {
        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        /** @var User $user */
        if (!$user = $this->run(GetUserByEmailJob::class, ['email' => $request->input('email')])) {
            throw new ApiException(['email' => __('auth.user-not-found')]);
        }

        $executed = RateLimiter::attempt(
            sprintf('%s:%s', self::ATTEMPT_NAME, $user->id),
            self::MAX_ATTEMPTS,
            function () {
            }
        );

        if (!$executed) {
            return response()->json(null, 429, [
                'Retry-After' => RateLimiter::availableIn(
                    sprintf('%s:%s', self::ATTEMPT_NAME, $user->id)
                ),
            ]);
        }

        try {
            /** @var PasswordReset $reset */
            $reset = $this->run(CreatePasswordResetJob::class, [
                'user' => $user,
                'ip' => $request->ip(),
                'country' => $requestCountry,
                'ua' => $request->userAgent(),
            ]);

            $this->runInQueue(SendPasswordResetMailOperation::class, [
                'user' => $user,
                'reset' => $reset,
                'locale' => App::getLocale(),
            ]);
        } catch (Exception $e) {
            $logger->error('Password reset error: ' . $e->getMessage());
            throw new InternalError();
        }

        return response()->json(null, 204);
    }
}
