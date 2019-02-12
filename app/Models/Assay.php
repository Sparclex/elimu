<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Assay extends Model implements AuditableContract
{
    use Auditable, DependsOnStudy;

    protected $casts = [
        'parameters' => 'collection'
    ];

    public function definitionFile()
    {
        return $this->belongsTo(AssayDefinitionFile::class, 'assay_definition_file_id');
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    public function primerMix()
    {
        return $this->belongsTo(PrimerMix::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
