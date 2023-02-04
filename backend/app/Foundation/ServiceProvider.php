<?php

declare(strict_types=1);

namespace App\Foundation;

use App\Services\Auth\Providers\AuthServiceProvider;
use App\Services\Balances\Providers\BalancesServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(BalancesServiceProvider::class);
    }
}
