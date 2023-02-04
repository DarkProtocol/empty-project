<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\LoginAsFeature;
use Lucid\Units\Controller;

class LoginAsController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(LoginAsFeature::class);
    }
}
