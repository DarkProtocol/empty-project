<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\RegisterFeature;
use Lucid\Units\Controller;

class RegisterController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(RegisterFeature::class);
    }
}
