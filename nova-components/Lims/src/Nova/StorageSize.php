<?php

namespace Sparclex\Lims\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;

class StorageSize extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Sparclex\Lims\Models\StorageSize';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static $displayInNavigation = false;

    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $studyId = $request->get('study');
        $sampleTypeId = $request->get('sampleType');
        return [
            ID::make()->sortable(),
            BelongsTo::make('Study')->rules('required', 'exists:studies,id'),
            BelongsTo::make('Sample Type', 'sampleType', SampleType::class)
                ->creationRules('required', 'exists:sample_types,id',
                    Rule::unique('storage_sizes', 'sample_type_id')->where(function ($query) use ($studyId, $sampleTypeId) {
                        return $query->where('study_id', $studyId);
                    }))
                ->updateRules('required', 'exists:sample_types,id',
                    Rule::unique('storage_sizes', 'sample_type_id')->where(function ($query) use ($studyId, $sampleTypeId) {
                        return $query->where('study_id', $studyId);
                    })->ignore('{{resourceId}}')),
            Number::make('Fields per box', 'size')->rules('required', 'numeric'),
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
