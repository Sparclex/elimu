<?php

namespace App\Providers;

use App\Models\Experiment;
use App\Models\InputParameter;
use App\Models\Sample;
use App\Models\SampleInformation;
use App\Models\StorageSize;
use App\Observers\AutoStorageSaver;
use App\Observers\InsertSelectedStudy;
use App\Rules\StorageSizeExists;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Illuminate\Http\Request;

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
            Experiment::observe(InsertSelectedStudy::class);
            InputParameter::observe(InsertSelectedStudy::class);
            SampleInformation::observe(InsertSelectedStudy::class);
            StorageSize::observe(InsertSelectedStudy::class);
            Nova::script('custom-tools', public_path('js/custom-tools.js'));
        });

        Nova::userTimezone(function (Request $request) {
            return $request->user()->timezone;
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
