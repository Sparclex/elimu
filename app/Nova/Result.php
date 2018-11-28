<?php

namespace App\Nova;

use App\Fields\Status;
use Illuminate\Http\Request;
use Treestoneit\BelongsToField\BelongsToField;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Result extends Resource
{
    public static $displayInNavigation = false;

    public static $model = 'App\Models\Result';

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static $with = ['assay.inputParameter','sample.sampleInformation', 'resultData'];

    public function fields(Request $request)
    {
        return [
            ID::make()
                ->hideFromIndex(),
            BelongsToField::make('Sample'),
            BelongsToField::make('Assay'),
            Text::make('Target')->sortable(),
            Text::make('Value', function () {
                $inputParameter = collect($this->assay->inputParameter->parameters)
                                            ->firstWhere('target', $this->target);
                $sample = $this->sample->sampleInformation->sampleId;
                $value = $this->determineValue(
                    $this->resultData
                        ->where('status', 1)
                        ->map(function ($data) {
                            return $data->getOriginal('primary_value');
                        }),
                    $inputParameter['cutoff'],
                    $inputParameter['lod'],
                    $inputParameter['cuttoffstdev']
                );
                if ($value == 'Positive' &&
                    strtolower(
                        $inputParameter['quant']
                    ) == 'yes') {
                    return $inputParameter['slope'] * collect($sample)->avg('cq')
                     + $inputParameter['intercept'] . " (Positive)";
                }
                return $value;
            }),
            Status::make('Status', function () {
                $inputParameter = collect($this->assay->inputParameter->parameters)
                                            ->firstWhere('target', $this->target);
                return $inputParameter['minvalues'] <= $this->resultData
                    ->pluck('status')
                    ->filter(function ($value) {
                        return $value == 1;
                    })->count() ? 'Verified' : 'Pending';
            })->loadingWhen('Pending')
            ->successWhen('Verified'),

            HasMany::make('Data', 'resultData', ResultData::class),

        ];
    }

    private function determineValue($cqs, $cutoff, $lod, $cuttoffstdev)
    {
        $isPositive = null;
        $needsRepetition = false;
        if (!count($cqs)) {
            return 'Insufficient data';
        }
        if ($cqs->standardDeviation() > $cuttoffstdev) {
            return 'Exceeds cutoff for standard deviation';
        }
        foreach ($cqs as $cq) {
            $status = $cq && $cq <= $cutoff ? true : false;
            if ($isPositive === null) {
                $isPositive = $status;
            } elseif ($isPositive !== $status) {
                $needsRepetition = true;
            }
        }
        return $needsRepetition ? 'Invalid data' : ($isPositive ? 'Positive' : 'Negative');
    }
}
