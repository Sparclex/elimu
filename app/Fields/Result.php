<?php

namespace App\Fields;

use Laravel\Nova\Fields\Field;

class Result extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'result';

    public $showOnCreation = false;

    public $showOnUpdate = false;
}
