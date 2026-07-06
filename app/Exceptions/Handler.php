<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        JobException::class,
        UnauthorizedException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Purpose: performs the report operation in Handler.
     *
     * Action: encapsulates one logic step so callers can use the result without duplicating details.
     *
     * Report or log an exception.
     *
     * @param  \Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Purpose: performs the render operation in Handler.
     *
     * Action: encapsulates one logic step so callers can use the result without duplicating details.
     *
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable $exception
     * @return \Illuminate\Http\Response
     */
    public function render(mixed $request, Throwable $exception): Response
    {
        if ($exception instanceof TokenMismatchException) {
            if ($request->ajax()) {
                return response()->json([
                    'dismiss' => __('Session expired due to inactivity. Please reload page'),
                ]);
            } else {
                return redirect()->back()->with(['dismiss' => __('Session expired due to inactivity. Please try again')]);
            }
        } elseif ($exception instanceof UnauthorizedException) {
            if ($request->is('api/*')) {
                return response()->json([
                    SERVICE_RESPONSE_STATUS => 'auth',
                    SERVICE_RESPONSE_MESSAGE => Str::title(str_replace('_', ' ', $exception->getMessage()))
                ], $exception->getCode());
            } else {
                return response()->view('errors.' . $exception->getMessage(), [], 401);
            }
        } elseif (env('APP_ENV') == 'production' && !$exception instanceof ValidationException) {
            return response()->view('errors.404');
        }

        return parent::render($request, $exception);
    }
}
