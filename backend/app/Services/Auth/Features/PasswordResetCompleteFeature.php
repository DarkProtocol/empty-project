<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Domains\Auth\Jobs\PasswordResetCompleteJob;
use App\Domains\Auth\Requests\PasswordResetCompleteRequest;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;
use Lucid\Units\Feature;
use Exception;

class PasswordResetCompleteFeature extends Feature
{
    public function handle(PasswordResetCompleteRequest $request, Logger $logger): JsonResponse
    {
        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        try {
            $user = $this->run(PasswordResetCompleteJob::class, [
                'token' => $request->input('token'),
                'ip' => $request->ip(),
                'country' => $requestCountry,
                'ua' => $request->userAgent(),
                'password' => $request->input('password'),
            ]);

            if ($user) {
                return response()->json(null, 204);
            }
        } catch (Exception $e) {
            $logger->error('Password reset complete error: ' . $e->getMessage());
            throw new InternalError();
        }

        throw new ApiException(null, __('auth.user-not-found'));
    }
}
