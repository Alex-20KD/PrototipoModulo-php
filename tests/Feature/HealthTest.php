<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_is_public_and_reports_all_components_up(): void
    {
        config(['filesystems.disks.pc5.host' => 'sftp']);
        Storage::fake('pc5');

        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('message', 'Servicio operativo')
            ->assertJsonPath('data.components.database', 'up')
            ->assertJsonPath('data.components.pc5', 'up');

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_health_returns_503_when_database_is_down(): void
    {
        config(['filesystems.disks.pc5.host' => 'sftp']);
        Storage::fake('pc5');
        config([
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => 1,
        ]);
        DB::purge('mysql');

        $response = $this->getJson('/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', false)
            ->assertJsonPath('data.components.database', 'down')
            ->assertJsonPath('data.components.pc5', 'up');
    }

    public function test_health_returns_503_when_pc5_storage_is_down(): void
    {
        config([
            'filesystems.disks.pc5.host' => '127.0.0.1',
            'filesystems.disks.pc5.port' => 1,
        ]);

        $response = $this->getJson('/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', false)
            ->assertJsonPath('data.components.database', 'up')
            ->assertJsonPath('data.components.pc5', 'down');
    }

    public function test_health_reports_pc5_disabled_when_not_configured(): void
    {
        config(['filesystems.disks.pc5.host' => null]);

        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.components.database', 'up')
            ->assertJsonPath('data.components.pc5', 'disabled');
    }

    public function test_health_lives_at_root_and_not_under_api_prefix(): void
    {
        config(['filesystems.disks.pc5.host' => null]);

        $this->getJson('/health')->assertStatus(200);
        $this->getJson('/api/health')->assertStatus(404);
    }

    public function test_api_returns_uniform_503_when_database_is_down(): void
    {
        config([
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => 1,
        ]);
        DB::purge('mysql');

        $response = $this->postJson('/api/auth/login', [
            'email' => 'medico@medtriaje.test',
            'password' => 'password123',
        ]);

        $response->assertStatus(503)
            ->assertExactJson([
                'status' => false,
                'message' => 'Servicio no disponible temporalmente',
                'data' => null,
            ]);
    }

    public function test_sql_error_not_caused_by_connection_still_returns_500(): void
    {
        Route::get('/sql-bug', fn () => DB::select('select 1 from tabla_inexistente'));

        $this->getJson('/sql-bug')->assertStatus(500);
    }
}
