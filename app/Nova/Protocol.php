<?php

namespace App\Nova;

use App\Rules\StudyUnique;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
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
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'protocol_id', 'name'
    ];

    public static function label()
    {
        return 'SOPs';
    }

    public static function singularLabel()
    {
        return 'SOP';
    }

    public function subtitle()
    {
        return $this->name;
    }

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
            Text::make('SOP Id', 'protocol_id')
                ->rules(
                    'required',
                    (new StudyUnique('protocols', 'protocol_id'))->ignore($request->resourceId)
                ),
            Text::make('Name')
                ->rules('required'),
            Text::make('Version')
                ->rules('required'),
            Date::make('Implemented date', 'implemented_at')
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
