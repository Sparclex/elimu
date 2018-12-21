<?php

namespace App\Fields;

use App\Models\Reagent;
use App\Models\Experiment;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class ReagentsFields extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'reagent-field';

    public $showOnDetail = false;

    public $showOnIndex = false;

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

    public function getRules(NovaRequest $request)
    {
        return [
            'form.assay' => 'required|exists:assays,id',
            'form.useExisting' => 'required|boolean',
            'form.reagent' => 'required_if:form.useExisting,1|exists:reagents,id',
            'form.lot' => 'required_if:form.useExisting,0',
            'form.name' => 'required_if:form.useExisting,0',
            'form.expires_at' => 'required_if:form.useExisting,0|date',
        ];
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (!($model instanceof Experiment)) {
            return parent::fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);
        }

        $form = $request[$requestAttribute];

        $reagentId = null;

        if (!$form['useExisting']) {
            $reagent = new Reagent();
            $reagent->lot = $form['lot'];
            $reagent->name = $form['name'];
            $reagent->expires_at = $form['expires_at'];
            $reagent->assay_id = $form['assay'];
            $reagent->save();
            $reagentId = $reagent->id;
        } else {
            $reagentId = $form['reagent'];
        }

        $model->reagent_id = $reagentId;
    }
}
