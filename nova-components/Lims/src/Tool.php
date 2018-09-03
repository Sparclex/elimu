<?php

namespace Sparclex\Lims;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;
use Sparclex\Lims\Nova\Person;
use Sparclex\Lims\Nova\ProcessingLog;
use Sparclex\Lims\Nova\Result;
use Sparclex\Lims\Nova\Sample;
use Sparclex\Lims\Nova\SampleType;
use Sparclex\Lims\Nova\Storage;
use Sparclex\Lims\Nova\StorageSize;
use Sparclex\Lims\Nova\Study;
use Sparclex\Lims\Nova\Test;

class Tool extends BaseTool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            Person::class,
            ProcessingLog::class,
            Result::class,
            SampleType::class,
            Sample::class,
            Storage::class,
            StorageSize::class,
            Study::class,
            Test::class
        ]);
        $this->bootFields();
        Nova::script('Lims', __DIR__.'/../dist/js/tool.js');


    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        //return view('Lims::navigation');
    }

    public function bootFields() {
        $fieldDir = __DIR__.'/../dist/fields/';
        Nova::script('html-readonly', $fieldDir.'html-readonly/js/field.js');
    }
}
