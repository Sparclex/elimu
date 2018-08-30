<?php

namespace Sparclex\HtmlReadonly;

use Laravel\Nova\Fields\Field;

class HtmlReadonly extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'html-readonly';
    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    public $showOnCreation = false;

    public $showOnUpdate = false;
}
