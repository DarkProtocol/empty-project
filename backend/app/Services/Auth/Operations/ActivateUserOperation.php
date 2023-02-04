<?php

declare(strict_types=1);

namespace App\Services\Auth\Operations;

use App\Data\Models\User;
//use App\Domains\Auth\Jobs\SendMailNotificationToUserJob;
//use App\Domains\Auth\Notifications\Welcome;
use Lucid\Units\QueueableOperation;

class ActivateUserOperation extends QueueableOperation
{
    public function __construct(
        protected User $user,
        protected string $locale
    ) {
    }

    public function handle(): void
    {
        // TODO send
//        $this->run(SendMailNotificationToUserJob::class, [
//            'user' => $this->user,
//            'notification' => new Welcome($this->locale),
//        ]);
    }
}
