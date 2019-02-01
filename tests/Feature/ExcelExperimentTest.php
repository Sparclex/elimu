<?php

namespace Tests\Feature;

use App\Models\Assay;
use App\Models\Experiment;
use App\Models\Result;
use App\Models\ResultData;
use App\Models\Sample;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelExperimentTest extends TestCase
{
    use RefreshDatabase;

    private const RESOURCE_URI = '/nova-api/experiments';

    /** @test */
    public function result_of_a_excel_file_are_extracted()
    {
        $this->withoutExceptionHandling([ValidationException::class]);

        $user = $this->signInScientist();

        $experiment = factory(Experiment::class)->create([
            'study_id' => $user->study_id,
            'assay_id' => factory(Assay::class)
                ->create([
                    'result_type' => 'Excel',
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
                    'result_file' => $this->createTmpFile('excel/valid.xlsx')
                ]
            ))
            ->assertSuccessful();

        $this->assertEquals(2, Result::where('sample_id', $sample->id)->count());

        $this->assertEquals(3, ResultData::count());

        $this->assertDatabaseHas('result_data', [
            'experiment_id' => $experiment->id,
            'primary_value' => 1,
            'secondary_value' => 'Bagamoyo',
            'status' => 1
        ]);

        $this->assertDatabaseHas('result_data', [
            'experiment_id' => $experiment->id,
            'primary_value' => 'Negative',
            'secondary_value' => 'Basel',
            'status' => 1
        ]);

        $this->assertDatabaseHas('result_data', [
            'experiment_id' => $experiment->id,
            'primary_value' => 'Negative',
            'secondary_value' => 'Basel',
            'status' => 1
        ]);
    }
}
