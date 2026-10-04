<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        if ($app['config']->get('database.default') !== 'mysql'
            || $app['config']->get('database.connections.mysql.database') !== 'medtriaje_test'
            || $app['config']->get('database.connections.mysql.url')) {
            throw new RuntimeException('Los tests solo pueden usar la base MySQL medtriaje_test.');
        }

        return $app;
    }
}
