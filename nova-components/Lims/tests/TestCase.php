<?php

namespace Sparclex\Lims\Tests;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Sparclex\Lims\ToolServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);

        $this->withFactories(realpath(dirname(__DIR__).'/database/factories'));

    }

    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
        ];
    }
}
