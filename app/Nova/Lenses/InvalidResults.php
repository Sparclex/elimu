<?php

namespace App\Nova\Lenses;

use App\Fields\Status;
use App\Models\Result;
use App\Nova\Assay;
use App\Nova\Sample;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use Treestoneit\BelongsToField\BelongsToField;

class InvalidResults extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        $ids = Result::with('assay.inputParameter', 'sample.sampleInformation', 'resultData')
            ->whereHas('assay', function ($query) {
                return $query->join('input_parameters', 'assays.id', 'input_parameters.assay_id')
                    ->where('input_parameters.study_id', auth()->user()->study_id);
            })
            ->get()
            ->filter(function ($result) {
                return $result->status == 'Pending';
            })
            ->pluck('id');

        return $request->withOrdering($request->withFilters(
            $query
        ))->whereIn('id', $ids)
        ->with('assay.inputParameter', 'sample.sampleInformation', 'resultData');
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {

        return [
            ID::make('ID', 'id')->sortable(),

            BelongsToField::make('Sample', 'sample', Sample::class),
            BelongsToField::make('Assay', 'assay', Assay::class),
            Text::make('Target')
                ->sortable(),
            Text::make('Value'),
            Status::make('Status')
                ->loadingWhen('Pending')
                ->successWhen('Verified'),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request  $request
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
        return 'invalid';
    }
}
