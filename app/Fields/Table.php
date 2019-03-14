<?php

namespace App\Fields;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class Table extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'table-field';

    public $showOnIndex = false;

    public $showOnDetail = true;

    public function fields(array $fields)
    {
        $this->withMeta(['fields' => $fields]);

        return $this;
    }

    public function resolveAttribute($resource, $attribute = null)
    {
        return $resource->{$attribute};
    }

    public function getRules(NovaRequest $request)
    {
        return array_merge_recursive(
            parent::getRules($request),
            [
                $this->attribute => 'json'
            ]
        );
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = json_decode($request->get($requestAttribute));
        }
    }
}
