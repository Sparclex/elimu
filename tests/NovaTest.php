<?php

namespace Tests;

use App\User;

abstract class NovaTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs(factory(User::class)->create());
    }
}
