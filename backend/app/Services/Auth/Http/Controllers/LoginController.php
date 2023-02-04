<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\LoginFeature;
use Lucid\Units\Controller;

class LoginController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(LoginFeature::class);
    }
}
