<?php

namespace App\Importer;

use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BasicImporter extends \Sparclex\NovaImportCard\BasicImporter implements WithChunkReading
{
    private static $dates = ['expires_at'];

    public function model(array $row)
    {
        $model = new $this->modelClass();

        foreach ($row as $key => $value) {
            $model->{$key} = in_array($key, self::$dates) ? Date::excelToDateTimeObject($value) : $value;
        }

        return $model;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
