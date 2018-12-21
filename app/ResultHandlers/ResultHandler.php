<?php

namespace App\ResultHandlers;

use App\Models\Result;
use Illuminate\Http\File;
use App\Models\Experiment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

abstract class ResultHandler
{
    protected $experimentId;

    protected $path;

    protected $attributeName;

    public static $dataLabel = 'Data';

    public static $additionalDataLabel = 'Additional Data';

    public function __construct(Experiment $experiment, $path)
    {
        $this->experiment = $experiment;

        $this->path = $path;

        $this->inputParameters = $experiment->inputParameters;
    }

    abstract public function handle();

    public function validateWithRequestedSamples($samplesIds)
    {
        $missingIds = $this->diffExperimentSamples($samplesIds);

        $error = '';

        if ($missingIds['missingInDb']->isNotEmpty()) {
            $error = "The following sample ids were present in the file but not requested for this experiment: " .
                $missingIds['missingInDb']->implode(', ');
        }

        if ($missingIds['missingInFile']->isNotEmpty()) {
            $error .= "The following sample ids were requested for the experiment but missing in the file: " .
                $missingIds['missingInFile']->implode(', ') . ". ";
        }

        if (strlen($error)) {
            $this->error($error);
        }
    }

    /**
     * @param $sampleIds
     * @return Collection[]
     */
    public function diffExperimentSamples($sampleIds)
    {
        $existingSampleIds = $this->experimentSamples();

        $sampleIds = is_array($sampleIds) ? collect($sampleIds) : $sampleIds;


        $missingInDb = $existingSampleIds->reject(function ($sampleId) use ($sampleIds) {
            return $sampleIds->contains($sampleId);
        });

        $missingInFile = $sampleIds->reject(function ($sampleId) use ($existingSampleIds) {
            return $existingSampleIds->contains($sampleId);
        });

        return compact('missingInDb', 'missingInFile');
    }

    public function getDatabaseIdBySampleIds($sampleIds)
    {
        return DB::table('sample_informations')->whereIn(
            'sample_id',
            $sampleIds
        )->join(
            'samples',
            ['sample_informations.id' => 'sample_information_id']
        )->pluck(
            'samples.id',
            'sample_informations.sample_id'
        );
    }

    public function error($message)
    {
        throw ValidationException::withMessages([
            'result_file' => $message
        ]);
    }

    public function removeData()
    {
        self::removeSampleData($this->experiment->id);
    }

    public static function removeSampleData($experimentId)
    {
        DB::table('result_data')->where('experiment_id', $experimentId)->delete();
    }

    public function experimentSamples()
    {
        return DB::table('requested_experiments')
            ->join('samples', 'requested_experiments.sample_id', '=', 'samples.id')
            ->join('sample_informations', 'sample_informations.id', '=', 'samples.sample_information_id')
            ->where('requested_experiments.experiment_id', $this->experiment->id)
            ->select('sample_informations.sample_id')
            ->distinct()
            ->pluck('sample_id');
    }

    abstract public static function determineResultValue(Result $result);

    abstract public static function getStatus(Result $result);
}
