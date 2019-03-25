<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DefinitionFileController extends Controller
{
    public function template($resultType)
    {
        $path = sprintf(
            'assay-definition-file-templates/%s',
            Str::snake(Str::lower($resultType)).".xlsx"
        );

        if (! Storage::exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }
}
