<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use Illuminate\Support\Facades\Cache;
use Lucid\Units\Job;

class GetUserLastSeenJob extends Job
{
    public const CACHE_KEY = 'user-last-seen-%s';

    public function __construct(protected string $userId)
    {
    }

    public function handle(): ?int
    {
        $lastSeen = Cache::get(sprintf(self::CACHE_KEY, $this->userId));
        return $lastSeen ? (int) $lastSeen : null;
    }
}
