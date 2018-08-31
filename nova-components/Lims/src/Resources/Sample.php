<?php

namespace Sparclex\Lims\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Sparclex\HtmlReadonly\HtmlReadonly;
use Sparclex\Tagging\Tagging;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Timezone;
use Laravel\Nova\Fields\BelongsTo;
use Sparclex\Multiselect\Multiselect;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\StorageSize as StorageSizeModel;

class Sample extends Resource
{

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Sparclex\Lims\Sample';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'brady_number',
        'subject_id',
    ];

    public function title()
    {
        return "Brady Nr. ".$this->brady_number;
    }

    public function subtitle()
    {
        return 'Study: ('.$this->study->study_id.') '.$this->study->name;
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
            ID::make()->sortable()->hideFromIndex()->hideFromDetail(),
            Number::make('Brady Number')->rules('required', 'numeric'),
            BelongsTo::make('Study')->searchable()->rules('required', 'exists:studies,id'),
            BelongsToMany::make('SampleTypes')->fields(function () {
                return [
                    Boolean::make('Should be stored', 'stored')->help('Only sample types with a defined storage size can be stored.'),
                ];
            }),
            HasMany::make('ProcessingLogs'),
            BelongsTo::make('Deliverer', 'deliverer', Person::class)->hideFromIndex()->rules('required', 'exists:people,id')->searchable(),
            BelongsTo::make('Receiver', 'receiver', Person::class)->hideFromIndex()->searchable()->rules('required', 'exists:people,id'),
            Text::make('Subject ID')->rules('required')->hideFromIndex()->rules('required'),
            DateTime::make('Collected at')->hideFromIndex()->rules('date'),
            DateTime::make('Received at')->hideFromIndex()->rules('date'),
            HtmlReadonly::make('Storage Places', function () {
                $content = '';
                foreach ($this->sampleTypes as $type) {
                    if ($type->pivot->stored) {
                        $content .= '<tr><td class="pr-4 text-80">'.$type->name.'</td><td>Box <strong>'.$type->pivot->box.'</strong></td><td>Field <strong>'.$type->pivot->field.'</strong></td></tr>';
                    }
                }

                return $content ? "<table>".$content."</table>" : "<p class='italic text-90'>No samples stored</p>";
            }),
            Text::make('Visit'),
            Trix::make('Comment'),
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
