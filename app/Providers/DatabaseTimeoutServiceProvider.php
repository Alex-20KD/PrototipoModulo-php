<?php

namespace App\Providers;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\MySqlConnector;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;
use PDO;

class DatabaseTimeoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * Configura el timeout de lectura de red en mysqlnd y optimiza la reconexión de BD.
     *
     * NOTA: Toca internals de Laravel; revisar tras `composer update`.
     */
    public function register(): void
    {
        // 1. Configura el timeout de lectura a nivel de socket en mysqlnd.
        // Se lee de las opciones PDO de la configuración de MySQL o de la variable DB_TIMEOUT (3 s por defecto).
        // Si el valor es 0 (desactivado) o inválido, no se aplica.
        $options = config('database.connections.mysql.options', []);
        $timeout = $options[PDO::ATTR_TIMEOUT] ?? env('DB_TIMEOUT', 3);

        $timeout = filter_var($timeout, FILTER_VALIDATE_INT);
        if ($timeout !== false && $timeout > 0) {
            ini_set('mysqlnd.net_read_timeout', (string) $timeout);
        }

        // 2. Reemplazo de createConnection en el conector MySQL:
        // Por qué se reemplaza createConnection del conector: Laravel reintenta la conexión al fallar
        // y eso multiplica el timeout (con la BD sin respuesta la espera se multiplicaba por 4),
        // así que se hace un solo intento.
        $this->app->bind('db.connector.mysql', function () {
            return new class extends MySqlConnector
            {
                public function createConnection($dsn, array $config, array $options)
                {
                    return $this->createPdoConnection($dsn, $config['username'] ?? null, $config['password'] ?? null, $options);
                }
            };
        });

        // 3. Resolución personalizada de MySqlConnection:
        // Por qué tryAgainIfCausedByLostConnection solo reconecta si $this->pdo instanceof PDO:
        // si todavía no hay conexión establecida (PDO perezoso o inexistente) un reintento volvería a esperar
        // el timeout completo; solo tiene sentido reconectar cuando había una conexión viva que se perdió.
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new class($connection, $database, $prefix, $config) extends MySqlConnection
            {
                protected function tryAgainIfCausedByLostConnection(QueryException $e, $query, $bindings, Closure $callback)
                {
                    if ($this->pdo instanceof PDO && $this->causedByLostConnection($e->getPrevious())) {
                        $this->reconnect();

                        return $this->runQueryCallback($query, $bindings, $callback);
                    }

                    throw $e;
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
