<?php

namespace App\Http\Responses;

class ApiResponses
{
    public static function success($data, $status = 200,$message=null): array
    {
        if (!is_numeric($status) || $status < 100 || $status > 599) {
            $status = 200;
        }
        return [
            'status' => $status,
            'message' => $message ?? 'عملیات موفقیت آمیز',
            'response' => ['data' => $data],
        ];
    }

    public static function error($message, $status, $errors = []): array
    {
        if (!is_numeric($status) || $status < 100 || $status > 599) {
            $status = 500;
        }
        return [
            'status' => $status,
            'response' => [
                'message' => $message,
                'errors' => $errors,
            ],
        ];
    }

    public static function validationError($data, $status = 422): array
    {
        return [
            'status' => false,
            'errors' => $data
        ];
    }

    public static function forbidden($data = null, $status = 403): array
    {
        return [
            'status' => false,
            'message' => $data ?? 'دسترسی غیرمجاز'
        ];
    }

    public static function unauthenticated($data = null, $status = 401): array
    {
        return [
            'status' => false,
            'message' => $data ?? 'احراز هویت نشده‌اید'
        ];
    }

    public static function notFound($data = null, $status = 404): array
    {
        return [
            'status' => false,
            'message' => $data ?? 'منبع مورد نظر یافت نشد'
        ];
    }
}
