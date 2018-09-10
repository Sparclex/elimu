<?php

namespace App\Actions;

use App\Models\Assay;
use App\Models\Experiment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Nova;

class RequestExperiment extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields $fields
     * @param  \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $experiment = new Experiment();
        $experiment->requester_id = Auth::id();
        $experiment->requested_at = Carbon::now();
        $experiment->assay_id = $fields->assay;
        $experiment->save();
        $experiment->samples()->saveMany($models);
        Action::redirect(Nova::path(). "/resources/experiments/".$experiment->id);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {

        return [
            Select::make('Assay')->options(Assay::pluck('name', 'id'))->rules('required', 'exists:assays,id'),
        ];
    }
}
