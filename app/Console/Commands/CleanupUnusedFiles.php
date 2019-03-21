<?php

namespace App\Console\Commands;

use App\Models\Experiment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupUnusedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elimu:files:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes old experiment result files';

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
        if (!file_exists(storage_path('app/experiments'))) {
            $this->info('There are no experiment files');
            return;
        }
        $files = Storage::disk('local')->files('experiments');

        $experiments = Experiment::withoutGlobalScopes()->whereIn('result_file', $files)->pluck(
            'id',
            'result_file'
        );

        $deleteFiles = [];

        foreach ($files as $file) {
            if (! isset($experiments[$file])) {
                $this->line(sprintf('No experiment for file %s', $file));
                $deleteFiles[] = $file;
            }
        }

        if ($this->choice('Delete all these files', ['Yes', 'No'], 0)) {
            Storage::disk('local')->delete($deleteFiles);
        }

        $this->info(sprintf('%d files deleted', count($deleteFiles)));
    }
}
