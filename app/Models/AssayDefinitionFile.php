<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Factories\ReaderFactory;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssayDefinitionFile extends Model implements AuditableContract
{
    use Auditable, DependsOnStudy;

    protected static function boot()
    {
        parent::boot();
    }

    protected $casts = [
        'parameters' => 'collection'
    ];

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value;

        if (!file_exists(storage_path('app/' .$value))) {
            return;
        }

        $reader = IOFactory::createReader(Excel::XLSX);
        $reader->setReadDataOnly(true);
        $data = collect($reader->load(storage_path('app/'. $value))
            ->getActiveSheet()
            ->toArray())
            ->filter(function ($row) {
                return array_filter($row);
            });

        $this->parameters = $data
            ->except(0)
            ->map(function ($row) use ($data) {
                $combined = array_combine($data->first(), $row);
                ksort($combined);
                return $combined;
            })
            ->values();
    }
}
