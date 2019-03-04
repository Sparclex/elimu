<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Unique;

class StudyUnique extends Unique
{
    public function __construct(string $table, string $column = 'NULL')
    {
        parent::__construct($table, $column);

        $this->where('study_id', auth()->user()->study_id);
    }
}
