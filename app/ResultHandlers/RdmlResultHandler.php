<?php

namespace App\ResultHandlers;

use App\FileTypes\RDML;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RdmlResultHandler extends ResultHandler
{
    public function handle()
    {
        $rdml = RDML::make($this->file, false)->withInputParameters($this->inputParameters);
        if (empty($rdml->getData())) {
            $this->error(__('Invalid rdml file'));
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
