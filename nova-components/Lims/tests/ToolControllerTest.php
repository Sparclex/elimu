<?php

namespace Sparclex\Lims\Tests;

use Sparclex\Lims\Http\Controllers\ToolController;
use Sparclex\Lims\Lims;
use Symfony\Component\HttpFoundation\Response;

class ToolControllerTest extends TestCase
{
    /** @test */
    public function it_can_can_return_a_response()
    {
        $this
            ->get('nova-vendor/sparclex/Lims/endpoint')
            ->assertSuccessful();
    }
}
