<?php

namespace App\ResultHandlers;

use App\FileTypes\RDML;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RdmlResultHandler extends ResultHandler
{
    public static $dataLabel = 'Cq Value';

    public static $additionalDataLabel = 'Position';

    public function handle()
    {
        if (!$this->inputParameters) {
            $this->error(__('Input parameters not set'));
        }

        $rdml = new RDML($this->file, $this->inputParameters);

        if ($rdml->getData()->isEmpty()) {
            $this->error(__('Invalid rdml file'));
        }

        if (!$rdml->hasValidTargets()) {
            $this->error(__($rdml->getLastError()));
        }

        if (!$rdml->hasValidControls()) {
            $this->error(__($rdml->getLastError()));
        }

        $this->validateWithRequestedSamples($rdml->getSampleIds());

        DB::transaction(function () use ($rdml) {
            $this->removeData();
            $this->store($rdml);
        });
    }

    private function store(RDML $rdml)
    {
        $sampleIds = $this
            ->getDatabaseIdBySampleIds(
                $rdml
                    ->cyclesOfQuantificationWithoutControl()
                    ->pluck('sampleId')
            );
        $targets = $rdml
            ->cyclesOfQuantificationWithoutControl()
            ->groupBy(['target', 'sampleId']);
        $resultData = [];
        foreach ($targets as $target => $samples) {
            foreach ($samples as $sampleId => $sample) {
                $result = Result::firstOrCreate([
                    'assay_id' => $this->experiment->reagent->assay->id,
                    'sample_id' => $sampleIds[$sampleId],
                    'target' => $sample[0]['target']
                ]);

                foreach ($sample as $data) {
                    $resultData[] = [
                        'result_id' => $result->id,
                        'primary_value' => $data['cq'],
                        'secondary_value' => $data['position'],
                        'experiment_id' => $this->experiment->id,
                        'study_id' => Auth::user()->study_id,
                        'extra' => json_encode(
                            collect($data)
                                ->except(['position', 'sampleId', 'cq', 'data'])
                                ->merge(['sample ID' => $sampleId])
                                ->sortKeys()
                                ->toArray()
                        )
                    ];
                }
            }
        }

        foreach (array_chunk($resultData, 100) as $chunk) {
            DB::table('result_data')->insert($chunk);
        }
    }

    public static function getStatus(Result $result)
    {

        return is_string(self::getOutput($result)) ? 'Pending' : 'Verified';
    }

    public static function determineResultValue(Result $result)
    {
        $inputParameters = $result->assay->inputParameters;
        if (is_string(static::getOutput($result))) {
            return static::getOutput($result);
        }
        if (static::getOutput($result) === 1 &&
            strtolower(
                $inputParameters['quant']
            ) == 'yes') {
            return $result->resultData
                    ->onlyAccepted()
                    ->quantitativeOutput($inputParameters['slope'], $inputParameters['intercept'])
                ." (Positive)";
        }
        return self::getOutput($result) === 1 ? 'Positive' : 'Negative';
    }

    /**
     * @return int|string numeric if there is a result and string for error message
     */
    public static function getOutput(Result $result)
    {
        if (!$result->output) {
            $result->output = self::determineOutput($result);
        }

        return $result->output;
    }

    private static function determineOutput(Result $result)
    {
        $inputParameters = $result->assay->inputParameters;

        $acceptedResults = $result->resultData->onlyAccepted();

        if (!$acceptedResults->hasEnoughValues($inputParameters['minvalues'])) {
            return "Insufficient amount of data";
        }

        if (!$acceptedResults->standardDeviationIsInRange($inputParameters['cuttoffstdev'])) {
            return "Standard deviation to higher than ". $inputParameters['cuttoffstdev'];
        }

        $value = $acceptedResults->determineResult($inputParameters['cutoff']);

        if ($value == -1) {
            return "Needs Repetition";
        }

        return $value;
    }
}
