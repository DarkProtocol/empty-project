<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetUserByEmailJob;
use App\Domains\Auth\Requests\LoginRequest;
use App\Services\Auth\Operations\IssueTokensOperation;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Hash;
use Lucid\Units\Feature;
use Exception;

class LoginFeature extends Feature
{
    /**
     * @throws InternalError
     * @throws ApiException
     */
    public function handle(LoginRequest $request, Logger $logger): mixed
    {
        /** @var User|null $user */
        $user = $this->run(GetUserByEmailJob::class, ['email' => $request->input('email')]);

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw new ApiException(['email' => __('auth.failed')]);
        }

        if ($user->role === User::ROLE_BOT) {
            throw new ApiException(['email' => __('auth.failed')]);
        }

        if (!$user->activated_at) {
            throw new ApiException(['email' => __('auth.inactive')]);
        }

        if ($user->is_banned) {
            throw new ApiException(['email' => __('auth.banned')]);
        }

        try {
            return $this->run(IssueTokensOperation::class, [
                'user' => $user,
                'action' => AuthorizationToken::ACTION_LOGIN,
                'createdBy' => $user,
            ]);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            $logger->error('Error on reset avatar: ' . $e->getMessage());
            throw new InternalError();
        }

    }
}
