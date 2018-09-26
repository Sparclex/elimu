<?php

namespace App\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;

class GenerateReport extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $onlyOnDetail = true;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields $fields
     * @param  \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        $availableAssays = [];
        dump($this->sample);
        foreach ($this->sample->experiments as $experiment) {
            $availableAssays[] = $experiment->assay;
        }
        dump($availableAssays);
        return [
            Select::make('Field')->options([])
        ];
    }
}
