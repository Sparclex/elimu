<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use SetUserStudyOnSave;

    protected $table = 'storage';

    protected $fillable = ['study_id', 'sample_type_id', 'sample_id', 'box', 'position'];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }
}
