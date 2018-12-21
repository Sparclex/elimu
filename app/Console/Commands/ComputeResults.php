<?php

namespace App\Console\Commands;

use App\Models\Experiment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ComputeResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lims:results:compute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts result data from result files';

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
        $experiments = Experiment::withoutGlobalScopes()->get();

        Experiment::disableAuditing();

        Auth::loginUsingId(1);

        $originalSelectedStudy = Auth::user()->study_id;

        $command = $this;

        $experiments
        ->filter(function ($experiment) use ($command) {
            $exists = file_exists(storage_path('app/'.$experiment->result_file));

            if (!$exists) {
                $command->comment(sprintf('Experiment %d has no result file', $experiment->id));
            }

            return $exists;
        })
        ->each(function ($experiment) use ($command) {
            $command->info(sprintf('Extract data from experiment %d...', $experiment->id));

            Auth::user()->study_id = $experiment->study_id;
            Auth::user()->save();

            (new $experiment->result_handler($experiment, $experiment->result_file))->handle();
        });

        Auth::user()->study_id = $originalSelectedStudy;
        Auth::user()->save();
    }
}
