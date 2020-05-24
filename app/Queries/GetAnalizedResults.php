<?php

namespace App\Queries;

use App\Models\AssayDefinitionFile;

class GetAnalizedResults
{
    public function __construct(GetQpcrResults $)
    {
    }

    public function get(AssayDefinitionFile $assayDefinitionFile) {
        switch($assayDefinitionFile->result_type) {
            case 'qPCR RDML':
                return (new GetQpcrResults())
        }
    }
}
