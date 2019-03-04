<?php

namespace App\Fields;

use App\Models\Sample;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class SampleIds extends Textarea
{
    public $showOnIndex = false;

    public $showOnDetail = false;

    public $pivotFields = [];

    public function pivot($fields = [])
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $this->pivotFields = $fields;

        return $this;
    }

    public function resolve($resource, $attribute = null)
    {
        $samples = $resource->samples;

        $text = "";
        foreach ($samples as $sample) {
            $text .= $sample->sample_id;

            foreach ($this->pivotFields as $field) {
                $text .= "," . $sample->pivot->{$field};
            }

            $text .= "\n";
        }

        $this->value = $text;
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $input = array_filter(array_map('trim', explode("\n", $request[$requestAttribute])));

        [$sampleIds, $pivots] = $this->extractPivotFields($input);

        $samples = Sample::whereIn('sample_id', $sampleIds)->get();


        $data = [];
        if ($pivots) {
            foreach ($samples as $sample) {
                $data[$sample->id] = $pivots[$sample->sample_id];
            }
        } else {
            $data = $samples->pluck('id');
        }

        $class = get_class($model);
        $class::saved(function ($model) use ($data) {
            $model->samples()->sync($data);
        });
    }

    protected function extractPivotFields($input)
    {
        if (count($this->pivotFields) == 0) {
            return [$input, false];
        }

        $sampleIds = [];
        $pivots = [];
        foreach ($input as $line) {
            $sampleIdsWithPivot = explode(',', $line, count($this->pivotFields) + 1);
            $sampleIds[] = trim($sampleIdsWithPivot[0]);
            $pivot = [];
            foreach ($this->pivotFields as $key => $field) {
                if (isset($sampleIdsWithPivot[$key + 1]) && trim($sampleIdsWithPivot[$key + 1])) {
                    $pivot[$field] = trim($sampleIdsWithPivot[$key + 1]);
                }
            }
            $pivots[$sampleIdsWithPivot[0]] = $pivot;
        }

        return [$sampleIds, $pivots];
    }
}
