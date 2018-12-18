<?php

namespace App\Fields;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class SampleTypeField extends BelongsTo
{
    public $showOnCreation = true;

    public $showOnUpdate = false;

    public $showOnIndex = false;

    public $showOnDetail = false;

    public function fill(NovaRequest $request, $model)
    {
    }

    public function getRules(NovaRequest $request)
    {
        return [$this->attribute => 'required'];
    }

    public function resolve($resource, $attribute = null)
    {
    }
}
