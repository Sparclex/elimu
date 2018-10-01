<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Model;

class SampleInformation extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'collected_at'
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

        static::addGlobalScope(new OnlyCurrentStudy('sample_informations'));
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
