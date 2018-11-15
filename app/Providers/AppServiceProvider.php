<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use App\Providers\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('standardDeviation', function ($key = null) {
            $data = $key ? $this->pluck($key)->values() : $this->values();
            $variance = 0.0;
            $average = $data->avg();

            foreach ($data as $element) {
                // sum of squares of differences between
                // all numbers and means.
                $variance += pow(($element - $average), 2);
            }
            return (float) sqrt($variance/$data->count());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(TelescopeServiceProvider::class);
    }
}
