<?php

namespace App\ResultHandlers\Rdml;

use App\ResultHandlers\Extractor as ExtractorContract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Extractor implements ExtractorContract
{
    private static $conrolLabels = ['Neg', 'Pos', 'NTC'];

    /**
     * @var \App\ResultHandlers\Rdml\ProcessorContract
     */
    protected $manager;

    public function __construct(ProcessorContract $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $experiment integer representing the data / file in the database
     * @return boolean
     */
    public function handle($experiment)
    {
        $sampleData = $this->manager->cyclesOfQuantification()->reject(
            function ($sample) {
                return in_array($sample['sample'], self::$conrolLabels);
            }
        );
        $sampleIds = $this->getDatabaseIdSampleIds($sampleData->pluck('sample')->toArray());
        $data = [];
        $createdAt = Carbon::now();
        foreach ($sampleData as $sample) {
            if (!isset($sampleIds[$sample['sample']])) {
                return false;
            }
            $data[] = [
                'primary_value' => $sample['cq'],
                'secondary_value' => $sample['well'],
                'sample_id' => $sampleIds[$sample['sample']],
                'target' => $sample['target'],
                'additional' => serialize(array_except($sample, ['well', 'target', 'sample', 'cq'])),
                'experiment_id' => $experiment,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
        DB::table('sample_data')->insert($data);

        return true;
    }

    private function getDatabaseIdSampleIds($sampleIds)
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
}
