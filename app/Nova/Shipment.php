<?php

namespace App\Nova;

use App\Fields\CustomBelongsToMany;
use App\Fields\SampleIds;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Sparclex\NovaCreatableBelongsTo\CreatableBelongsTo;
use Treestoneit\BelongsToField\BelongsToField;

class Shipment extends Resource
{
    public static $globallySearchable = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Shipment';

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
        'id', 'recipient', 'shipment_date'
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        return parent::indexQuery($request, $query)->withCount('samples');
    }

    public function title()
    {
        return sprintf('%s - %s', $this->recipient, $this->shipment_date->format('Y-m-d'));
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
            ID::make()->sortable(),
            SampleIds::make('Samples')
                ->pivot('quantity')
                ->help('Each line in the following format: sample_id,aliquots')
                ->rules('required'),
            BelongsToField::make('Type', 'sampleType', SampleType::class),
            Text::make('Recipient')
                ->rules('required'),
            CreatableBelongsTo::make('Recipient Contact', 'recipientPerson', Person::class)
                ->nullable()
                ->hideFromIndex(),
            Text::make('Shipper', 'shipper_institution'),
            CreatableBelongsTo::make('Shipper Contact', 'shipper', Person::class)
                ->nullable()
                ->hideFromIndex(),
            Date::make('Shipment Date')
                ->rules('required')
                ->sortable(),
            Trix::make('Condition'),
            Trix::make('Comment'),
            Boolean::make('Shipped', function () {
                return optional(($this->shipment_date))->isPast();
            }),
            Text::make('Samples', 'samples_count')
                ->onlyOnIndex()
                ->sortable(),
            CustomBelongsToMany::make('Samples')
                ->fields(function () {
                    return [Number::make('Aliquots', 'quantity')];
                })
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
