<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\LogoutFeature;
use Lucid\Units\Controller;

class LogoutController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(LogoutFeature::class);
    }
}
