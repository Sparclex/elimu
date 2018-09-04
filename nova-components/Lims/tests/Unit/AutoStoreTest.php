<?php

namespace Sparclex\Lims\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Sparclex\Lims\Models\Sample;
use Sparclex\Lims\Models\Storage;
use Sparclex\Lims\Models\StorageSize;
use Sparclex\Lims\Observers\AutoStorageSaver;
use Sparclex\Lims\Tests\TestCase;

class AutoStoreTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_create_storage_position_for_a_new_sample()
    {
        $storageSize = factory(StorageSize::class)->create();

        $sample = factory(Sample::class)->make([
            'quantity' => 1,
            'sample_type_id' => $storageSize->sample_type_id,
            'study_id' => $storageSize->study_id,
        ]);
        $sample->save();
        $event = new AutoStorageSaver();
        $event->created($sample);
        $this->assertDatabaseHas('storage', [
            'sample_id' => $sample->id,
            'study_id' => $sample->study_id,
            'sample_type_id' => $sample->sample_type_id,
        ]);

        $storageSize = factory(StorageSize::class)->create();
        $sample = factory(Sample::class)->make([
            'quantity' => 3,
            'sample_type_id' => $storageSize->sample_type_id,
            'study_id' => $storageSize->study_id,
        ]);
        $sample->save();
        $event = new AutoStorageSaver();
        $event->created($sample);
        $storagePositions = Storage::where('sample_id', $sample->id)->get();
        $this->assertEquals(3, $storagePositions->count());
        $this->assertEquals([
            'sample_type_id' => $sample->sample_type_id,
            'sample_id' => $sample->id,
            'study_id' => $sample->study_id,
            'box' => 1,
            'position' => 3,
        ], array_except($storagePositions->last()->toArray(), ['created_at', 'updated_at', 'id']));
    }
}
