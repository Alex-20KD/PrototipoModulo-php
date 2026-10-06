<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Decide cuándo renderizar JSON: prefijo api/* O cabecera Accept: application/json
        $isApiRequest = fn (Request $request): bool => $request->is('api/*') || $request->wantsJson();

        $exceptions->shouldRenderJsonWhen($isApiRequest);

        // 404 – modelo no encontrado
        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Recurso no encontrado',
                    'data' => null,
                ], 404);
            }
        });

        // 404 – ruta no encontrada
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ruta no encontrada',
                    'data' => null,
                ], 404);
            }
        });

        // 422 – error de validación
        $exceptions->render(function (ValidationException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Los datos proporcionados no son válidos',
                    'data' => $e->errors(),
                ], 422);
            }
        });

        // 401 – no autenticado
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No autenticado',
                    'data' => null,
                ], 401);
            }
        });
    })->create();
