<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use App\Fields\HtmlReadonly;

class SampleInformation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\SampleInformation';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
    ];



    public function title()
    {
        return "Sample ID ".$this->sample_id;
    }

    public function subtitle()
    {
        return 'Study: ('.$this->study->study_id.') '.$this->study->name;
    }

    public static function label()
    {
        return 'Sample Informations';
    }

    public static function singularLabel()
    {
        return 'Sample Information';
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
            ID::make()->onlyOnForms(),
            BelongsTo::make('Study')->searchable()->rules('required')->onlyOnDetail(),
            Text::make('Sample ID')
                ->creationRules('required', 'unique:sample_informations,sample_id')
                ->updateRules('required', 'unique:sample_informations,sample_id,{{resourceId}}'),
            Text::make('Subject ID')->rules('required'),
            Text::make('Visit', 'visit_id')->rules('required'),
            DateTime::make('Collected at')->rules('required'),
            HasMany::make('Samples')

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
