<?php

declare(strict_types=1);

namespace App\Services\Auth\Console;

use App\Data\Models\AuthorizationToken;
use App\Data\Models\User;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Throwable;

class BanUserCommand extends Command
{
    protected $signature = 'auth:ban-user {email} {reason}';

    protected $description = 'Ban user';

    public function handle(Logger $logger): void
    {
        if (!$this->argument('email')) {
            $this->error('Email is required');
            return;
        }

        if (!$this->argument('reason')) {
            $this->error('Reason is required');
            return;
        }

        /** @var User|null $user */
        $user = User::where('email', mb_strtolower($this->argument('email')))->first();

        if (!$user) {
            $this->error('User not found');
            return;
        }

        if ($user->is_banned) {
            $this->error('User already in ban');
            return;
        }

        $confirm = $this->confirm('Ban user "' . $user->nickname . '"?', true);

        if (!$confirm) {
            $this->error('Canceled!');
            return;
        }

        DB::beginTransaction();

        try {
            $now = now();

            $user->is_banned = true;
            $user->ban_reason = $this->argument('reason');
            $user->banned_at = $now;
            $user->saveOrFail();

            AuthorizationToken::where('user_id', $user->id)
                ->where('expire_at', '>', $now)
                ->update([
                    'revoked_at' => $now,
                ]);

            DB::commit();

            $this->info('User was banned!');
        } catch (Throwable $e) {
            DB::rollBack();
            $logger->error('Error in user ban: ' . $e->getMessage());
            $this->error('Error! ' . $e->getMessage());
        }
    }
}
