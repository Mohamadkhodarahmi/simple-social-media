<?php

namespace App\Http\Responses;

class ApiResponses
{
    public static function success($data, $status = 200): array
    {
        return [
            'status' => $status,
            'response' => ['data' => $data],
        ];
    }

    public static function unauthenticated(): array
    {
        return [
            'status' => 401,
            'response' => ['message' => 'Unauthenticated'],
        ];
    }

    public static function forbidden(): array
    {
        return [
            'status' => 403,
            'response' => ['message' => 'This action is unauthorized.'],
        ];
    }

    public static function notFound(): array
    {
        return [
            'status' => 404,
            'response' => ['message' => 'Not Found'],
        ];
    }

    public static function validationError($errors): array
    {
        return [
            'status' => 422,
            'response' => [
                'message' => 'The given data was invalid.',
                'errors' => $errors,
            ],
        ];
    }
}
