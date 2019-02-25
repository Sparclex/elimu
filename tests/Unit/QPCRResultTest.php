<?php

namespace Tests\Unit;

use App\Experiments\QPCR;
use Facades\Tests\Setup\ResultFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QPCRResultTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_the_number_of_replicas()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withPositives(3)
            ->create();

        $this->assertEquals(6, $results->count());
        $this->assertEquals(2, $results->first()->replicas);
    }

    /** @test */
    public function it_has_a_standard_deviation()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withPositives(1)
            ->create();

        $this->assertNotNull($results->first()->stddev);
        $this->assertEquals(0, $results->first()->stddev);
    }

    /** @test */
    public function it_has_a_high_standard_deviation()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_STDDEV)
            ->create();

        $this->assertNotNull($results->first()->stddev);
        $this->assertEquals(20.5, $results->first()->stddev);
    }

    /** @test */
    public function it_has_the_number_of_positives()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withPositives(3)
            ->create();

        $this->assertEquals(2, $results->first()->positives);
    }

    /** @test */
    public function it_has_the_number_of_negatives()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withNegatives(3)
            ->create();

        $this->assertEquals(0, $results->first()->positives);
        $this->assertEquals(2, $results->first()->replicas);
    }

    /** @test */
    public function it_recognizes_missing_replicas()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_REPLICAS)
            ->create();

        $this->assertEquals(1, $results->first()->replicas);
    }

    protected function getParameters()
    {
        return collect([
            [
                'target' => 'pfvarts',
                'minvalues' => 2,
                'cutoff' => 42,
            ],
            [
                'target' => 'fluor',
                'minvalues' => 2,
                'cutoff' => 40,
            ]
        ]);
    }
}