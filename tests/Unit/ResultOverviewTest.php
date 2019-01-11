<?php

namespace Tests\Unit;

use App\Models\Assay;
use App\Models\Result;
use App\Models\ResultData;
use App\Models\Sample;
use App\Queries\AnalyzedResults;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResultOverviewTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function validResults()
    {
        return [
            [
                [
                    'Pspp18S' => [
                        'expected' => [
                            'positive_count' => 2,
                            'negative_count' => 0,
                            'mean' => 5.5,
                            'stddev' => 0.70710678118655,
                            'quantitative' => pow(10, -0.29089999999999999 * 5.5
                                + 10.27),
                        ],
                        'primaryValues' => [5, 6]
                    ],
                    'PfvarATS' => [
                        'expected' => [
                            'positive_count' => 2,
                            'negative_count' => 0,
                            'mean' => 4,
                            'stddev' => 1.4142135623731,
                            'quantitative' => pow(10, -0.29160000000000003 * 4
                                + 9.5549999999999997),
                        ],
                        'primaryValues' => [3, 5]
                    ],
                    'HsRNaseP' => [
                        'expected' => [
                            'positive_count' => 2,
                            'negative_count' => 0,
                            'mean' => 2,
                            'stddev' => 1.4142135623731,
                            'quantitative' => null,
                        ],
                        'primaryValues' => [1, 3]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validResults
     */
    public function it_evaluates_valid_results($targets)
    {
        $this->signInMonitor();

        $assay = factory(Assay::class)->create([
            'parameters' => json_decode($this->stubContent('assay-definition.json'), true),
            'result_type' => 'qPcr Rdml'
        ]);
        $sample = factory(Sample::class)->create();
        foreach ($targets as $target => $targetValues) {
            $result = factory(Result::class)->create([
                'sample_id' => $sample->id,
                'target' => $target,
                'study_id' => Auth::user()->study_id,
                'assay_id' => $assay->id
            ]);

            factory(ResultData::class)->create([
                'primary_value' => $targetValues['primaryValues'][0],
                'result_id' => $result->id
            ]);

            factory(ResultData::class)->create([
                'primary_value' => $targetValues['primaryValues'][1],
                'result_id' => $result->id
            ]);
        }


        $samples = (new AnalyzedResults())->get($assay);

        $this->assertInstanceOf(Collection::class, $samples);
        $this->assertEquals(1, $samples->count());
        $sample = $samples->first();

        foreach ($targets as $target => $targetValues) {
            foreach ($targetValues['expected'] as $expectedKey => $expectedValue) {
                $this->assertEquals($expectedValue, $sample->{$target . '_' . $expectedKey},
                    $target . '.' . $expectedKey . ' does not match', 0.001);
            }
        }
    }

    public function invalidResults()
    {
        return [
            [
                [
                    'Pspp18S' => [
                        'expected' => [
                            'positive_count' => 1,
                            'negative_count' => 1,
                        ],
                        'primaryValues' => [5, null]
                    ],
                    'PfvarATS' => [
                        'expected' => [
                            'positive_count' => 1,
                            'negative_count' => 1,
                        ],
                        'primaryValues' => [8, null]
                    ],
                    'HsRNaseP' => [
                        'expected' => [
                            'positive_count' => 1,
                            'negative_count' => 1,
                        ],
                        'primaryValues' => [4, null]
                    ]
                ]
            ],
            [
                [
                    'Pspp18S' => [
                        'expected' => [
                            'positive_count' => 0,
                            'negative_count' => 2,
                        ],
                        'primaryValues' => [43, 43]
                    ],
                    'PfvarATS' => [
                        'expected' => [
                            'positive_count' => 2,
                            'negative_count' => 0,
                        ],
                        'primaryValues' => [0.00001, 42]
                    ],
                    'HsRNaseP' => [
                        'expected' => [
                            'positive_count' => 0,
                            'negative_count' => 2,
                        ],
                        'primaryValues' => [null, null]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider invalidResults
     */
    public function it_evaluated_invalid_results($targets)
    {
        $this->signInMonitor();

        $assay = factory(Assay::class)->create([
            'parameters' => json_decode($this->stubContent('assay-definition.json'), true),
            'result_type' => 'qPcr Rdml'
        ]);
        $sample = factory(Sample::class)->create();
        foreach ($targets as $target => $targetValues) {
            $result = factory(Result::class)->create([
                'sample_id' => $sample->id,
                'target' => $target,
                'study_id' => Auth::user()->study_id,
                'assay_id' => $assay->id
            ]);

            foreach ($targetValues['primaryValues'] as $primaryValue) {
                factory(ResultData::class)->create([
                    'primary_value' => $primaryValue,
                    'result_id' => $result->id
                ]);
            }
        }

        $samples = (new AnalyzedResults())->get($assay);

        $sample = $samples->first();

        foreach ($targets as $target => $targetValues) {
            foreach ($targetValues['expected'] as $expectedKey => $expectedValue) {
                $this->assertEquals($expectedValue, $sample->{$target . '_' . $expectedKey},
                    $target . '.' . $expectedKey . ' does not match', 0.001);
            }
        }
    }

    /**
     * @test
     * @dataProvider validResults
     */
    public function it_ignores_not_accepted_results($targets)
    {
        $this->signInMonitor();

        $assay = factory(Assay::class)->create([
            'parameters' => json_decode($this->stubContent('assay-definition.json'), true),
            'result_type' => 'qPcr Rdml'
        ]);
        $sample = factory(Sample::class)->create();
        foreach ($targets as $target => $targetValues) {
            $result = factory(Result::class)->create([
                'sample_id' => $sample->id,
                'target' => $target,
                'study_id' => Auth::user()->study_id,
                'assay_id' => $assay->id
            ]);

            foreach ($targetValues['primaryValues'] as $primaryValue) {
                factory(ResultData::class)->create([
                    'primary_value' => $primaryValue,
                    'result_id' => $result->id,
                    'status' => 0
                ]);
            }
        }

        $samples = (new AnalyzedResults())->get($assay);

        $this->assertInstanceOf(Collection::class, $samples);
        $this->assertEmpty($samples);
    }

    protected function createValidExperimentResult($assay, $quantity)
    {
        return $this->createExperimentResult($assay, $quantity, 'valid');
    }

    protected function createInvalidExperimentResult($assay, $quantity)
    {
        return $this->createExperimentResult($assay, $quantity, 'invalid');
    }

    protected function createExperimentResult($assay, $quantity, $type)
    {

        $results = factory(Result::class, $quantity)->create([
            'target' => $assay->parameters->pluck('target')[0],
            'study_id' => Auth::user()->study_id,
            'assay_id' => $assay->id
        ]);

        foreach ($results as $result) {
            $this->createResultDataFor($result,
                $this->faker->randomElement(
                    $type == 'invalid' ?
                        ['minvalues', 'cuttoffstdev', 'unequal results']
                        : ['valid']));
        }
        return $results;
    }

    protected function createResultDataFor(Result $result, $type = 'valid')
    {
        switch ($type) {
            case 'minvalues': // only add one instead of the minimum of 2
                factory(ResultData::class)->create(['result_id' => $result->id]);
                break;
            case 'cuttoffstdev': // add data for which the stdev of the cq value is greater than
                factory(ResultData::class)->create([
                    'primary_value' => '5',
                    'result_id' => $result->id
                ]);
                factory(ResultData::class)->create([
                    'primary_value' => '10',
                    'result_id' => $result->id
                ]);
                break;
            case 'unequal results': // one result has a cq and the other not
                factory(ResultData::class)->create([
                    'primary_value' => '20',
                    'result_id' => $result->id
                ]);
                factory(ResultData::class)->create([
                    'primary_value' => null,
                    'result_id' => $result->id
                ]);
                break;
            case 'valid':
            default:
                factory(ResultData::class)->create([
                    'primary_value' => 5,
                    'result_id' => $result->id
                ]);
                factory(ResultData::class)->create([
                    'primary_value' => 6,
                    'result_id' => $result->id
                ]);
        }
    }


}
