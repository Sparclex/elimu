<?php

namespace Sparclex\Lims;

use App\Policies\StoragePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Sparclex\Lims\Models\Sample;
use Sparclex\Lims\Nova\Storage;
use Sparclex\Lims\Observers\AutoStorageSaver;
use Sparclex\Lims\Rules\StorageSizeExists;

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
        $this->app->booted(
            function () {
                $this->routes();
            });
        Validator::extend('existing_storage', StorageSizeExists::class . "@validate");
        Sample::observe(AutoStorageSaver::class);
        Nova::serving(
            function (ServingNova $event) {
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

        Route::middleware(['nova'])->prefix('nova-vendor/sparclex/Lims')->namespace(
            'Sparclex\\Lims\\Http\\Controllers')->group(__DIR__.'/../routes/api.php');
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
