<?php

namespace App\Core\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Standardized API response formatting for all controllers.
 */
trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Success.', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function created(mixed $data = null, string $message = 'Created.', int $code = 201): JsonResponse
    {
        return $this->success($data, $message, $code);
    }

    protected function error(string $message = 'Error.', int $code = 400, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}
