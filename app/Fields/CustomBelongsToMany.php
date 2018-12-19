<?php

namespace App\Fields;

use App\Rules\RelatableAttachment;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class CustomBelongsToMany extends BelongsToMany
{
    public function getRules(NovaRequest $request)
    {
        $rules = parent::getRules($request);

        $rules[$this->attribute] = [
            'required',
            new RelatableAttachment($rules[$this->attribute][1]->request, $rules[$this->attribute][1]->query)
        ];

        return $rules;
    }
}
