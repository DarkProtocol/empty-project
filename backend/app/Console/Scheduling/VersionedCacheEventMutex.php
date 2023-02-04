<?php

declare(strict_types=1);

namespace App\Console\Scheduling;

use Illuminate\Console\Scheduling\CacheEventMutex;
use Illuminate\Console\Scheduling\Event;

class VersionedCacheEventMutex extends CacheEventMutex
{
    use VersionedMutex;

    public function create(Event $event)
    {
        return $this->cache->store($this->store)->add(
            $this->getVersionedMutex($event->mutexName()),
            true,
            $event->expiresAt * 60
        );
    }

    public function exists(Event $event)
    {
        return $this->cache->store($this->store)->has($this->getVersionedMutex($event->mutexName()));
    }

    public function forget(Event $event)
    {
        $this->cache->store($this->store)->forget($this->getVersionedMutex($event->mutexName()));
    }
}
