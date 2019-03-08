<?php

namespace Tests\Unit;

use App\Models\Queries\StoragePlates;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Shipment;
use App\Support\StoragePointer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_show_all_position_on_an_empty_plate()
    {
        $user = $this->signInMonitor();
        $user->study->sampleTypes()->save($type = factory(SampleType::class)->create(
            ['rows' => 2, 'columns' => 2]
        ));

        $this->assertEquals([
                0 => [
                    0 => [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => null,
                    ],
                    1 => [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => null,
                    ],
                ],
                1 => [
                    0 => [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => null,
                    ],
                    1 => [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => null,
                    ],
                ]
            ]
            , StoragePlates::get($type));
    }

    /** @test */
    public function it_should_show_a_filled_plate()
    {
        $user = $this->signInMonitor();
        $user->study->sampleTypes()->save($type = factory(SampleType::class)->create(['rows' => 2, 'columns' => 1]));
        $sample1 = factory(Sample::class)->create();
        $quantity = 5;
        $sample1->sampleTypes()->attach($type->id, compact('quantity'));

        $storagePointer = new StoragePointer($type->id, $user->study_id);
        $storagePointer->store($sample1, $quantity);


        $this->assertEquals([
                0 => [
                    0 => [
                        'id' => $sample1->id,
                        'sample_id' => $sample1->sample_id,
                        'shipped' => null,
                    ],
                ],
                1 => [
                    0 => [
                        'id' => $sample1->id,
                        'sample_id' => $sample1->sample_id,
                        'shipped' => null,
                    ]
                ]
            ]
            , StoragePlates::get($type));

        $this->assertEquals([
                0 => [
                    0 => [
                        'id' => $sample1->id,
                        'sample_id' => $sample1->sample_id,
                        'shipped' => null,
                    ],
                ],
                1 => [
                    0 => [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => null,
                    ]
                ]
            ]
            , StoragePlates::get($type, 3));
    }

    /** @test */
    public function it_should_show_shipped_samples()
    {
        $this->markTestSkipped('Not yet implemented');

        $user = $this->signInMonitor();
        $user->study->sampleTypes()->attach($type = factory(SampleType::class)->create(), ['rows' => 2, 'columns' => 1]);

        $storagePointer = new StoragePointer($type->id, $user->study_id);
        $storagePointer->store($sample1 = factory(Sample::class)->create(), 5);
        $sample1->shipments()->save($shipment = factory(Shipment::class)->create(), ['quantity' => 3]);


        $this->assertEquals([
                0 => [
                    0 => [
                        'id' => $sample1->id,
                        'sample_id' => $sample1->sample_id,
                        'shipped' => [
                            'id' => $shipment->id,
                            'title' => sprintf('%s (%s)', $shipment->recipient, $shipment->shipment_date)
                        ],
                    ],
                ],
                1 => [
                    0 => [
                        'id' => $sample1->id,
                        'sample_id' => $sample1->sample_id,
                        'shipped' => [
                            'id' => $shipment->id,
                            'title' => sprintf('%s (%s)', $shipment->recipient, $shipment->shipment_date)
                        ],
                    ]
                ]
            ]
            , StoragePlates::get($type));

        $this->assertEquals([
                0 => [
                    0 => [
                        'id' => $sample1->id,
                        'sample_id' => $sample1->sample_id,
                        'shipped' => [
                            'id' => $shipment->id,
                            'title' => sprintf('%s (%s)', $shipment->recipient, $shipment->shipment_date)
                        ],
                    ],
                ],
                1 => [
                    0 => [
                        'id' => null,
                        'sample_id' => null,
                        'shipped' => null,
                    ]
                ]
            ]
            , StoragePlates::get($type, 2));
    }
}
