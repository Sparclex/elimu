<?php

namespace App\Providers;

use App\Models\SampleMutation;
use App\Observers\StorageGenerator;
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
        SampleMutation::observe(StorageGenerator::class);
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
