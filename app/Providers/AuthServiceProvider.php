<?php

namespace App\Providers;

use App\Models\Assay;
use App\Models\Experiment;
use App\Models\InputParameter;
use App\Models\Sample;
use App\Models\SampleData;
use App\Models\SampleInformation;
use App\Models\SampleType;
use App\Models\Storage;
use App\Models\Study;
use App\Policies\AssayPolicy;
use App\Policies\Authorization;
use App\Policies\ExperimentPolicy;
use App\Policies\InputParameterPolicy;
use App\Policies\SampleDataPolicy;
use App\Policies\SampleInformationPolicy;
use App\Policies\SamplePolicy;
use App\Policies\SampleTypePolicy;
use App\Policies\StoragePolicy;
use App\Policies\StudyPolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Assay::class => AssayPolicy::class,
        Experiment::class => ExperimentPolicy::class,
        InputParameter::class => InputParameterPolicy::class,
        SampleData::class => SampleDataPolicy::class,
        SampleInformation::class => SampleInformationPolicy::class,
        Sample::class => SamplePolicy::class,
        SampleType::class => SampleTypePolicy::class,
        Study::class => StudyPolicy::class,
        User::class => UserPolicy::class,
        Storage::class => StoragePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return;
        });
        Gate::define('change-role', function ($auth, $user) {
            return $auth->id !== $user->id
                && Authorization::isLabManager($auth);
        });
        Gate::define('select-study', function ($auth, $study) {
            return $auth->studies->contains($study);
        });
    }
}
