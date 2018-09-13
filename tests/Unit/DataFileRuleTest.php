<?php

namespace Tests\Unit;

use App\Rules\DataFile;
use Illuminate\Http\File;
use Tests\TestCase;

class DataFileRuleTest extends TestCase
{
    /**
     * @var \Illuminate\Contracts\Validation\Rule
     */
    protected $rule;

    public function setUp()
    {
        parent::setUp();

        $this->rule = new DataFile();
    }

    /**
     * @test
     */
    public function it_should_accept_a_valid_rdml()
    {

        $this->assertTrue($this->rule->passes('test', new File($this->resource('valid-rdml.rdml'))));
    }

    /**
     * @test
     */
    public function it_should_accept_a_valid_csv()
    {

    }

    /**
     * @test
     */
    public function it_should_decline_a_rdml_with_not_matching_samples()
    {

    }

    /**
     * @test
     */
    public function it_should_decline_a_csv_with_not_matching_samples()
    {

    }

    /**
     * @test
     */
    public function it_should_decline_an_invalid_rdml()
    {

    }

    public function it_should_decline_an_invalid_csv()
    {

    }
}
