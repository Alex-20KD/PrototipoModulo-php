<?php

namespace Tests\Feature;

use PDO;
use Tests\TestCase;

class DatabaseTimeoutTest extends TestCase
{
    public function createApplication()
    {
        putenv('DB_TIMEOUT=3');
        $_ENV['DB_TIMEOUT'] = '3';
        $_SERVER['DB_TIMEOUT'] = '3';

        return parent::createApplication();
    }

    protected function tearDown(): void
    {
        putenv('DB_TIMEOUT');
        unset($_ENV['DB_TIMEOUT'], $_SERVER['DB_TIMEOUT']);

        parent::tearDown();
    }

    public function test_mysql_connection_exposes_configurable_timeout(): void
    {
        $options = config('database.connections.mysql.options');

        $this->assertArrayHasKey(PDO::ATTR_TIMEOUT, $options);
        $this->assertSame(3, $options[PDO::ATTR_TIMEOUT]);
        $this->assertSame('3', ini_get('mysqlnd.net_read_timeout'));
    }
}
