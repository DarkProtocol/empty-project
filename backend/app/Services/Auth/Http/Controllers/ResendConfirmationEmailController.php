<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\ResendConfirmationEmailFeature;
use Lucid\Units\Controller;

class ResendConfirmationEmailController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(ResendConfirmationEmailFeature::class);
    }
}
