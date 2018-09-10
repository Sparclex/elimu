<?php

namespace App\ResultHandlers;

use App\RdmlManager;
use Illuminate\Support\Facades\Storage;

class RdmlResultHandler extends ResultHandler
{
    public function handle()
    {
        try {
            $rdmlManager = new RdmlManager(
                Storage::disk('public')->get($this->filename), [
                'Pspp18S' => 100,
                'HsRNaseP' => 100,
                'PfvarATS' => 200,
            ]);

            return $rdmlManager->getChartData();
        } catch (\Exception $e) {
            return [];
        }
    }
}
