<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Readiness de la instancia: estado de cada componente dependiente (BD y PC5).
     * Pensado para el health check del balanceador (PC1): público y sin sesión.
     */
    public function index(): JsonResponse
    {
        $components = [
            'database' => $this->checkDatabase(),
            'pc5' => $this->checkStorage(),
        ];

        $healthy = ! in_array('down', $components, true);

        $response = $healthy
            ? $this->ok('Servicio operativo', ['components' => $components])
            : $this->error('Algunos componentes no están disponibles', 503, ['components' => $components]);

        return $response->header('Cache-Control', 'no-store');
    }

    /**
     * Round-trip real contra la base de datos (getPdo() devolvería el handle
     * cacheado sin contactar al servidor y reportaría "up" con la BD caída).
     */
    protected function checkDatabase(): string
    {
        try {
            DB::select('select 1 as ok');

            return 'up';
        } catch (Throwable $e) {
            Log::warning('Health check: base de datos no disponible: '.$e->getMessage());

            return 'down';
        }
    }

    /**
     * Comprueba conectividad y credenciales del disco SFTP de PC5 (stat en
     * "reports") reutilizando la configuración de filesystems. No verifica
     * permiso de escritura ni la presencia del directorio.
     */
    protected function checkStorage(): string
    {
        if (blank(config('filesystems.disks.pc5.host'))) {
            return 'disabled';
        }

        try {
            Storage::disk('pc5')->exists('reports');

            return 'up';
        } catch (Throwable $e) {
            Log::warning('Health check: almacenamiento PC5 no disponible: '.$e->getMessage());

            return 'down';
        }
    }
}
