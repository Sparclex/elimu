<?php

namespace App\Nova;

use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Sparclex\NovaCreatableBelongsTo\CreatableBelongsTo;

class Protocol extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Protocol';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'protocol_id';

    public function subtitle()
    {
        return $this->name;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'protocol_id', 'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable()
                ->hideFromDetail()
                ->hideFromIndex(),
            Text::make('Protocol Id')
                ->rules(
                    'required',
                    (new StudyUnique('protocols', 'protocol_id'))->ignore($request->resourceId)
                ),
            Text::make('Name')
                ->rules('required'),
            Text::make('Version')
                ->rules('required'),
            Date::make('implemented_at')
                ->rules('required'),
            File::make('Attachment', 'attachment_path')
                ->rules('required')
                ->storeOriginalName('attachment_name')
                ->prunable(),
            CreatableBelongsTo::make('Responsible', 'responsible', Person::class)
                ->rules('required'),
            CreatableBelongsTo::make('Institution', 'institution', Institution::class)
                ->rules('required'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
