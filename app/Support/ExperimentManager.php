<?php

namespace App\Support;

use App\Exceptions\ExperimentException;
use App\Experiments\ExperimentType;
use App\Models\Experiment;
use App\Models\Result;
use App\Models\ResultData;
use App\Models\Sample;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExperimentManager
{
    /**
     * @var ExperimentType
     */
    protected $type;

    /**
     * @var Experiment
     */
    protected $experiment;

    /**
     * @var Collection
     */
    protected $sampleDatabaseIds;

    public function __construct(ExperimentType $type, Experiment $experiment)
    {
        $this->type = $type;
        $this->experiment = $experiment;
    }

    public function validate()
    {
        try {
            $this->type->validate();
            $this->assertSampleExist();
        } catch (ExperimentException $exception) {
            throw ValidationException::withMessages([
                'result_file' => $exception->getMessage()
            ]);
        }
    }

    public function store()
    {
        DB::transaction(function () {
            $this->experiment->resultData()->delete();

            ResultData::insert(
                $this->type->getDatabaseData(
                    $this->experiment
                )->map(function ($dataRow) {
                    return [
                        'result_id' => Result::firstOrCreate([
                            'assay_id' => $this->experiment->assay_id,
                            'study_id' => Auth::user()->study_id,
                            'sample_id' => $this->getSampleDatabaseIds()[$dataRow['sample']],
                            'target' => $dataRow['target']
                        ]),
                        'experiment_id' => $this->experiment->id,
                        'study_id' => Auth::user()->study_id,
                        'primary_value' => $dataRow['primary_value'],
                        'secondary_value' => $dataRow['secondary_value'],
                        'extra' => $dataRow['extra']
                    ];
                })->toArray()
            );
        });
    }

    protected function getSampleDatabaseIds()
    {
        if (!$this->sampleDatabaseIds) {
            $this->sampleDatabaseIds = Sample::whereIn(
                'sample_id',
                $this->type->extractSamplesIds()->toArray()
            )->whereHas('experiments', function ($query) {
                return $query->where('experiment_id', $this->experiment->id);
            })->pluck('id', 'sample_id');
        }

        return $this->sampleDatabaseIds;
    }

    /**
     * @throws ExperimentException
     */
    protected function assertSampleExist()
    {
        $missingSamples = $this->type->extractSamplesIds()->diff($this->getSampleDatabaseIds()->keys());

        if ($missingSamples->isNotEmpty()) {
            throw new ExperimentException(sprintf(
                'Following samples are missing: %s',
                $missingSamples->implode(', ')
            ));
        }
    }
}
