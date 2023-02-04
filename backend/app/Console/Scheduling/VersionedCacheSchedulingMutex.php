<?php

declare(strict_types=1);

namespace App\Console\Scheduling;

use DateTimeInterface;
use Illuminate\Console\Scheduling\CacheSchedulingMutex;
use Illuminate\Console\Scheduling\Event;

class VersionedCacheSchedulingMutex extends CacheSchedulingMutex
{
    use VersionedMutex;

    public function create(Event $event, DateTimeInterface $time)
    {
        return $this->cache->store($this->store)->add(
            $this->getVersionedMutex($event->mutexName()) . $time->format('Hi'),
            true,
            3600
        );
    }

    public function exists(Event $event, DateTimeInterface $time)
    {
        return $this->cache->store($this->store)->has(
            $this->getVersionedMutex($event->mutexName()) . $time->format('Hi')
        );
    }
}
