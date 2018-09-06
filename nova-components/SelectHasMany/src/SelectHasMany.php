<?php

namespace Sparclex\SelectHasMany;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\HasMany;

class SelectHasMany extends HasMany
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'select-has-many';
}
