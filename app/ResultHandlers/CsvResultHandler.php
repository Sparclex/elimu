<?php

namespace App\ResultHandlers;

use App\FileTypes\CSV;

class CsvResultHandler extends ResultHandler
{
    public function handle()
    {
        $csv = CSV::make($this->file->getRealPath());
        if (!$csv->isValid()) {
            $this->error(__('CSV is not valid. Columns \'sample\', \'target\' and \'data\' have to be present.'));
        }
        $this->validateSampleIds($csv->getSamplesIds()->toArray());
        try {
            $this->removeData();
            $this->store($csv);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function store(CSV $csv)
    {
        $sampleData = $csv->getData()->map(function ($sample) {
            return [
                'primary' => $sample['data'],
                'secondary' => $sample['secondary'] ?? null,
                'sample' => $sample['sample'],
                'target' => $sample['target'],
                'additional' => serialize(array_except($sample, ['data', 'secondary', 'sample', 'target']))
            ];
        });
        $this->storeSampleData($sampleData->toArray());
    }
}
