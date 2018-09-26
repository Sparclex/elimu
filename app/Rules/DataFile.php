<?php

namespace App\Rules;

use App\Utility\RDML;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class DataFile implements Rule
{
    protected $message = 'The given file is not a valid data file.';

    private $experimentId;

    public function __construct($experimentId)
    {

        $this->experimentId = $experimentId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value instanceof File) {
            return false;
        }
        try {
            switch ($value->getExtension()) {
                case 'rdml':
                case 'zip':
                    $this->validateRdml($value);
                    break;
                case 'csv':
                    $this->validateCsv($value);
                    break;
                default:
                    return false;
            }
        } catch (\Exception $e) {
            $this->message = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @param \Illuminate\Http\File $file
     * @throws \Exception
     */
    protected function validateRdml(File $file)
    {
        $rdml = RDML::make($file, false);
        if (!$rdml->containsOnlyOneFile()) {
            throw new \Exception('The given rdml contains more than one xml file.');
        }
        if (!$rdml->validateKeys()) {
            throw new \Exception('The given rdml file has an invalid content.');
        }
        if (!$rdml->atLeastOneSampleExists()) {
            throw new \Exception('The given rdml file contains no samples.');
        }
        if (!$rdml->allControlsExist()) {
            throw new \Exception('The given rdml file contains not all control samples.');
        }
        $this->validateSampleIds(array_pluck($rdml->getSamples(), '@id'));
    }

    protected function validateSampleIds($sampleIds)
    {
        $existingSampleIds = DB::table('experiment_requests')
            ->join('samples', 'experiment_requests.sample_id', '=', 'samples.id')
            ->join('sample_informations', 'sample_informations.id', '=', 'samples.sample_information_id')
            ->where('experiment_requests.experiment_id', $this->experimentId)
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
                implode(', ', $missingInFile);
        }
        if (count($missingInDb)) {
            $error .= "The following sample ids were present in the file but not requested for this experiment: " .
                implode(', ', $missingInDb);
        }
        if (strlen($error)) {
            throw new \Exception($error);
        }
    }

    protected function validateCsv(File $file)
    {
        throw new \Error();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
