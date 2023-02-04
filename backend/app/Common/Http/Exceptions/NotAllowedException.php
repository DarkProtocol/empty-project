<?php

declare(strict_types=1);

namespace App\Common\Http\Exceptions;

use JetBrains\PhpStorm\Pure;
use Throwable;

class NotAllowedException extends ApiException
{
    #[Pure]
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(null, __('common.not-allowed'), 403, $previous);
    }
}
