<?php

namespace Tests\Unit;

use App\Experiments\QPCR;
use App\Support\QPCRResultSpecifier;
use Facades\Tests\Setup\ResultFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QPCRResultSpecifierTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_specifies_results_positive()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withPositives(1)
            ->create();

        $this->assertEquals('Positive', $this->specifier($results)->qualitative());
    }

    protected function getParameters()
    {
        return collect([
            [
                'target' => 'PFvarts',
                'cutoff' => 42,
                'minvalues' => 2,
                'cuttoffstdev' => 5
            ],
            [
                'target' => 'PZZZAB',
                'cutoff' => 42,
                'minvalues' => 2,
                'cuttoffstdev' => 5
            ]
        ]);
    }

    protected function specifier($results)
    {
        return new QPCRResultSpecifier(
            $this->getParameters()->firstWhere('target', 'PFvarts'),
            $results->first()
        );
    }

    /** @test */
    public function it_specifies_results_negative()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withNegatives(1)
            ->create();

        $this->assertEquals('Negative', $this->specifier($results)->qualitative());
    }

    /** @test */
    public function it_specifies_result_should_be_repeated()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_REPEAT)
            ->create();

        $this->assertEquals('Needs repetition', $this->specifier($results)->qualitative());
    }

    /** @test */
    public function it_specifies_result_with_high_stddev()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_STDDEV)
            ->create();

        $this->assertEquals('Standard deviation too high', $this->specifier($results)->qualitative());
    }

    /** @test */
    public function it_specifies_result_with_not_enough_data()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_REPLICAS)
            ->create();

        $this->assertEquals('Not enough data', $this->specifier($results)->qualitative());
    }
}
