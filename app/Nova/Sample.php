<?php

namespace App\Nova;

use App\Fields\QuickBelongsToMany;
use App\Importer\SampleImporter;
use App\Models\Storage;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Treestoneit\BelongsToField\BelongsToField;

class Sample extends Resource
{
    public static $model = \App\Models\Sample::class;

    public static $search = ['sample_id', 'subject_id', 'visit_id'];

    public static $title = 'sample_id';

    public static $importer = SampleImporter::class;


    public function subtitle()
    {
        return $this->subject_id;
    }

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->onlyOnForms(),
            Text::make('Sample ID')
                ->creationRules('required', 'unique:samples,sample_id')
                ->updateRules('required', 'unique:samples,sample_id,{{resourceId}}')
                ->sortable(),
            Text::make('Subject ID')
                ->sortable(),
            Text::make('Visit', 'visit_id')
                ->sortable(),
            DateTime::make('Collected at')
                ->sortable(),
            Date::make('Birthdate')
                ->rules('nullable', 'date')
                ->hideFromIndex(),
            Select::make('Gender')
                ->options([0 => 'Male', 1 => 'Female'])
                ->displayUsingLabels()
                ->hideFromIndex(),

            QuickBelongsToMany::make('Types', 'sampleTypes')
                ->fields([
                    Select::make('Sample Type', 'id')
                        ->options(\App\Models\SampleType::pluck('name', 'id'))
                        ->rules('required_with:quantity', 'exists:sample_types,id'),
                    Number::make('Aliquots', 'quantity')
                        ->rules('nullable', 'numeric', 'min:0', 'not_in:0')
                ])
                ->afterAttachCallback(function ($relatedModels, $changes) {

                    foreach ($relatedModels as $sampleTypeId => $model) {
                        if (!isset($model['quantity']) || !$model['quantity']) {
                            continue;
                        }

                        if (in_array($sampleTypeId, $changes['attached'])) {
                            Storage::generateStoragePosition(
                                $this->id,
                                $this->study_id,
                                $sampleTypeId,
                                $model['quantity']
                            );
                        } else {
                            $oldQuantity = Storage::where([
                                'sample_id' => $this->id,
                                'study_id' => $this->study_id,
                                'sample_type_id' => $sampleTypeId
                            ])->count();

                            if ($oldQuantity <= $model['quantity']) {
                                Storage::generateStoragePosition(
                                    $this->id,
                                    $this->study_id,
                                    $sampleTypeId,
                                    $model['quantity'] - $oldQuantity
                                );
                            } else {
                                Storage::where([
                                    'sample_id' => $this->id,
                                    'study_id' => $this->study_id,
                                    'sample_type_id' => $sampleTypeId
                                ])->orderByDesc('position')
                                    ->limit($oldQuantity - $model['quantity'])
                                    ->delete();
                            }
                        }
                    }
                }),

            HasMany::make('Results'),

            BelongsToMany::make('Types', 'sampleTypes', SampleType::class)
                ->fields(function () {
                    return [
                        Text::make('Aliquots', 'quantity')
                    ];
                }),

            BelongsToMany::make('Shipments')
                ->fields(function () {
                    return [Number::make('Aliquots', 'quantity')];
                }),
        ];
    }
}
