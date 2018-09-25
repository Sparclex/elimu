<?php

namespace App\Fields;

use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ReagentsFields extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'reagent-field';

    public function __construct($assays)
    {
        parent::__construct('form');
        $this->withMeta(['assays' => $assays]);
        $this->withMeta(['reagentFields' => $this->reagentFields()]);
    }

    public function reagentFields()
    {
        return [
            Text::make('Lot', 'form[lot]'),
            Text::make('Name', 'form[name]'),
            Date::make('Expires at', 'form[expires_at]')
        ];
    }

    public function getRules(NovaRequest $request) {
        return [
            'form.assay' => 'required|exists:assays,id',
            'form.useExisting' => 'required|boolean',
            'form.reagent' => 'required_if:useExisting,1|exists:reagents,id',
            'form.lot' => 'required_if:useExisting,0',
            'form.name' => 'required_if:useExisting,0',
            'form.expires_at' => 'required_if:useExisting,0|date',
        ];
    }
}
