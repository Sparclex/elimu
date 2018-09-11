<?php

namespace App\ResultHandlers\Rdml;

use App\ResultHandlers\Extractor as ExtractorContract;
use Illuminate\Support\Facades\DB;

class Extractor implements ExtractorContract
{
    /**
     * @var \App\ResultHandlers\Rdml\ProcessorContract
     */
    protected $manager;

    public function __construct(ProcessorContract $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $dataId integer representing the data / file in the database
     * @return array missing sample warnings
     */
    public function handle($dataId)
    {
        $sampleData = $this->manager->getSampleTargets();
        $sampleIds = $this->getDatabaseIdSampleIds($sampleData->pluck('sample')->toArray());
        $data = [];
        $missingMessages = [];
        foreach ($sampleData as $sample) {
            if (! isset($sampleIds[$sample['sample']])) {
                $missingMessages[$sample['sample']] = 'Sample with ID '.$sample['sample'].' does not exist in Database but is listed in result';
                continue;
            }
            $data[] = [
                'sample_id' => $sampleIds[$sample['sample']],
                'target' => $sample['target'],
                'additional' => $sample['react'],
                'data_id' => $dataId,
            ];
        }
        DB::table('data_sample')->insert($data);

        return array_values($missingMessages);
    }

    private function getDatabaseIdSampleIds($sampleIds)
    {
        return DB::table('sample_informations')->whereIn(
            'sample_id', $sampleIds)->join(
            'samples', ['sample_informations.id' => 'sample_information_id'])->pluck(
            'samples.id', 'sample_informations.sample_id');
    }
}
