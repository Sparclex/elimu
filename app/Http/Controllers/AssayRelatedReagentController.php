<?php

namespace App\Http\Controllers;

use App\Models\Assay;

class AssayRelatedReagentController extends Controller
{
    public function handle(Assay $assay)
    {
        return $assay->reagents;
    }
}
