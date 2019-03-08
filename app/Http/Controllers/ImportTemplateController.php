<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportTemplateController extends Controller
{
    private $importTemplates = [
        'samples',
        'oligos',
        'reagents',
        'controls',
    ];

    public function show($resourceName, Request $request)
    {
        if (!in_array($resourceName, $this->importTemplates)) {
            return [
                'exists' => false,
                'template_uri' => ''
            ];
        }

        return [
            'exists' => true,
            'template_uri' => '/nova-vendor/lims/import-template/' . $resourceName . '/download'
        ];
    }

    public function download($resourceName)
    {
        if (!in_array($resourceName, $this->importTemplates)) {
            abort(404);
        }

        return Storage::download('import-templates/' . $resourceName . '.xlsx', $resourceName . '-template.xlsx');
    }
}
