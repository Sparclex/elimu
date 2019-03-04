<?php

namespace Tests\Unit;

use App\Experiments\QPCR;
use Facades\Tests\Setup\ResultFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QPCRResultFilterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_filters_by_target()
    {
        $this->markTestSkipped();
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
        $this->markTestSkipped();
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

}
