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

            if ($statusCode >= 400) {
                return response()->json([
                    'message' => $originalData['message'] ?? Response::$statusTexts[$statusCode],
                    'status' => $statusCode,
                    'errors' => $originalData['errors'] ?? [],
                ], $statusCode);
            }

            $data = $originalData['response']['data'] ?? $originalData;
            $responseStatus = $originalData['status'] ?? $statusCode;

            $finalStatus = (is_numeric($responseStatus) && $responseStatus >= 100 && $responseStatus <= 599) ? $responseStatus : 200;

            return response()->json([
                'data' => $data,
                'status' => 'success',
            ], $finalStatus);
        }

        return $response;
    }
}
