<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Common\Support\Str;
use App\Data\Models\PasswordReset;
use App\Data\Models\User;
use Lucid\Units\Job;

class CreatePasswordResetJob extends Job
{
    public function __construct(
        protected User $user,
        protected ?string $ip,
        protected ?string $country,
        protected ?string $ua,
    ) {
    }

    public function handle(): PasswordReset
    {
        $reset = new PasswordReset();
        $reset->user_id = $this->user->id;
        $reset->token = Str::random(PasswordReset::TOKEN_LENGTH);
        $reset->create_ip = $this->ip;
        $reset->create_country = $this->country;
        $reset->create_ua = $this->ua;
        $reset->saveOrFail();

        return $reset;
    }
}
