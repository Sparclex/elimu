<?php

namespace App\Nova;

use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Control extends Resource
{
    public static $model = 'App\Models\Control';

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static $globallySearchable = true;

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(
                    'required',
                    (new StudyUnique('assays', 'name'))->ignore($request->resourceId)
                ),
            Trix::make('Description'),
            Text::make('Concentration'),
            BelongsToMany::make('Assays')
                ->searchable(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new DownloadExcel)->withHeadings()->allFields(),
        ];
    }
}
