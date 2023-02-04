<?php

declare(strict_types=1);

namespace App\Services\Auth\Operations;

use App\Data\Models\PasswordReset;
use App\Data\Models\User;
//use App\Domains\Auth\Notifications\PasswordReset as Notification;
//use App\Domains\Auth\Jobs\SendMailNotificationToUserJob;
use Lucid\Units\QueueableOperation;

class SendPasswordResetMailOperation extends QueueableOperation
{
    public function __construct(
        protected User $user,
        protected PasswordReset $reset,
        protected string $locale,
    ) {
    }

    public function handle(): void
    {
        // TODO
//        $this->run(SendMailNotificationToUserJob::class, [
//            'user' => $this->user,
//            'notification' => new Notification($this->reset->token, $this->locale),
//        ]);
    }
}
