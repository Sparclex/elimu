<?php

namespace App\Providers;

use App\Models\Sample;
use App\Observers\AutoStorageSaver;
use App\Manager;
use App\ResultHandlers\Rdml\ProcessorContract;
use App\Rules\StorageSizeExists;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class LimsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('existing_storage', StorageSizeExists::class."@validate");
        Sample::observe(AutoStorageSaver::class);
        Nova::serving(function (ServingNova $event) {
            Nova::script('result-field', public_path('tools/result-field/js/field.js'));
            Nova::script('status-field', public_path('tools/status-field/js/field.js'));
            Nova::script('data-field', public_path('tools/data-field/js/field.js'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProcessorContract::class, Manager::class);
    }
}
