<?php

namespace App\Console\Commands;

use App\Models\ResultData;
use Illuminate\Console\Command;

class DeleteResultDataDuplicate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elimu:result:duplicate:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes duplicated result data';

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
        $results = ResultData::withoutGlobalScopes()->where('study_id', 4)->get();

        $results = $results->groupBy(
            function ($data) {
                return $data->experiment_id."-".$data->secondary_value.'-'.$data->target;
            }
        )->reject(
            function ($datas) {
                    return count($datas) < 2;
            }
        )->map(
            function ($duplicateData) {
                        return $duplicateData->pluck('id')->first();
            }
        );

        $this->info(sprintf('%d duplicated results found', $results->count()));

        $this->info(
            sprintf(
                '%d result data deleted',
                ResultData::withoutGlobalScopes()
                ->whereIn('id', $results->toArray())
                ->delete()
            )
        );
    }
}
