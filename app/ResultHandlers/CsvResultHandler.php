<?php

namespace App\ResultHandlers;

use App\CSVReader;
use Illuminate\Support\Facades\Storage;

class CsvResultHandler extends ResultHandler
{
    public function handle()
    {

        return CSVReader::make(Storage::disk('public')->path($this->filename))->toArray();
    }
}
