<?php

namespace App\ResultHandlers;

use App\Models\Result;
use App\FileTypes\RDML;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        // the following
/*        if (!$rdml->hasCorrectDeviation()) {
            $this->error(__($rdml->getLastError()));
        }*/
        $this->validateSampleIds($rdml->getSampleIds()->toArray());

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
            $targetInputParameter = collect($this->inputParameters)->firstWhere('target', $target);
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
                        'additional' => serialize(
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
}
