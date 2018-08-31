<?php

namespace Sparclex\Lims;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class Tool extends BaseTool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        dump('in tool boot');
        Nova::script('Lims', __DIR__.'/../dist/js/tool.js');
        Nova::style('Lims', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('Lims::navigation');
    }
}
