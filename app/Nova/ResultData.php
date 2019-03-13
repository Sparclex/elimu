<?php

namespace App\Nova;

use App\Actions\ChangeValidationStatus;
use App\Fields\AdditionalData;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

class ResultData extends Resource
{
    public static $globallySearchable = true;

    public static $displayInNavigation = false;

    public static $model = 'App\Models\ResultData';

    public static $title = 'id';

    public static $search = [
        'sample_id',
        'target',
        'primary_value',
        'secondary_value'
    ];

    public static $with = ['result', 'experiment'];

    public function title()
    {
        return $this->sample_id;
    }

    public function subtitle()
    {
        return sprintf(
            '%d %s (%s)',
            $this->experiment->id,
            $this->experiment->name,
            $this->target
        );
    }

    public static function singularLabel()
    {
        return 'Data';
    }

    public static function label()
    {
        return 'Data';
    }

    public static function uriKey()
    {
        return 'result-data';
    }

    public function fields(Request $request)
    {
        return [
            ID::make()->onlyOnForms(),
            BelongsToField::make('Result'),
            BelongsToField::make('Experiment'),
            Text::make('Sample ID', 'sample_id')
                ->sortable(),
            Text::make('Target')
                ->sortable(),
            Number::make('Primary Value')
                ->displayUsing(function ($value) {
                    if (!$value) {
                        return "&mdash;";
                    }
                    return number_format(round($value, 2), 2);
                })
                ->asHtml()
                ->sortable()
                ->nullable()
                ->nullValues(function ($value) {
                    return $value == '' || $value == 'null' || (float)$value === 0.00 || $value == '0.00';
                }),
            Text::make('Secondary Value')
                ->sortable(),
            Boolean::make('included')
                ->sortable(),
            AdditionalData::make('extra')
        ];
    }

    private function data()
    {
        /*
        if ($this->experiment) {
            $dataLabel = $this->experiment->result_handler::$dataLabel;
            $additionalDataLabel = $this->experiment->result_handler::$additionalDataLabel;
        }

        $primaryValueField = Text::make($dataLabel ?? 'Data', 'primary_value');
        if (is_numeric($this->primary_value) || !strlen($this->primary_value)) {
            $primaryValueField = Number::make($dataLabel ?? 'Data', 'primary_value')
                ->resolveUsing(function ($value) {
                    return number_format((float)$value, 2, '.', '\'');
                });
        }

        return [
            $primaryValueField
                ->sortable(),
            Text::make($additionalDataLabel ?? 'Additional Data', 'secondary_value')
                ->sortable(),
            AdditionalData::make('extra')
        ];
        */
    }

    public function actions(Request $request)
    {
        return [
            (new ChangeValidationStatus())->canRun(function ($request, $user) {
                return true;
            }),
        ];
    }
}
