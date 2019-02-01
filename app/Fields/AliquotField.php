<?php

namespace App\Fields;

use App\Models\StorageSize;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class AliquotField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'aliquot-field';

    public $showOnCreation = true;

    public $showOnUpdate = true;

    public $showOnIndex = false;

    public $showOnDetail = false;

    public function getRules(NovaRequest $request)
    {
        $sampleTypeAttribute = $this->attribute . '.*.sampleType';
        $quantityAttribute = $this->attribute . '.*.quantity';

        return array_merge_recursive(parent::getRules($request), [
            $sampleTypeAttribute => 'required_if:' . $quantityAttribute . '|exists:sampleTypes,id',
            $quantityAttribute => 'nullable|numeric|gte:0',
        ]);
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $aliquots = $request[$requestAttribute];

        $class = get_class($model);
        $class::saved(function ($model) use ($aliquots) {
            $sampleTypeIds = array_pluck($aliquots, 'sampleType');
            $model->sampleTypes()->attach($sampleTypeIds, [

            ]);
        });
    }
}
