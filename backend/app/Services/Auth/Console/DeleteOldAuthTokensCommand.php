<?php

declare(strict_types=1);

namespace App\Services\Auth\Console;

use App\Data\Models\AuthorizationToken;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;
use Exception;

class DeleteOldAuthTokensCommand extends Command
{
    protected $signature = 'auth:delete-old-auth-tokens';

    protected $description = 'Delete old MFA';

    public function handle(Logger $logger): void
    {
        try {
            AuthorizationToken::whereRaw("created_at <= NOW() - interval '3 month'")->delete();
        } catch (Exception $e) {
            $logger->error('Delete old MFA error: ' . $e->getMessage());
        }
    }
}
