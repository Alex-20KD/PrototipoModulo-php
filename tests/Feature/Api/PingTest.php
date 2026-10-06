<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class PingTest extends TestCase
{
    /**
     * GET /api/ping devuelve 200 con el envoltorio estándar.
     */
    public function test_ping_returns_200_with_standard_wrapper(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
            ->assertExactJson([
                'status'  => true,
                'message' => 'pong',
                'data'    => null,
            ]);
    }

    /**
     * El campo status es booleano true, no el string "true".
     */
    public function test_ping_status_field_is_boolean_true(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('message', 'pong')
            ->assertJsonPath('data', null);
    }

    /**
     * Una ruta de API inexistente devuelve 404 con el envoltorio estándar (no HTML).
     */
    public function test_unknown_api_route_returns_404_json_wrapper(): void
    {
        $response = $this->getJson('/api/esta-ruta-no-existe');

        $response->assertStatus(404)
            ->assertJsonStructure(['status', 'message', 'data'])
            ->assertJsonPath('status', false);
    }
}
