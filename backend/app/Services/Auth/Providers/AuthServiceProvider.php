<?php

declare(strict_types=1);

namespace App\Services\Auth\Providers;

use App\Services\Auth\Console\BanUserCommand;
use App\Services\Auth\Console\DeleteOldAuthTokensCommand;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /** @var string[] */
    protected static array $commands = [
        BanUserCommand::class,
        DeleteOldAuthTokensCommand::class,
    ];

    public function boot(): void
    {
        $this->loadMigrationsFrom([
            realpath(__DIR__ . '/../database/migrations')
        ]);
    }

    public function register(): void
    {
        $this->commands(static::$commands);

        $this->app->register(RouteServiceProvider::class);

        $this->registerResources();
    }

    protected function registerResources(): void
    {
        // Translation must be registered ahead of adding lang namespaces
        $this->app->register(TranslationServiceProvider::class);

        Lang::addNamespace('auth', realpath(__DIR__ . '/../resources/lang'));

        View::addNamespace('auth', base_path('resources/views/vendor/auth'));
        View::addNamespace('auth', realpath(__DIR__ . '/../resources/views'));
    }
}
