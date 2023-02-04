<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\UserFeature;
use Lucid\Units\Controller;

class UserController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(UserFeature::class);
    }
}
