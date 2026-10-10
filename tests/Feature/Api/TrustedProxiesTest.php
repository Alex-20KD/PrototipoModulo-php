<?php

namespace Tests\Feature\Api;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Support\SecurityConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class TrustedProxiesTest extends TestCase
{
    use RefreshDatabase;

    private array $originalEnv = [];

    protected function setUp(): void
    {
        $this->backupEnv('TRUSTED_PROXIES');
        $this->backupEnv('CORS_ALLOWED_ORIGINS');

        $testName = $this->name();

        if (str_contains($testName, 'ignores_forwarded_for_without_trusted_proxies_configured')) {
            $this->setEnv('TRUSTED_PROXIES', '');
        } else {
            $this->setEnv('TRUSTED_PROXIES', '10.0.0.1');
        }

        parent::setUp();

        Route::get('/api/ping-ip', function (Request $request) {
            return response()->json(['ip' => $request->ip()]);
        });
    }

    protected function tearDown(): void
    {
        $this->restoreEnv('TRUSTED_PROXIES');
        $this->restoreEnv('CORS_ALLOWED_ORIGINS');

        parent::tearDown();
    }

    private function backupEnv(string $key): void
    {
        $this->originalEnv[$key] = [
            'putenv' => getenv($key),
            'env' => array_key_exists($key, $_ENV) ? $_ENV[$key] : false,
            'server' => array_key_exists($key, $_SERVER) ? $_SERVER[$key] : false,
        ];
    }

    private function setEnv(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;

        if (class_exists(Env::class)) {
            \Closure::bind(function () {
                Env::$repository = null;
            }, null, Env::class)();
        }
    }

    private function restoreEnv(string $key): void
    {
        $backup = $this->originalEnv[$key] ?? null;
        if (! $backup) {
            return;
        }

        if ($backup['putenv'] === false) {
            putenv($key);
        } else {
            putenv("{$key}={$backup['putenv']}");
        }

        if ($backup['env'] === false) {
            unset($_ENV[$key]);
        } else {
            $_ENV[$key] = $backup['env'];
        }

        if ($backup['server'] === false) {
            unset($_SERVER[$key]);
        } else {
            $_SERVER[$key] = $backup['server'];
        }

        if (class_exists(Env::class)) {
            \Closure::bind(function () {
                Env::$repository = null;
            }, null, Env::class)();
        }
    }

    public function test_trusted_proxies_rejects_wildcard(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('TRUSTED_PROXIES no admite comodines');

        SecurityConfig::parseAndValidate('*', 'TRUSTED_PROXIES');
    }

    public function test_cors_origins_rejects_wildcard(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('CORS_ALLOWED_ORIGINS no admite comodines');

        SecurityConfig::parseAndValidate('**', 'CORS_ALLOWED_ORIGINS');
    }

    public function test_resolves_client_ip_if_proxy_is_trusted(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.7',
        ])->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.1',
        ])->getJson('/api/ping-ip');

        $response->assertStatus(200)
            ->assertJsonPath('ip', '203.0.113.7');
    }

    public function test_ignores_forwarded_for_if_proxy_is_untrusted(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.7',
        ])->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.2',
        ])->getJson('/api/ping-ip');

        $response->assertStatus(200)
            ->assertJsonPath('ip', '10.0.0.2');
    }

    public function test_ignores_forwarded_for_without_trusted_proxies_configured(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.7',
        ])->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.1',
        ])->getJson('/api/ping-ip');

        $response->assertStatus(200)
            ->assertJsonPath('ip', '10.0.0.1');
    }

    public function test_rate_limiting_is_separate_for_clients_behind_trusted_proxy(): void
    {
        Staff::create([
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => StaffRole::Reception,
        ]);

        RateLimiter::clear('test@example.com|203.0.113.1');
        RateLimiter::clear('test@example.com|203.0.113.2');

        for ($i = 0; $i < 5; $i++) {
            $this->withHeaders([
                'X-Forwarded-For' => '203.0.113.1',
            ])->withServerVariables([
                'REMOTE_ADDR' => '10.0.0.1',
            ])->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrong',
            ]);
        }

        $response1 = $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.1',
        ])->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.1',
        ])->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong',
        ]);

        $response1->assertStatus(429);

        $response2 = $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.2',
        ])->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.1',
        ])->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong',
        ]);

        $response2->assertStatus(401);

        RateLimiter::clear('test@example.com|203.0.113.1');
        RateLimiter::clear('test@example.com|203.0.113.2');
    }

    public function test_cors_headers_with_allowed_origin(): void
    {
        $this->app['config']->set('cors.allowed_origins', ['http://192.168.10.2']);

        $response = $this->withHeaders([
            'Origin' => 'http://192.168.10.2',
        ])->optionsJson('/api/ping');

        $response->assertHeader('Access-Control-Allow-Origin', 'http://192.168.10.2');
    }

    public function test_cors_headers_missing_for_unallowed_origin(): void
    {
        $this->app['config']->set('cors.allowed_origins', ['http://192.168.10.2']);

        $response = $this->withHeaders([
            'Origin' => 'http://10.0.0.99',
        ])->optionsJson('/api/ping');

        if ($response->headers->has('Access-Control-Allow-Origin')) {
            $this->assertNotSame('http://10.0.0.99', $response->headers->get('Access-Control-Allow-Origin'));
        } else {
            $this->assertTrue(true);
        }
    }
}
