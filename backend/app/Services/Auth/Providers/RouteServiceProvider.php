<?php

declare(strict_types=1);

namespace App\Services\Auth\Providers;

use Illuminate\Routing\Router;
use Lucid\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function map(Router $router): void
    {
        $namespace = 'App\Services\Auth\Http\Controllers';
        $pathApi = __DIR__ . '/../routes/api.php';

        $this->mapApiRoutes($router, $namespace, $pathApi, '');
    }
}
