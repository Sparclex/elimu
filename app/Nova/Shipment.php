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
            CreatableBelongsTo::make('Contact Person', 'recipientPerson', Person::class)
                ->nullable(),
            CreatableBelongsTo::make('Shipper', 'shipper', Person::class)
                ->nullable(),
            Date::make('Shipment Date')
                ->rules('required')
                ->sortable(),
            Trix::make('Comment'),
            Boolean::make('Shipped', function () {
                return optional(($this->shipment_date))->isPast();
            }),
            CustomBelongsToMany::make('Samples')
                ->fields(function () {
                    return [Number::make('Aliquots', 'quantity')];
                })
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
