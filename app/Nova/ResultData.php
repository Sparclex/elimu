<?php

namespace App\Nova;

use App\Fields\Status;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use App\Fields\AdditionalData;
use App\Actions\ChangeValidationStatus;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\BelongsToField\BelongsToField;

class ResultData extends Resource
{
    public static $globallySearchable = false;

    public static $displayInNavigation = false;

    public static $model = 'App\Models\ResultData';

    public static $title = 'id';

    public static $search = [];

    public static $with = ['result', 'experiment'];

    public static function singularLabel()
    {
        return 'Data';
    }

    public static function label()
    {
        return 'Data';
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            BelongsToField::make('Result'),
            BelongsToField::make('Experiment'),
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
        return [
            Text::make($dataLabel ?? 'Data', 'primary_value')
                ->sortable(),
            Text::make($additionalDataLabel ?? 'Additional Data', 'secondary_value')
                ->sortable(),
            AdditionalData::make('additional')
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

    public static function uriKey()
    {
        return 'result-data';
    }
}
