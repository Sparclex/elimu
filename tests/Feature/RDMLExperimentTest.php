<?php

namespace Tests\Feature;

use App\Models\Assay;
use App\Models\Experiment;
use App\Models\Result;
use App\Models\ResultData;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RDMLExperimentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const RESOURCE_URI = '/nova-api/experiments';

    /** @test */
    public function results_of_a_rdml_file_are_extracted()
    {
        $this->withoutExceptionHandling([ValidationException::class]);

        $user = $this->signInScientist();

        $experiment = factory(Experiment::class)->create([
            'study_id' => $user->study_id,
            'assay_id' => factory(Assay::class)
                ->create([
                    'result_type' => 'qPcr Rdml',
                    'parameters' => json_decode($this->stubContent('assay-definition.json'), true)
                ])->id
        ]);

        $sample = factory(Sample::class)->create([
            'sample_id' => '1179587',
            'study_id' => $user->study_id
        ]);
        $sample->experiments()
            ->attach($experiment);

        $this->putJson(self::RESOURCE_URI . "/{$experiment->id}",
            array_merge($experiment->toArray(),
                [
                    'assay' => $experiment->assay_id,
                    'sampleType' => $experiment->sample_type_id,
                    'result_file' => $this->createTmpFile('rdmls/valid.rdml')
                ]
            ))
            ->assertSuccessful();

        $this->assertDatabaseHas('results', [
            'sample_id' => $sample->id
        ]);

        $this->assertEquals(3, Result::where('sample_id', $sample->id)->count());

        $this->assertEquals(6, ResultData::count());

        $this->assertDatabaseHas('result_data', [
            'result_id' => 1,
            'experiment_id' => $experiment->id,
            'primary_value' => null,
            'secondary_value' => 'B02',
            'status' => 1
        ]);

        $this->assertDatabaseHas('result_data', [
            'result_id' => 1,
            'experiment_id' => $experiment->id,
            'primary_value' => null,
            'secondary_value' => 'C02',
            'status' => 1
        ]);

        $this->assertDatabaseHas('result_data', [
            'result_id' => 3,
            'experiment_id' => $experiment->id,
            'primary_value' => 24.73933644,
            'secondary_value' => 'B02',
            'status' => 1
        ]);
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
