<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use App\Services\Auth\Operations\IssueTokensOperation;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Cookie;
use Lucid\Units\Feature;
use Exception;

class TokenRefreshFeature extends Feature
{
    /**
     * @throws ApiException
     * @throws InternalError
     */
    public function handle(Logger $logger)
    {
        $tokenValue = Cookie::get(AuthorizationToken::COOKIE_REFRESH);

        if (!$tokenValue) {
            throw new ApiException(null, __('auth.user-not-found'));
        }

        /** @var AuthorizationToken|null $token */
        $token = AuthorizationToken::where(['token' => $tokenValue])
            ->whereNull('revoked_at')
            ->where(['type' => AuthorizationToken::TYPE_REFRESH])
            ->whereRaw('expire_at >= NOW()')
            ->first();

        if (!$token) {
            throw new ApiException(null, __('auth.user-not-found'));
        }

        /** @var User|null $user */
        if (!$user = User::where(['id' => $token->user_id])->first()) {
            throw new ApiException(null, __('auth.user-not-found'));
        }

        try {
            return $this->run(IssueTokensOperation::class, [
                'user' => $user,
                'action' => AuthorizationToken::ACTION_REFRESH,
                'createdBy' => $user,
                'sessionId' => $token->session_id,
            ]);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            $logger->error('TokenRefreshFeature: ' . $e->getMessage());
            throw new InternalError();
        }

    }
}
