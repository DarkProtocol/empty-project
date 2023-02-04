<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetUserByEmailJob;
use App\Domains\Auth\Requests\ResendConfirmationEmailRequest;
use App\Services\Auth\Operations\SendEmailConfirmationMailOperation;
use Illuminate\Support\Facades\App;
use Lucid\Units\Feature;

class ResendConfirmationEmailFeature extends Feature
{
    public function handle(ResendConfirmationEmailRequest $request): mixed
    {
        /** @var User|null $user */
        $user = $this->run(GetUserByEmailJob::class, [
            'email' => $request->input('email'),
        ]);

        if (!$user || $user->activated_at !== null) {
            throw new ApiException(['email' => __('auth.user-not-found')]);
        }

        $this->runInQueue(SendEmailConfirmationMailOperation::class, [
            'user' => $user,
            'locale' => App::getLocale(),
        ]);

        return response()->json(null, 204);
    }
}
