<?php

namespace App\Fields;

use App\Models\Sample;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class SampleIds extends Textarea
{
    public $showOnCreation = true;

    public $showOnUpdate = false;

    public $showOnIndex = false;

    public $showOnDetail = false;

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $sampleIds = array_filter(array_map('trim', explode("\n", $request[$requestAttribute])));
        $samples = Sample::where('sample_type_id', $request['sampleType'])
            ->with('sampleInformation')
            ->whereHas('sampleInformation', function ($query) use ($sampleIds) {
                $query->whereIn('sample_id', $sampleIds);
            })
            ->get();
        $class = get_class($model);
        $class::saved(function ($model) use ($samples) {
            $model->samples()->saveMany($samples);
        });
    }
}
