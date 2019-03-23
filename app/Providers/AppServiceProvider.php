<?php

namespace App\Providers;

use App\CustomIndexRequest;
use App\CustomRequest;
use App\Models\SampleMutation;
use App\Observers\StorageGenerator;
use App\Rules\StorageSizeExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\Nova;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ResourceIndexRequest::class, CustomIndexRequest::class);
        SampleMutation::observe(StorageGenerator::class);
        Validator::extend('existing_storage', StorageSizeExists::class."@validate");
        Nova::serving(
            function (ServingNova $event) {
                Nova::script('custom-tools', public_path('js/custom-tools.js'));
            }
        );

        Nova::userTimezone(
            function (Request $request) {
                return $request->user()->timezone;
            }
        );
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
