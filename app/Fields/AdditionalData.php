<?php

namespace App\Fields;

use Laravel\Nova\Fields\Field;

class AdditionalData extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'additional-data-field';

    public $showOnCreation = false;

    public $showOnUpdate = false;

    public $showOnIndex = false;

    public function __construct($attribute, $resolveCallback = null)
    {
        parent::__construct('', $attribute, $resolveCallback);
    }
}
