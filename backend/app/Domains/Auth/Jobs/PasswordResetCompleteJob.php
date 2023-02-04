<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\PasswordReset;
use App\Data\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Lucid\Units\Job;

class PasswordResetCompleteJob extends Job
{
    public function __construct(
        protected string $token,
        protected ?string $ip,
        protected ?string $country,
        protected ?string $ua,
        protected string $password,
    ) {
    }

    public function handle(): ?User
    {
        /** @var PasswordReset|null $reset */
        $reset = PasswordReset::query()
            ->where('token', $this->token)
            ->whereNull('completed_at')
            ->first();

        if (!$reset) {
            return null;
        }

        $reset->complete_ip = $this->ip;
        $reset->complete_country = $this->country;
        $reset->complete_ua = $this->ua;
        $reset->completed_at = Carbon::now();
        $reset->saveOrFail();

        $reset->user->password = Hash::make($this->password);

        if (!$reset->user->activated_at) {
            $reset->user->activated_at = Carbon::now();
        }

        $reset->user->saveOrFail();

        return $reset->user;
    }
}
