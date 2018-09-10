<?php

namespace App\ResultHandlers;

use Illuminate\Support\Facades\Storage;

ini_set("auto_detect_line_endings", true);

class CsvResultHandler extends ResultHandler
{
    public function handle()
    {
        if (! ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        return $this->csv_to_array(Storage::disk('public')->path($this->filename));
    }

    public function csv_to_array($filename, $delimiter = ',')
    {
        if (! file_exists($filename) || ! is_readable($filename)) {
            return false;
        }
        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (! $header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }
}
