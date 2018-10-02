<?php

namespace App\FileTypes;

class CSV
{
    private $filename;

    private $data;

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

    public function getData()
    {
        if (!$this->data) {
            $this->data = collect($this->toArray());
        }
        return $this->data;
    }

    public function isValid()
    {
        try {
            $data = $this->getData();
        } catch (\Exception $e) {
            return false;
        }
        if (!isset($data[0]['sample']) || !isset($data[0]['target']) || count(array_keys($data[0])) < 3) {
            return false;
        }
        return true;
    }
    public function getSamplesIds()
    {
        return $this->getData()->pluck('sample');
    }
}
