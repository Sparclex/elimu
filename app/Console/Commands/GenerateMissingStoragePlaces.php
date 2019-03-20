<?php

namespace App\Console\Commands;

use App\Models\Sample;
use App\Models\SampleMutation;
use App\Models\Storage;
use App\Support\StoragePointer;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GenerateMissingStoragePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elimu:storage:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates missing storage places';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $samples = Sample::withoutGlobalScopes()->with('allSampleTypes')->get();
        $storage = Storage::withoutGlobalScopes()->get();

        $studyIds = $samples->pluck('study_id');


        $pointers = array_combine($studyIds->toArray(), array_fill(0, $studyIds->count(), []));

        $positions = [];

        foreach ($samples as $sample) {
            foreach ($sample->allSampleTypes as $sampleMutation) {
                $sampleMutation = $sampleMutation->pivot;
                if ($storage->where('sample_type_id', $sampleMutation->sample_type_id)->where(
                    'study_id',
                    $sample->study_id
                )->where(
                    'sample_id',
                    $sampleMutation->sample_id
                )->count() < $sampleMutation->quantity) {
                    if (! isset($pointers[$sample->study_id][$sampleMutation->sample_type_id])) {
                        $pointers[$sample->study_id][$sampleMutation->sample_type_id] = new StoragePointer(
                            $sampleMutation->sample_type_id,
                            $sample->study_id
                        );
                    }
                    $positions = array_merge(
                        $positions,
                        $pointers[$sample->study_id][$sampleMutation->sample_type_id]->store(
                            $sampleMutation->sample_id,
                            $sampleMutation->quantity,
                            false
                        )
                    );
                }
            }
        }
        $numberOfRows = 0;

        foreach (array_chunk($positions, 100) as $data) {
            $numberOfRows += DB::table('storage')->insert($data);
        }

        $this->line(sprintf('%d rows inserted', $numberOfRows));
    }
}
