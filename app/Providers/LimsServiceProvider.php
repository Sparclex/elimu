<?php

namespace App\Providers;

use App\Models\Sample;
use App\Observers\AutoStorageSaver;
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
        Validator::extend('existing_storage', StorageSizeExists::class . "@validate");
        Nova::serving(function (ServingNova $event) {
            Sample::observe(AutoStorageSaver::class);
            Nova::script('custom-tools', public_path('js/custom-tools.js'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
