<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class PingTest extends TestCase
{
    /**
     * Verificar que el endpoint de ping responda con el JSON estándar.
     */
    public function test_ping_endpoint_returns_standard_json_response(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
            ->assertExactJson([
                'status' => true,
                'message' => 'pong',
                'data' => null,
            ]);
    }

    /**
     * Verificar que una ruta inexistente de API devuelva error 404 en formato JSON estándar.
     */
    public function test_missing_api_route_returns_standard_json_404(): void
    {
        $response = $this->getJson('/api/ruta-inexistente');

        $response->assertStatus(404)
            ->assertJsonStructure(['status', 'message', 'data'])
            ->assertJsonPath('status', false);
    }
}
