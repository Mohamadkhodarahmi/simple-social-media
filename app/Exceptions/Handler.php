<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception): JsonResponse
    {
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'message' => 'مدل موردنظر پیدا نشد.',
                'status' => 404,
            ], 404);
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json([
                'message' => 'لطفاً وارد شوید.',
                'status' => 401,
            ], 401);
        }


        $response = parent::render($request, $exception);
        if (!$response instanceof JsonResponse) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status' => $response->getStatusCode(),
            ], $response->getStatusCode());
        }

        return $response;
    }
}
