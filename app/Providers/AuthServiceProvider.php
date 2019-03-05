<?php

namespace App\Providers;

use App\Models\Assay;
use App\Models\Experiment;
use App\Models\InputParameter;
use App\Models\Institution;
use App\Models\Laboratory;
use App\Models\Person;
use App\Models\Result;
use App\Models\ResultData;
use App\Models\Sample;
use App\Models\SampleData;
use App\Models\SampleMutation;
use App\Models\SampleType;
use App\Models\Storage;
use App\Models\Study;
use App\Policies\AssayPolicy;
use App\Policies\AuditPolicy;
use App\Policies\Authorization;
use App\Policies\ExperimentPolicy;
use App\Policies\InstitutionPolicy;
use App\Policies\LaboratoryPolicy;
use App\Policies\PersonPolicy;
use App\Policies\ResultDataPolicy;
use App\Policies\ResultPolicy;
use App\Policies\SampleMutationPolicy;
use App\Policies\SamplePolicy;
use App\Policies\SampleTypePolicy;
use App\Policies\StoragePolicy;
use App\Policies\StudyPolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use OwenIt\Auditing\Models\Audit;

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
        Sample::class => SamplePolicy::class,
        Institution::class => InstitutionPolicy::class,
        SampleType::class => SampleTypePolicy::class,
        Study::class => StudyPolicy::class,
        User::class => UserPolicy::class,
        Storage::class => StoragePolicy::class,
        Result::class => ResultPolicy::class,
        ResultData::class => ResultDataPolicy::class,
        Audit::class => AuditPolicy::class,
        Person::class => PersonPolicy::class,
        Laboratory::class => LaboratoryPolicy::class,
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
