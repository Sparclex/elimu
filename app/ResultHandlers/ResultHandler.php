<?php

namespace App\ResultHandlers;

use App\Models\Result;
use App\Models\Sample;
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

        $this->inputParameters = $experiment->assay->parameters;
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
        $existingSampleIds = $this->experiment->samples->pluck('sample_id');

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
        return Sample::whereIn('sample_id', $sampleIds)
            ->pluck('id', 'sample_id');
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

    abstract public static function determineResultValue(Result $result);

    abstract public static function getStatus(Result $result);
}
