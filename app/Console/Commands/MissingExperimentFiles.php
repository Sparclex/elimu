<?php

namespace App\Console\Commands;

use App\Models\Experiment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MissingExperimentFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elimu:files:missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays all experiments with a missing result file';

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
        if (! file_exists(storage_path('app/experiments'))) {
            $this->error('There are no experiment files therefore all result files are missing.');

            return;
        }
        $experiments = Experiment::withoutGlobalScopes()
            ->whereNotNull('result_file')
            ->get()
            ->reject(function ($experiment) {
                return Storage::disk('local')->exists($experiment->result_file);
            })
            ->each(function ($experiment) {
                $this->line(sprintf('No file for experiment %d %s', $experiment->id, $experiment->name));
            });

        $this->line("");
        $this->line(sprintf('%d missing result files', $experiments->count()));
    }
}
