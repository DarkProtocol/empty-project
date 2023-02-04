<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Lucid\Units\Job;

class GetUserByEmailJob extends Job
{
    protected string $email;

    /**
     * Create a new job instance.
     *
     * @param string $email
     */
    public function __construct(
        string $email
    ) {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return User|null
     */
    public function handle(): ?User
    {
        return User::where('email', mb_strtolower($this->email))->first();
    }
}
