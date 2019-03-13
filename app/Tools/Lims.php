<?php

namespace App\Tools;

use Laravel\Nova\Tool;

class Lims extends Tool
{
    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('sidebar');
    }
}
