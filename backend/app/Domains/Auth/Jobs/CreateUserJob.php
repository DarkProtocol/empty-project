<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Illuminate\Support\Facades\Hash;
use Lucid\Units\Job;

class CreateUserJob extends Job
{
    public function __construct(
        protected string $email,
        protected string $nickname,
        protected string $password,
        protected ?string $ip,
        protected ?string $country,
        protected ?string $ua,
        protected ?string $utm,
        protected ?string $referrer
    ) {
    }

    public function handle(): User
    {
        $user = new User();
        $user->email = mb_strtolower(trim($this->email));
        $user->nickname = htmlspecialchars(trim($this->nickname));
        $user->password = Hash::make($this->password);
        $user->role = User::ROLE_USER;
        $user->create_ip = $this->ip ? trim($this->ip) : null;
        $user->create_country = $this->country ? trim($this->country) : null;
        $user->create_ua = $this->ua ? trim($this->ua) : null;
        $user->utm = $this->utm ? trim($this->utm) : null;

        if ($this->referrer) {
            /** @var User $referer */
            if ($referrer = User::where('nickname', $this->referrer)->first()) {
                $user->ref_id = $referrer->id;
            }
        }

        $user->saveOrFail();

        return $user;
    }
}
