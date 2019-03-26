<?php

namespace App\Nova;

use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Reagent extends Resource
{
    public static $model = 'App\Models\Reagent';

    public static $search = ['id', 'lot', 'name'];

    public static $title = 'lot';

    public function title()
    {
        return sprintf('%s | %s | %s', $this->name, $this->lot, $this->expires_at->format('d.m.Y'));
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Name')
                ->rules('required')
                ->sortable(),
            Text::make('Lot')
                ->rules(
                    'required',
                    (new StudyUnique('reagents', 'lot'))->ignore($request->resourceId)
                )
                ->sortable(),
            Date::make('Expiry date', 'expires_at')
                ->sortable(),

            Text::make('Manufacturer'),
            Text::make('Supplier'),
            Text::make('Catalog Number')
                ->hideFromIndex(),
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
