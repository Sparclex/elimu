<?php

namespace App\Nova;

use App\Support\Position;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

class Storage extends Resource
{
    public static $globallySearchable = false;

    public static $model = 'App\Models\Storage';

    public static $title = 'id';

    public static $with = ['sample', 'sampleType'];

    public static function label()
    {
        return 'Storage';
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->onlyOnForms(),
            BelongsToField::make('Sample'),
            BelongsToField::make('Type', 'sampleType', SampleType::class),
            Text::make('Position')->resolveUsing(
                function () {
                    if (! $this->sampleType->columns || ! $this->sampleType->rows) {
                        return sprintf(
                            '<span>%s</span> 
                            <span class="text-sm text-80">
                                specify rows and columns in 
                                <a href="%s/resources/sample-types/%s/edit" 
                                class="no-underline dim text-primary">%s</a>
                            </span>',
                            $this->position,
                            config('nova.path'),
                            $this->sampleType->id,
                            $this->sampleType->name
                        );
                    }

                    return Position::fromPosition($this->position)
                        ->withColumns($this->sampleType->columns)
                        ->withRows($this->sampleType->rows)
                        ->withColumnFormat($this->sampleType->column_format)
                        ->withRowFormat($this->sampleType->row_format)
                        ->startWithZero()
                        ->showPlates()
                        ->toLabel();
                }
            )->asHtml()
                ->sortable(),
        ];
    }
}
