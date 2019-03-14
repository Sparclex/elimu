<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Assay extends Model implements AuditableContract
{
    use Auditable, SetUserStudyOnSave;

    protected $casts = [
        'parameters' => 'collection'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->creator_id = Person::firstOrCreate(['name' => auth()->user()->name])->id;
        });
    }

    public function qpcrProgram()
    {
        return $this->belongsTo(QPCRProgram::class, 'qpcr_program_id');
    }

    public function creator()
    {
        return $this->belongsTo(Person::class);
    }

    public function controls()
    {
        return $this->belongsToMany(Control::class);
    }

    public function reagent()
    {
        return $this->belongsTo(Reagent::class);
    }

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

    public function oligos()
    {
        return $this->belongsToMany(Oligo::class)
            ->withPivot('concentration');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
