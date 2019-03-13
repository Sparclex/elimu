<?php

namespace App\Fields;

use Closure;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class QuickBelongsToMany extends Field
{
    public $component = 'quick-belongs-to-many-field';

    public $showOnCreation = true;

    public $showOnUpdate = true;

    public $showOnIndex = false;

    public $showOnDetail = false;

    public $afterAttachCallback;

    public function resolve($resource, $attribute = null)
    {
        $this->value = $resource->{$this->attribute}
            ->pluck('pivot')
            ->map(function ($data) {
                $newData = [
                    'id' => $data[$data->getRelatedKey()],
                ];

                foreach ($this->meta['fields'] as $field) {
                    if ($field->attribute == 'id') {
                        continue;
                    }
                    $newData[$field->attribute] = $data[$field->attribute];
                }

                return $newData;
            });
    }


    public function afterAttachCallback($callback)
    {
        $this->afterAttachCallback = $callback;

        return $this;
    }

    public function fields(array $fields)
    {
        $this->withMeta(
            [
                'fields' => $fields
            ]
        );

        return $this;
    }

    public function getRules(NovaRequest $request)
    {
        return [$this->attribute => 'json'];
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {

        if (!$request[$requestAttribute]) {
            return;
        }

        $rules = [];
        foreach ($this->meta['fields'] as $field) {
            $rules['*.' . $field->attribute] = array_values($field->getRules($request))[0] ?? [];
        }

        $data = Validator::make(json_decode($request[$requestAttribute], 1), $rules)->validate();

        $relatedModels = [];

        foreach ($data as $relatedModel) {
            $relatedModels[$relatedModel['id']] = [];
            foreach ($this->meta['fields'] as $field) {
                if ($field->attribute == 'id') {
                    continue;
                }
                $relatedModels[$relatedModel['id']][$field->attribute] = $relatedModel[$field->attribute]
                && strlen($relatedModel[$field->attribute]) ? $relatedModel[$field->attribute] : null;
            }
        }

        $class = get_class($model);
        $callback = $this->afterAttachCallback;
        $class::saved(function ($model) use ($relatedModels, $requestAttribute, $callback) {
            $changes = $model->{$requestAttribute}()->sync($relatedModels);

            if ($callback) {
                Closure::bind($callback, $model);
                $callback($relatedModels, $changes);
            }
        });
    }
}
