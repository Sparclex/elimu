<?php

namespace App\ResultHandlers;

use App\Importer\ResultImporter;
use App\Models\Result;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CsvResultHandler extends ResultHandler
{
    public function handle()
    {
        $data = Excel::toCollection(new ResultImporter, $this->path)->first();

        if (!$this->hasRequiredColumns($data->first())) {
            $this->error(__('Columns \'sample\', \'target\' and \'data\' have to be present.'));
        }

        $this->validateWithRequestedSamples($data->pluck('sample'));

        $resultHandler = $this;


        DB::transaction(function () use ($data, $resultHandler) {
            $resultHandler->removeData();

            $resultHandler->store($data);
        });
    }

    private function hasRequiredColumns(Collection $row)
    {
        return $row->has(['sample', 'target', 'data']);
    }

    public function store(Collection $data)
    {
        $sampleIds = $this->getDatabaseIdBySampleIds($data->pluck('sample'));

        $resultData = [];

        $createdAt = now();
        $updatedAt = now();

        $data->each(function ($row) {
            $row['sample'] = (int) $row['sample'];
        });

        foreach ($data->groupBy(['target', 'sample']) as $target => $samples) {
            foreach ($samples as $sampleId => $sample) {
                $result = Result::firstOrCreate([
                    'assay_id' => $this->experiment->assay_id,
                    'sample_id' => $sampleIds[$sampleId],
                    'target' => $sample[0]['target']
                ]);

                foreach ($sample as $sampleData) {
                    $resultData[] = [
                        'result_id' => $result->id,
                        'primary_value' => $sampleData['data'],
                        'secondary_value' => $sampleData['secondary'] ?? null,
                        'experiment_id' => $this->experiment->id,
                        'study_id' => Auth::user()->study_id,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                        'extra' => collect($sampleData)
                            ->except(['sample', 'target', 'data', 'secondary'])
                            ->sortKeys()
                            ->toJson()
                    ];
                }
            }
        }

        foreach (array_chunk($resultData, 100) as $chunk) {
            DB::table('result_data')->insert($chunk);
        }
    }


    public static function determineResultValue(Result $result)
    {
        return $result->resultData->pluck('data')->unique()->implode(', ');
    }

    public static function getStatus(Result $result)
    {
        return 'Accepted';
    }
}
