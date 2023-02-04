<?php

declare(strict_types=1);

namespace App\Console\Scheduling;

trait VersionedMutex
{
    protected function getVersionedMutex(string $mutex): string
    {
        return config('app.version') . DIRECTORY_SEPARATOR . $mutex;
    }
}
