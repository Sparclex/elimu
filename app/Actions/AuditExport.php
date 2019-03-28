<?php

namespace App\Actions;

use Laravel\Nova\Nova;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class AuditExport extends DownloadExcel
{
    public function query()
    {
        return parent::query()->with('study', 'user');
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->user->name,
            sprintf('%s %s', $row->event, Nova::resourceForModel($row->auditable_type)::singularLabel()),
            $row->created_at,
            $row->old_values,
            $row->new_values,
            $row->ip_address,
            $row->user_agent,
        ];
    }
}
