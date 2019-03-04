<?php

namespace App\Models;

use App\Models\Scopes\OnlyCurrentStudy;
use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StorageSize extends Model
{
    use SetUserStudyOnSave;

    protected $table = 'storage_box_sizes';

    protected $fillable = ['sample_type_id', 'study_id', 'size'];

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }
}
