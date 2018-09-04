<?php

namespace Sparclex\Lims;

use Illuminate\Support\Str;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;
use ReflectionClass;
use Sparclex\Lims\Models\Sample;
use Sparclex\Lims\Nova\Resource;
use Sparclex\Lims\Observers\AutoStorageSaver;
use Symfony\Component\Finder\Finder;

class Tool extends BaseTool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        $this->resourcesIn(__DIR__.'/Nova');
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

    public function resourcesIn($directory)
    {
        $namespace = 'Sparclex\\Lims';
        $resources = [];

        foreach ((new Finder)->in($directory)->files() as $resource) {
            $resource = $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($resource->getPathname(), __DIR__)
                );
            if (is_subclass_of($resource, Resource::class) &&
                ! (new ReflectionClass($resource))->isAbstract()) {
                $resources[] = $resource;
            }
        }

        Nova::resources(
            collect($resources)->sort()->all()
        );
    }
}
