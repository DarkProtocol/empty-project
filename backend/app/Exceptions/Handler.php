<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\UnauthorizedException;
use App\Common\Http\Exceptions\NotAllowedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ApiException::class,
        UnauthorizedException::class,
        NotAllowedException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                app('sentry')->captureException($e);
            }
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json(
                [
                    'message' => __('validation.invalid-data'),
                    'errors' => $e->validator->getMessageBag()
                ],
                422
            );
        }

        return parent::render($request, $e);
    }

    /**
     * Render the given HttpException.
     *
     * @param HttpExceptionInterface $e
     * @return Response
     */
    protected function renderHttpException(HttpExceptionInterface $e): Response
    {
        return response()->json($e->getMessage(), $e->getStatusCode(), $e->getHeaders());
    }
}
