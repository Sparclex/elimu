<?php

namespace App\Utility;

class CSVReader
{
    private $filename;

    public function __construct($filename)
    {

        ini_set("auto_detect_line_endings", true);
        $this->filename = $filename;
    }

    public static function make($filename)
    {
        return new self($filename);
    }

    public function toArray($delimiter = ',')
    {
        if (!file_exists($this->filename) || !is_readable($this->filename)) {
            return false;
        }
        $header = null;
        $data = [];
        if (($handle = fopen($this->filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if ([null] === $row) {
                    continue;
                }
                if (!$header) {
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
