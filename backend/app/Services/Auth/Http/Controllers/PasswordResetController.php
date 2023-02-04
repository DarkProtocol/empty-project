<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\PasswordResetFeature;
use Lucid\Units\Controller;

class PasswordResetController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(PasswordResetFeature::class);
    }
}
