<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->ajax() || $request->acceptsJson()) {
            Log::error($exception);

            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'errors' => [
                        'code' => 404,
                        'message' => 'Resource not found',
                    ],
                ], 404);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'errors' => [
                        'code' => 404,
                        'message' => 'Page not found',
                    ],
                ], 404);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'errors' => [
                        'code' => 403,
                        'message' => 'Method not allowed',
                    ],
                ], 403);
            }

            if ($exception instanceof PostTooLargeException) {
                return response()->json([
                    'errors' => [
                        'code' => 400,
                        'message' => 'Files size is too large',
                    ],
                ], 400);
            }

            $json = [
                'errors' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ],
            ];
            if (auth()->check()) {
                return response()->json($json, 400);
            } else {
                return response()->json($json, 401);
            }
        }

        return parent::render($request, $exception);
    }
}
