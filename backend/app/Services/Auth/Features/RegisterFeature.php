<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\CreateUserJob;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Domains\Balances\Jobs\GenerateBalancesByUserIdJob;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use App\Services\Auth\Operations\SendEmailConfirmationMailOperation;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Lucid\Units\Feature;
use Exception;

class RegisterFeature extends Feature
{
    public function handle(RegisterRequest $request, Logger $logger): mixed
    {
        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        try {
            DB::beginTransaction();
            /** @var User $user */
            $user = $this->run(CreateUserJob::class, [
                'email' => $request->input('email'),
                'nickname' => $request->input('nickname'),
                'password' => $request->input('password'),
                'ip' => $request->ip(),
                'country' => $requestCountry,
                'ua' => $request->userAgent(),
                'utm' => $request->input('utm'),
                'referrer' => $request->input('referrer'),
            ]);

            $this->run(GenerateBalancesByUserIdJob::class, [
                'userId' => $user->id,
                'check' => false,
            ]);

            DB::commit();
            $this->runInQueue(SendEmailConfirmationMailOperation::class, [
                'user' => $user,
                'locale' => App::getLocale(),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $logger->error('Register error: ' . $e->getMessage());
            throw new InternalError();
        }

        return response()->json(null, 201);
    }
}
