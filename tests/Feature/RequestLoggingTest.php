<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class RequestLoggingTest extends TestCase
{
    public function test_response_includes_request_id_and_instance_id_headers(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200);
        $this->assertTrue($response->headers->has('X-Request-ID'));
        $this->assertTrue($response->headers->has('X-Instance-ID'));

        $requestId = $response->headers->get('X-Request-ID');
        $this->assertTrue(Str::isUuid($requestId));

        $expectedInstance = config('app.instance_id') ?? 'app';
        $this->assertSame($expectedInstance, $response->headers->get('X-Instance-ID'));
    }

    public function test_incoming_request_id_is_preserved_in_response(): void
    {
        $customId = 'trace-id-abc-123';

        $response = $this->withHeaders([
            'X-Request-ID' => $customId,
        ])->getJson('/api/ping');

        $response->assertStatus(200);
        $this->assertSame($customId, $response->headers->get('X-Request-ID'));
    }

    public function test_instance_id_reflects_configuration(): void
    {
        config(['app.instance_id' => 'app2']);

        $response = $this->getJson('/api/ping');

        $response->assertStatus(200);
        $this->assertSame('app2', $response->headers->get('X-Instance-ID'));
    }

    public function test_request_and_instance_ids_are_available_in_context(): void
    {
        Route::get('/api/test-context-verification', function () {
            return response()->json([
                'request_id' => class_exists(Context::class) ? Context::get('request_id') : null,
                'instance_id' => class_exists(Context::class) ? Context::get('instance_id') : null,
            ]);
        });

        $customId = 'trace-context-999';

        $response = $this->withHeaders([
            'X-Request-ID' => $customId,
        ])->getJson('/api/test-context-verification');

        $response->assertStatus(200);
        $this->assertSame($customId, $response->json('request_id'));
        $this->assertSame(config('app.instance_id') ?? 'app', $response->json('instance_id'));
    }
}
