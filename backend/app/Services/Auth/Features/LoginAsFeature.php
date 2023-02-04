<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Support\Str;
use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetUserByEmailJob;
use App\Services\Auth\Operations\IssueTokensOperation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lucid\Units\Feature;

class LoginAsFeature extends Feature
{
    protected const CACHE_KEY = 'login-as-%s';
    protected const CACHE_TTL = 60;

    public function handle(Request $request): JsonResponse
    {
        if ($request->get('email') || $request->get('nickname')) {
            /** @var User|null $currentUser */
            if (!$currentUser = $request->user()) {
                return response()->json(null, 404);
            }

            if (!in_array($currentUser->role, User::MODERATORS_ROLES)) {
                return response()->json(null, 404);
            }

            /** @var User|null $user */
            if ($request->has('email')) {
                $user = $this->run(GetUserByEmailJob::class, [
                    'email' => $request->get('email'),
                ]);
            } else {
                $user = User::where([
                    'nickname' => $request->get('nickname'),
                ])->first();
            }

            if (!$user || $user->id === $currentUser->id) {
                return response()->json([
                    'error' => 'User not found'
                ], 422);
            }

            if (
                !in_array($currentUser->role, User::ADMIN_ROLES) &&
                in_array($user->role_id, User::MODERATORS_ROLES)
            ) {
                // only admins can login as admin and moders
                return response()->json([
                    'error' => 'User forbidden'
                ], 403);
            }

            $code = Str::random(32);

            Cache::add(
                sprintf(self::CACHE_KEY, $code),
                [
                    'userId' => $user->id,
                    'initUserId' => $currentUser->id
                ],
                self::CACHE_TTL * 60
            );

            return response()->json([
                'url' => sprintf(
                    '%s?code=%s',
                    route('auth.login-as'),
                    $code
                ),
            ]);
        }

        if ($code = $request->get('code')) {
            $users = Cache::get(sprintf(self::CACHE_KEY, $code));

            if (!isset($users['userId']) || !isset($users['initUserId'])) {
                return response()->json([
                    'error' => 'Code not found'
                ], 422);
            }

            /** @var User|null $user */
            $user = User::where('id', $users['userId'])->first();

            /** @var User|null $initUser */
            $initUser = User::where('id', $users['initUserId'])->first();

            if (!$user || !$initUser) {
                return response()->json([
                    'error' => 'User not found'
                ], 422);
            }

            Cache::forget(sprintf(self::CACHE_KEY, $code));

            $this->run(IssueTokensOperation::class, [
                'user' => $user,
                'action' => AuthorizationToken::ACTION_LOGIN_AS,
                'createdBy' => $initUser,
                'withResponse' => false,
            ]);

            return response()->json([
                'status' => 'OK'
            ], 200);
        }

        return response()->json(null, 404);
    }
}
