<?php

namespace App\Tools;


use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class SampleBatchImporter extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('Lims', public_path('tools/sample-batch-importer/js/tool.js'));
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('tools.sample-batch-importer
        ');
    }
}
