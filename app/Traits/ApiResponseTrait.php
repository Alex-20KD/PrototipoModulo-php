<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    public function ok(string $message = 'Success', mixed $data = null): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public function created(string $message = 'Created', mixed $data = null): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    public function error(string $message = 'Error', int $code = 400, mixed $data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function notFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    public function validationError(string $message = 'Validation Error', mixed $data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], 422);
    }

    public function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401);
    }

    public function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }
}
