<?php

namespace App\Nova;

use App\Nova\Filters\AssayFilter;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\DecodesFilters;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class AssayResult extends Resource
{

    protected $assay = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Sample';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    public static $globallySearchable = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static function singularLabel()
    {
        return 'Evaluated Result';
    }

    public static function label()
    {
        return 'Evaluated Results';
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return parent::indexQuery($request, $query)
            ->whereHas('results');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fields = [
            ID::make()->sortable(),
            Text::make('Sample Id')
        ];

        if ($assay = $this->getAssayFromFilter($request)) {
            foreach ($assay->parameters as $parameter) {
                $target = $parameter['target'];
                $fields[] = Text::make($target . '_accepted');
            }
        }


        return $fields;
    }

    public function getAssayFromFilter(Request $request)
    {
        if ($this->assay === false) {
            $filter = $this->decodedFilters($request)->firstWhere('class', AssayFilter::class);

            $this->assay = null;

            if ($filter) {
                $this->assay = \App\Models\Assay::find($filter->value);
            }
        }
        return $this->assay;
    }

    public function decodedFilters(Request $request)
    {
        return collect(json_decode(base64_decode($request->get('filters'))));
    }

    public function filters(Request $request)
    {
        return [
            new AssayFilter
        ];
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToAdd(NovaRequest $request, $model)
    {
        return false;
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}
