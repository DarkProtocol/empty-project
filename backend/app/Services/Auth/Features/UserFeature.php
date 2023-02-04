<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Data\Models\User;
use Illuminate\Http\Request;
use Lucid\Units\Feature;

class UserFeature extends Feature
{
    public function handle(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();

        return [
            'id' => $user->id,
            'email' => $user->email,
            'nickname' => $user->nickname,
        ];
    }
}
