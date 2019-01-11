<?php

namespace App\Nova;

use App\Actions\ChangeValidationStatus;
use App\Fields\AdditionalData;
use App\Fields\Status;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use Treestoneit\BelongsToField\BelongsToField;

class ResultData extends Resource
{
    public static $globallySearchable = false;

    public static $displayInNavigation = false;

    public static $model = 'App\Models\ResultData';

    public static $title = 'id';

    public static $search = [
        'sample_id',
        'status',
    ];

    public static $with = ['result', 'experiment'];

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
            ID::make()
                ->sortable(),
            BelongsToField::make('Result'),
            BelongsToField::make('Experiment'),
            Text::make('Sample ID')->sortable(),
            Status::make('Status')
                ->failedWhen('Declined', 0)
                ->successWhen('Accepted', 1),
            new Panel('Data', $this->data()),
        ];
    }

    private function data()
    {
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
