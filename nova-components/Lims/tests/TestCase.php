<?php

namespace Sparclex\Lims\Tests;

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Orchestra\Testbench\TestCase as Orchestra;
use Sparclex\Lims\ToolServiceProvider;

abstract class TestCase extends Orchestra
{

    protected function setUp()
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);

        $this->withFactories(realpath(dirname(__DIR__).'/database/factories'));

    }
    protected function getEnvironmentSetUp($app) {

    }

    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
        ];
    }
}
