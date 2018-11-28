<?php

namespace App\ResultHandlers;

use App\Models\Experiment;
use App\Models\InputParameter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\File;

abstract class ResultHandler
{
    protected $experimentId;
    /**
     * @var File
     */
    protected $file;

    protected $attributeName;

    public static $dataLabel = 'Data';

    public static $additionalDataLabel = 'Additional Data';

    public function __construct(Experiment $experiment, $attributeName, File $file = null)
    {

        $this->experiment = $experiment;
        $this->file = $file ?? $experiment->result_file;
        if (!$this->file) {
            throw new \Exception('No file given');
        }
        $this->attributeName = $attributeName;
        $this->inputParameters = InputParameter::getByExperiment($this->experiment->id);

        $this->handle();
    }

    abstract public function handle();

    public function validateSampleIds(array $sampleIds)
    {
        $existingSampleIds = DB::table('requested_experiments')
            ->join('samples', 'requested_experiments.sample_id', '=', 'samples.id')
            ->join('sample_informations', 'sample_informations.id', '=', 'samples.sample_information_id')
            ->where('requested_experiments.experiment_id', $this->experiment->id)
            ->select('sample_informations.sample_id')->pluck('sample_id')->unique();
        $missingInDb = [];
        foreach ($sampleIds as $sampleId) {
            if (!in_array($sampleId, $existingSampleIds->toArray())) {
                $missingInDb[] = $sampleId;
            }
        }
        $missingInFile = [];
        foreach ($existingSampleIds as $sampleId) {
            if (!in_array($sampleId, $sampleIds)) {
                $missingInFile[] = $sampleId;
            }
        }
        $error = '';
        if (count($missingInFile)) {
            $error .= "The following sample ids were requested for the experiment but missing in the file: " .
                implode(', ', $missingInFile) . ". ";
        }
        if (count($missingInDb)) {
            $error .= "The following sample ids were present in the file but not requested for this experiment: " .
                implode(', ', $missingInDb);
        }
        if (strlen($error)) {
            $this->error($error);
        }
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
            $this->attributeName => $message
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
}
