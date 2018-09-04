<?php

namespace Sparclex\Lims;

use App\Policies\StoragePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Sparclex\Lims\Models\Sample;
use Sparclex\Lims\Nova\Storage;
use Sparclex\Lims\Observers\AutoStorageSaver;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Storage::class => StoragePolicy::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Lims');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->registerPolicies();
        $this->app->booted(function () {
            $this->routes();
        });
        Sample::observe(AutoStorageSaver::class);
        Nova::serving(function (ServingNova $event) {
        });
    }

    private function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
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

        Route::middleware(['nova'])->prefix('nova-vendor/sparclex/Lims')->group(__DIR__.'/../routes/api.php');
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
