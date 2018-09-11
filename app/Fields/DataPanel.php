<?php

namespace App\Fields;

use Laravel\Nova\Panel;

class DataPanel extends Panel {
    /**
     * Create a new panel instance.
     *
     * @param  string  $name
     * @param  \Closure|array  $fields
     * @return void
     */
    public function __construct($name)
    {
        parent::__construct($name, [Data::make('Data')]);
    }
}
