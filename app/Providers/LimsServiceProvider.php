<?php

namespace App\Providers;

use App\Models\Sample;
use App\Observers\AutoStorageSaver;
use App\ResultHandlers\Rdml\ProcessorContract;
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
        Sample::observe(AutoStorageSaver::class);
        Nova::serving(function (ServingNova $event) {
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
        $this->app->bind(ProcessorContract::class, Manager::class);
    }
}
