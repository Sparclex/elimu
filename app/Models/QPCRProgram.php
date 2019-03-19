<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class QPCRProgram extends Model implements AuditableContract
{
    use SetUserStudyOnSave, Auditable;

    protected $table = 'qpcr_programs';

    protected $casts = [
        'program' => 'collection',
        'detection_table' => 'collection',
    ];

    public function assays()
    {
        return $this->belongsToMany(
            Assay::class,
            'assay_qpcr_program',
            'qpcr_program_id'
        );
    }
}
