<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use OwenIt\Auditing\Models\Audit as AuditModel;

class Audit extends AuditModel
{
    use SetUserStudyOnSave;
}
