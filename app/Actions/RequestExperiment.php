<?php

namespace App\Actions;

use App\Models\Assay;
use App\Models\SampleMutation;
use Laravel\Nova\Nova;
use App\Models\Reagent;
use App\Models\Experiment;
use Illuminate\Bus\Queueable;
use App\Fields\ReagentsFields;
use Illuminate\Support\Carbon;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Textarea;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

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
        if ($models->pluck('study_id')->unique()->count() > 1) {
            return Action::danger('Only select samples from the same study');
        }
        $data = $fields->form;
        if (!isset($data['reagent'])) {
            $reagent = new Reagent();
            $reagent->lot = $data['lot'];
            $reagent->name = $data['name'];
            $reagent->expires_at = $data['expires_at'];
            $reagent->assay_id = $data['assay'];
            $reagent->save();
            $reagentId = $reagent->id;
        } else {
            $reagentId = $data['reagent'];
        }
        $experiment = new Experiment();
        $experiment->requester_id = Auth::id();
        $experiment->requested_at = Carbon::now();
        $experiment->reagent_id = $reagentId;
        $experiment->study_id = $models->first()->sampleInformation->study_id;
        $experiment->save();
        $experiment->samples()->saveMany($models);

        return Action::redirect(Nova::path() . "/resources/experiments/" . $experiment->id);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            ReagentsFields::make(Assay::all()),
        ];
    }
}
