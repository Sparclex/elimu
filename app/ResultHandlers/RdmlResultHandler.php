<?php

namespace App\ResultHandlers;

use App\FileTypes\RDML;

class RdmlResultHandler extends ResultHandler
{
    public function handle()
    {
        if (!$this->inputParameters) {
            $this->error(__('Input parameters not set'));
        }
        $rdml = RDML::make($this->file, false)->withInputParameters($this->inputParameters);
        if (empty($rdml->getData())) {
            $this->error(__('Invalid rdml file'));
        }
        if (!$rdml->hasValidTargets()) {
            $this->error(__($rdml->getLastError()));
        }
        if (!$rdml->hasEnoughRepetitions()) {
            $this->error(__($rdml->getLastError()));
        }
        if (!$rdml->hasCorrectDeviation()) {
            $this->error(__($rdml->getLastError()));
        }

        $this->validateSampleIds($rdml->getSampleIds()->toArray());

        $this->removeData();

        try {
            $this->store($rdml);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function store(RDML $rdml)
    {
        $sampleData = $rdml->cyclesOfQuantificationWithoutControl()->map(function ($sample) {
            return [
                'primary' => $sample['cq'],
                'secondary' => $sample['well'],
                'sample' => $sample['sample'],
                'target' => $sample['target'],
                'additional' => serialize(array_except($sample, ['well', 'target', 'sample', 'cq']))
            ];
        });
        $this->storeSampleData($sampleData->toArray());
    }
}
