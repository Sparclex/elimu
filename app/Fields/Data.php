<?php

namespace App\Fields;

use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Fields\Field;

class Data extends Field implements ListableField
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'data';

    public $showOnCreation = false;

    public $showOnUpdate = false;

    public $showOnIndex = false;
}
