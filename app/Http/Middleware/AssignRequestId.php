<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID');

        if (blank($requestId)) {
            $requestId = (string) Str::uuid();
            $request->headers->set('X-Request-ID', $requestId);
        }

        $instanceId = (string) (config('app.instance_id') ?? env('INSTANCE_ID', 'app'));

        // Añadir el ID de petición y de instancia al contexto del log para trazabilidad
        Log::withContext([
            'request_id' => $requestId,
            'instance_id' => $instanceId,
        ]);

        if (class_exists(Context::class)) {
            Context::add('request_id', $requestId);
            Context::add('instance_id', $instanceId);
        }

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('X-Instance-ID', $instanceId);

        return $response;
    }
}
