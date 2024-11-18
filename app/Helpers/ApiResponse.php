<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function response($type = 'success', $message = 'Request successful', $content = null, $status = 200): JsonResponse
    {
        switch ($type) {
            case 'success':
                $response = [
                    'success' => true,
                    'message' => $message,
                    'data' => $content,
                ];
                break;

            case 'error':
                $response = [
                    'success' => false,
                    'message' => $message,
                    'errors' => $content,
                ];
                $status = $status ?: 400;
                break;

            default:
                $response = [
                    'success' => false,
                    'message' => 'Invalid response type',
                    'errors' => ['Invalid response type specified'],
                ];
                $status = 500;
                break;
        }

        return response()->json($response)->setStatusCode($status);
    }

}
