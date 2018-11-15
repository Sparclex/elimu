<?php

namespace App\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class SampleRegistry extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->select([
                'samples.id',
                'samples.created_at',
                'samples.updated_at',
                'sample_informations.study_id',
                'sample_informations.sample_id',
                'sample_informations.subject_id',
                'sample_informations.visit_id',
                'sample_informations.collected_at',
                'sample_types.name as sample_type',
                'studies.study_id as study',
                'position',
                'box'
            ])
                ->leftJoin('storage', 'samples.id', '=', 'storage.sample_id')
                ->join('sample_informations', 'samples.sample_information_id', 'sample_informations.id')
                ->join('studies', 'studies.id', 'sample_informations.study_id')
                ->join('sample_types', 'sample_types.id', 'samples.sample_type_id')
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(),
            Text::make('Sample ID'),
            Text::make('Study'),
            Text::make('Sample Type'),
            Text::make('Subject ID'),
            Text::make('Visit ID'),
            Text::make('Collected at'),
            Number::make('Box'),
            Number::make('Position')
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'sample-registry';
    }
}
