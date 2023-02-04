<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\UserActivateFeature;
use Lucid\Units\Controller;

class UserActivateController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(UserActivateFeature::class);
    }
}
