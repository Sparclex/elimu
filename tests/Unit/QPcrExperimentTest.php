<?php

namespace Tests\Unit;

use App\Exceptions\ExperimentException;
use App\Experiments\QPCR;
use Facades\Tests\Setup\ExperimentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QPcrExperimentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_extract_samples()
    {
        $this->signInScientist();

        $experiment = ExperimentFactory::withSamples('1179588')
            ->qpcrType()
            ->withParameters($this->getParameters())
            ->create();

        $handler = new QPCR(
            base_path('tests/stubs/rdmls/valid.xml'),
            $experiment->assay->definitionFile->parameters->keyBy('target')
        );

        $handler->ignore(
            [
                '6179592',
                '7179593',
                '8179594',
                '9179595',
                '1179596',
                '5176792',
                '5176801',
                '9176805',
            ]
        );

        $this->assertEquals(
            [
                '1179588',
            ],
            $handler->extractSamplesIds()->toArray()
        );

        $handler->validate();

        $this->assertNotNull($handler->getDatabaseData($experiment)
            ->firstWhere('target', 'hsrnasep'));

        $this->assertEquals(
            24.84,
            $handler->getDatabaseData($experiment)
                ->firstWhere('target', 'hsrnasep')['primary_value'],
            '',
            0.001
        );
    }

    public function getParameters()
    {
        return [
            [
                'target' => 'pfvarats',
                'cutoff' => '42',
                'negctrl' => 'null',
                'posctrl' => 'cutoff',
                'ntc' => 'null',
            ],
            [
                'target' => 'pspp18s',
                'cutoff' => '42',
                'negctrl' => 'null',
                'posctrl' => 'cutoff',
                'ntc' => 'null',
            ],
            [
                'target' => 'hsrnasep',
                'cutoff' => '42',
                'negctrl' => 'cutoff',
                'posctrl' => '',
                'ntc' => '',
            ]
        ];
    }

    /** @test */
    public function it_should_fail_for_invalid_controls()
    {
        $this->signInScientist();

        $experiment = ExperimentFactory::withSamples('1179588')
            ->qpcrType()
            ->withParameters($this->getParameters())
            ->create();

        $handler = new QPCR(
            base_path('tests/stubs/rdmls/invalid.xml'),
            $experiment->assay->definitionFile->parameters->keyBy('target')
        );

        $this->expectException(ExperimentException::class);

        $handler->validate();
    }
}
