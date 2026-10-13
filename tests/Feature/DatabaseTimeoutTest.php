<?php

namespace Tests\Feature;

use PDO;
use Tests\TestCase;

class DatabaseTimeoutTest extends TestCase
{
    public function test_mysql_connection_exposes_configurable_timeout(): void
    {
        $options = config('database.connections.mysql.options');

        $this->assertArrayHasKey(PDO::ATTR_TIMEOUT, $options);
        $this->assertSame(5, $options[PDO::ATTR_TIMEOUT]);
    }
}
