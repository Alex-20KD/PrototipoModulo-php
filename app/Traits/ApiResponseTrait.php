<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Respuesta de éxito genérica.
     */
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Respuesta 200 – recurso devuelto.
     */
    protected function ok(mixed $data = null, string $message = 'OK'): JsonResponse
    {
        return $this->success($data, $message, 200);
    }

    /**
     * Respuesta 201 – recurso creado.
     */
    protected function created(mixed $data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Respuesta de error genérica.
     */
    protected function error(string $message = 'Error', mixed $data = null, int $status = 500): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Respuesta 404 – recurso no encontrado.
     */
    protected function notFound(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->error($message, null, 404);
    }

    /**
     * Respuesta 422 – error de validación.
     *
     * @param  array<string, mixed>|null  $errors
     */
    protected function validationError(string $message = 'Los datos proporcionados no son válidos', ?array $errors = null): JsonResponse
    {
        return $this->error($message, $errors, 422);
    }

    /**
     * Respuesta 401 – no autenticado.
     */
    protected function unauthorized(string $message = 'No autenticado'): JsonResponse
    {
        return $this->error($message, null, 401);
    }

    /**
     * Respuesta 403 – sin permiso.
     */
    protected function forbidden(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->error($message, null, 403);
    }
}
