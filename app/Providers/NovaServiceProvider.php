<?php

namespace App\Providers;

use App\Nova\User;
use App\Nova\Assay;
use App\Nova\Study;
use App\Nova\Result;
use App\Nova\Sample;
use App\Nova\Reagent;
use App\Nova\Storage;
use Laravel\Nova\Nova;
use App\Nova\Experiment;
use App\Nova\ResultData;
use App\Nova\SampleData;
use App\Nova\SampleType;
use App\Nova\InputParameter;
use App\Nova\SampleInformation;
use App\Tools\SampleBatchImporter;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new \Spatie\BackupTool\BackupTool(),
        ];
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

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
        ];
    }

    protected function resources()
    {
        Nova::resources([
            User::class,
            Study::class,
            SampleInformation::class,
            Sample::class,
            InputParameter::class,
            Experiment::class,
            ResultData::class,
            Result::class,
            Reagent::class,
            Assay::class,
            SampleType::class,
            Storage::class,
        ]);
    }
}
