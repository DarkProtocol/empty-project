<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\PasswordResetCompleteFeature;
use Lucid\Units\Controller;

class PasswordResetCompleteController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(PasswordResetCompleteFeature::class);
    }
}
