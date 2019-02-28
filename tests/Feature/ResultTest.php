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
            ->assertJsonFragment([
                [
                    'ID' => $results->first()->id,
                    'Sample ID' => $results->first()->sample->sample_id,
                    'Target' => 'PFvarts',
                    'Result' => 'Positive',
                    'Error' => null
                ]
            ])
            ->assertJsonFragment([
                [
                    'ID' => $results->last()->id,
                    'Sample ID' => $results->last()->sample->sample_id,
                    'Target' => $this->getParameters()->pluck('target')->last(),
                    'Result' => null,
                    'Error' => 'Not enough values'
                ]
            ]);
    }

    /** @test */
    public function it_shows_missing_replication()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_REPLICAS)
            ->create();

        $this->get("/nova-vendor/lims/results/{$results->first()->assay_id}")
            ->assertJson([
                [
                    'ID' => $results->first()->id,
                    'Sample ID' => $results->first()->sample->sample_id,
                    'Target' => 'PFvarts',
                    'Result' => null,
                    'Error' => 'Not enough values'
                ]
            ]);
    }

    /** @test */
    public function it_shows_inconsistent_values()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_REPEAT)
            ->create();

        $this->get("/nova-vendor/lims/results/{$results->first()->assay_id}")
            ->assertJson([
                [
                    'ID' => $results->first()->id,
                    'Sample ID' => $results->first()->sample->sample_id,
                    'Target' => 'PFvarts',
                    'Result' => null,
                    'Error' => 'Needs repetition'
                ]
            ]);
    }

    /** @test */
    public function it_shows_too_high_standard_deviation()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_STDDEV)
            ->create();

        $this->get("/nova-vendor/lims/results/{$results->first()->assay_id}")
            ->assertJson([
                [
                    'ID' => $results->first()->id,
                    'Sample ID' => $results->first()->sample->sample_id,
                    'Target' => 'PFvarts',
                    'Result' => null,
                    'Error' => 'Standard deviation too high'
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
                'PFvarts'
            ]);
    }

    /** @test */
    public function it_filters_by_target()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withPositives(2)
            ->create();

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'target' => 'PFvarts',
        ])
            ->assertJsonFragment(
                [
                    'Target' => 'PFvarts',
                ]
            )
            ->assertJsonMissing(
                [
                    'Target' => 'PZZZAB',
                ]
            );
    }

    /** @test */
    public function it_filters_by_status()
    {
        $this->signInScientist();

        $results = ResultFactory::withParameters($this->getParameters())
            ->withError(QPCR::ERROR_REPEAT)
            ->withError(QPCR::ERROR_STDDEV)
            ->withError(QPCR::ERROR_REPLICAS)
            ->withPositives(1)
            ->withNegatives(1)
            ->create();

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'valid',
        ])
            ->assertJsonMissing(['Error' => 'Standard deviation too high'])
            ->assertJsonMissing(['Error' => 'Needs repetition'])
            ->assertJsonMissing(['Error' => 'Not enough values'])
            ->assertJsonFragment(['Error' => null,])
            ->assertJsonFragment(['Result' => 'Positive'])
            ->assertJsonFragment(['Result' => 'Negative']);


        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'errors',
        ])
            ->assertJsonFragment(['Error' => 'Standard deviation too high'])
            ->assertJsonFragment(['Error' => 'Needs repetition'])
            ->assertJsonFragment(['Error' => 'Not enough values'])
            ->assertJsonMissing(['Error' => null]);

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'repetition',
        ])
            ->assertJsonMissing(['Error' => 'Standard deviation too high'])
            ->assertJsonFragment(['Error' => 'Needs repetition'])
            ->assertJsonMissing(['Error' => 'Not enough values'])
            ->assertJsonMissing(['Error' => null]);

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'stdev',
        ])
            ->assertJsonFragment(['Error' => 'Standard deviation too high'])
            ->assertJsonMissing(['Error' => 'Needs repetition'])
            ->assertJsonMissing(['Error' => 'Not enough values'])
            ->assertJsonMissing(['Error' => null]);

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'replicates',
        ])
            ->assertJsonMissing(['Error' => 'Standard deviation too high'])
            ->assertJsonMissing(['Error' => 'Needs repetition'])
            ->assertJsonFragment(['Error' => 'Not enough values'])
            ->assertJsonMissing(['Error' => null]);

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'positive',
        ])
            ->assertJsonMissing(['Error' => 'Standard deviation too high'])
            ->assertJsonMissing(['Error' => 'Needs repetition'])
            ->assertJsonMissing(['Error' => 'Not enough values'])
            ->assertJsonFragment(['Result' => 'Positive'])
            ->assertJsonMissing(['Result' => 'Negative']);

        $this->call('GET', "/nova-vendor/lims/results/{$results->first()->assay_id}", [
            'status' => 'negative',
        ])
            ->assertJsonMissing(['Error' => 'Standard deviation too high'])
            ->assertJsonMissing(['Error' => 'Needs repetition'])
            ->assertJsonMissing(['Error' => 'Not enough values'])
            ->assertJsonMissing(['Result' => 'Positive'])
            ->assertJsonFragment(['Result' => 'Negative']);

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
}
