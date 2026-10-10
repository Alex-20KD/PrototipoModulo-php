<?php

use App\Exceptions\StorageUnavailableException;
use App\Http\Controllers\HealthController;
use App\Http\Middleware\CheckRole;
use App\Support\SecurityConfig;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // /health queda fuera de los grupos web y api: sin sesión (la BD no
        // debe sondearse con escrituras de sesión) y sin autenticación, para
        // que el balanceador (PC1) pueda consultarlo.
        then: function (): void {
            Route::get('/health', [HealthController::class, 'index'])->name('health');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: SecurityConfig::getTrustedProxies(),
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Decide cuándo renderizar JSON: prefijo api/* O cabecera Accept: application/json
        $isApiRequest = fn (Request $request): bool => $request->is('api/*') || $request->wantsJson();

        // Distingue una base de datos caída de un error real de SQL: solo lo
        // primero debe devolver 503 al balanceador, un bug de código sigue en 500.
        $isDatabaseConnectionFailure = function (PDOException $e): bool {
            $sqlState = (string) ($e->errorInfo[0] ?? $e->getCode());
            $driverCode = (int) ($e->errorInfo[1] ?? 0);
            $message = $e->getMessage();

            return str_starts_with($sqlState, '08')
                || in_array($driverCode, [2002, 2003, 2005, 2006, 1040, 2013], true)
                || str_contains($message, 'Connection refused')
                || str_contains($message, 'server has gone away')
                || str_contains($message, 'Lost connection')
                || str_contains($message, 'Too many connections')
                || str_contains($message, 'Unknown MySQL server host')
                || str_contains($message, 'getaddrinfo')
                || str_contains($message, 'php_network_getaddresses');
        };

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

        // 403 – no autorizado
        $exceptions->render(function (AccessDeniedHttpException|AuthorizationException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage() ?: 'No tiene permisos para acceder a este recurso',
                    'data' => null,
                ], 403);
            }
        });

        // 503 - almacenamiento no disponible (SFTP caído)
        $exceptions->render(function (StorageUnavailableException $e, Request $request) use ($isApiRequest) {
            Log::error('Storage Unavailable: '.$e->getMessage());
            if ($isApiRequest($request)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Servicio de almacenamiento no disponible temporalmente. Intente más tarde.',
                    'data' => null,
                ], 503);
            }
        });

        // 503 - base de datos caída (readiness) con el envoltorio uniforme
        $exceptions->render(function (PDOException $e, Request $request) use ($isApiRequest, $isDatabaseConnectionFailure) {
            if (! $isDatabaseConnectionFailure($e) || ! $isApiRequest($request)) {
                return null;
            }

            Log::error('Database unavailable: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Servicio no disponible temporalmente',
                'data' => null,
            ], 503);
        });
    })->create();
