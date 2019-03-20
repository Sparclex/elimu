<?php

namespace App\Experiments;

use App\Nova\Filters\TargetFilter;
use App\Nova\Resource;
use App\Support\QPCRResultSpecifier;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class QPCRResult extends Resource
{
    public static $model = 'App\Models\Result';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(),
            Text::make('Sample ID', function () {
                return $this->sample->sample_id;
            }),
            Text::make('Target'),
            Text::make('Result', function () {
                $parameters = $this->assay->definitionFile->parameters->firstWhere('target', strtolower($this->target));
                return (new QPCRResultSpecifier($parameters, $this->resource))
                    ->withStyles()
                    ->qualitative();
            })->asHtml(),
            Number::make('Quant', function () {
                $parameters = $this->assay->definitionFile->parameters->firstWhere('target', strtolower($this->target));
                return (new QPCRResultSpecifier($parameters, $this->resource))
                    ->quantitative();
            }),
        ];
    }
}
