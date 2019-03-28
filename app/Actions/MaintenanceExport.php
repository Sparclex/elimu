<?php

namespace App\Actions;

use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class MaintenanceExport extends DownloadExcel
{
    public function query()
    {
        return parent::query()->with('instrument', 'technician');
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->created_at,
            $row->instrument->name,
            $row->technician->name,
            $row->procedure,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
