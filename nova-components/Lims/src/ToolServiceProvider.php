<?php

namespace Sparclex\Lims;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Sparclex\Lims\Http\Middleware\Authorize;
use Sparclex\Lims\Nova\Person;
use Sparclex\Lims\Nova\ProcessingLog;
use Sparclex\Lims\Nova\Result;
use Sparclex\Lims\Nova\SampleType;
use Sparclex\Lims\Nova\Storage;
use Sparclex\Lims\Nova\StorageSize;
use Sparclex\Lims\Nova\Study;
use Sparclex\Lims\Nova\Test;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Lims');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
                ->prefix('nova-vendor/sparclex/Lims')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
