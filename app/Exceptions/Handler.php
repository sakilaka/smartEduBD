<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (!in_array($e->getCode(), [400, 401, 404, 422])) {
                Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                ]);
            }

            return false;
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 404);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized.'
                    ], 401);
                }

                if ($e->getCode() == 400) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 400);
                }

                $code = $e->getCode();
                $status = is_int($code) && $code >= 100 ? $code : 500;

                return response()->json([
                    'success' => false,
                    'message' => $this->errorMessage($e),
                ], $status);
            }
        });
    }

    /**
     * Get error message.
     *
     * @param Throwable $e
     * @return string
     */
    public function errorMessage(Throwable $e)
    {
        return config('app.debug') ? $e->getMessage() : 'An error occurred.';
    }

    /**
     * unauthenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $guard = array($exception->guards(), 0);

        switch ($guard[0][0]) {
            case 'admin':
                $login = 'admin.loginme';
                break;

            default:
                $login = 'login';
                break;
        }

        return redirect()->guest(route($login));
    }
}
