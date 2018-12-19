<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SampleInformation extends Model implements AuditableContract
{
    use DependsOnStudy, Auditable;

    protected $dates = [
        'created_at',
        'updated_at',
        'collected_at',
        'birthdate'
    ];

    protected $fillable = [
        'sample_id',
        'subject_id',
        'collected_at',
        'visit_id',
        'study_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function samples()
    {
        return $this->hasMany(Sample::class);
    }
}
