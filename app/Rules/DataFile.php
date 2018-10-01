<?php

namespace App\Rules;

use App\Utility\CSVReader;
use App\Utility\RDML;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DataFile
{
    protected $message = 'The given file is not a valid data file.';

    private $experimentId;

    public function __construct($experimentId)
    {

        $this->experimentId = $experimentId;
    }

    /**
     * Determine if the given file is a valid data file.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function validate($attribute, $value)
    {
        if (!$value instanceof UploadedFile) {
            throw ValidationException::withMessages([
                $attribute => __('It has to be a file')
            ]);
        }
        try {
            switch ($value->getClientOriginalExtension()) {
                case 'rdml':
                case 'zip':
                    $this->validateRdml($value);
                    break;
                case 'csv':
                    $this->validateCsv($value);
                    break;
                default:
                    throw ValidationException::withMessages([
                        $attribute => 'Unsupported file type'
                    ]);
            }
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                $attribute => $e->getMessage()
            ]);
        }
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateRdml(UploadedFile $file)
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

    protected function validateCsv(UploadedFile $file)
    {
        try {
            $reader = new CSVReader($file->getRealPath());
            $data = $reader->toArray();
        } catch (\Exception $e) {
            throw new \Exception($this->message);
        }
        if (!isset($data[0]['sample_id'])) {
            throw new \Exception('There no sample_id column');
        }
        if (!isset($data[0]['target'])) {
            throw new \Exception('There is not target column');
        }
        $this->validateSampleIds(array_column($data, 'sample_id'));
    }
}
