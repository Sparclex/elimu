<?php

namespace Sparclex\Lims\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Sparclex\Lims\Models\Sample;
use Sparclex\Lims\Models\Storage;

class AutoStoreTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_create_storage_position_for_a_new_sample()
    {
        $sample = factory(Sample::class)->make(['quantity' => 1]);
        $sample->save();
        $this->assertDatabaseHas('storage', [
            'sample_id' => $sample->id,
            'study_id' => $sample->study_id,
            'sample_type_id' => $sample->sample_type_id
        ]);
    }
}
