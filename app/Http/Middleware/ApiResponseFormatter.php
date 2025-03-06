<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseFormatter
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $originalData = $response->getData(true);
            $statusCode = $response->getStatusCode();

            // If it's an error response
            if ($statusCode >= 400) {
                return response()->json([
                    'message' => $originalData['message'] ?? Response::$statusTexts[$statusCode],
                    'status' => $statusCode,
                    'errors' => $originalData['errors'] ?? [],
                ], $statusCode);
            }


            $data = $originalData['response']['data'] ?? ($originalData['data'] ?? $originalData);
            $responseStatus = $originalData['status'] ?? 'success';
            $message = $originalData['message'] ?? 'عملیات موفقیت آمیز';

            return response()->json([
                'data' => $data ?: ['message' => $message],
                'status' => $responseStatus,
                'message' => $message,
            ], $statusCode);
        }

        return $response;
    }

}
