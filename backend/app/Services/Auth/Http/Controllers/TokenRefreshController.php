<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\TokenRefreshFeature;
use Lucid\Units\Controller;

class TokenRefreshController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(TokenRefreshFeature::class);
    }
}
