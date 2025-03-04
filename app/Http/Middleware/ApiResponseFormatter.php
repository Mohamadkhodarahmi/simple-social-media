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
                    'errors' => $originalData['errors'] ?? [],
                ], $statusCode);
            }

            return response()->json([
                'data' => $originalData,
                'status' => 'success',
            ], $statusCode);
        }

        return $response;
    }
}
