<?php

namespace Tests\Feature;

use App\Experiments\QPCR;
use Facades\Tests\Setup\ResultFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_results()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withPositives(2)
            ->withNegatives(2)
            ->withError(QPCR::ERROR_REPLICAS)
            ->create();

        $this->get("/nova-vendor/lims/results/{$results->first()->assay_id}")
            ->assertSuccessful();
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

    /** @test */
    public function it_returns_available_targets()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_STDDEV)
            ->create();

        $this->get("/nova-vendor/lims/results/{$results->first()->assay_id}/targets")
            ->assertJson([
                'pfvarts'
            ]);
    }
}
