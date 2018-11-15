<?php

namespace App\Nova;

use App\Actions\ChangeValidationStatus;
use App\Fields\AdditionalData;
use App\Fields\Status;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class ResultData extends Resource
{
    public static $globallySearchable = false;

    public static $displayInNavigation = false;

    public static $model = 'App\Models\ResultData';

    public static $with = ['result.experiment'];

    public static $title = 'id';

    public static $search = [];

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
        if ($this->result) {
            $dataLabel = $this->result->experiment->result_handler::$dataLabel;
            $additionalDataLabel = $this->result->experiment->result_handler::$additionalDataLabel;
        }
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Result'),
            Text::make($dataLabel ?? 'Data', 'primary_value')
                ->sortable(),
            Text::make($additionalDataLabel ?? 'Additional Data', 'secondary_value')
                ->sortable(),
            AdditionalData::make('additional'),
            Status::make('Status')
                ->loadingWhenNull('Pending')
                ->failedWhen('Declined', 0)
                ->successWhen('Accepted', 1),
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
