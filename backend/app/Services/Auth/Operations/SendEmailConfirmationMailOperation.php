<?php

declare(strict_types=1);

namespace App\Services\Auth\Operations;

use App\Data\Models\User;
use App\Domains\Auth\Jobs\CreateEmailConfirmationLinkJob;
//use App\Domains\Auth\Notifications\EmailConfirmation;
//use App\Domains\Auth\Jobs\SendMailNotificationToUserJob;
use Lucid\Units\QueueableOperation;

class SendEmailConfirmationMailOperation extends QueueableOperation
{
    public function __construct(
        protected User $user,
        protected string $locale,
    ) {
    }

    public function handle(): void
    {
        $confirmationLink = $this->run(CreateEmailConfirmationLinkJob::class, [
            'user' => $this->user,
        ]);
        // TODO
//        $this->run(SendMailNotificationToUserJob::class, [
//            'user' => $this->user,
//            'notification' => new EmailConfirmation($confirmationLink, $this->locale),
//        ]);
    }
}
