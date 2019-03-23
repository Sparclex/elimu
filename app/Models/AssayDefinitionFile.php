<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssayDefinitionFile extends Model implements AuditableContract
{
    use Auditable, SetUserStudyOnSave;

    public function setParametersAttribute($value)
    {
        $this->attributes['parameters'] = json_encode($value);
    }

    public function getParametersAttribute($value)
    {
        if (!$value) {
            return new Collection();
        }
        $parameters = collect(json_decode($value, 1));
        return $parameters->map(function ($targetParameter) {
            $targetParameter['target'] = strtolower($targetParameter['target']);

            return $targetParameter;
        });
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    /**
     * @return \App\Experiments\ExperimentType
     */
    public function resultTypeClass()
    {
        return config('elimu.result_types.' . $this->result_type);
    }

    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value;

        if (!file_exists(storage_path('app/' . $value))) {
            return;
        }

        $reader = IOFactory::createReader(Excel::XLSX);
        $reader->setReadDataOnly(true);
        $data = collect($reader->load(storage_path('app/' . $value))
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
