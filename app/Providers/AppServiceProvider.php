<?php

namespace App\Providers;

use App\Models\Experiment;
use App\Models\InputParameter;
use App\Models\SampleInformation;
use App\Models\StorageSize;
use App\Observers\InsertSelectedStudy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
