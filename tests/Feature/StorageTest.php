<?php

namespace Tests\Feature;

use Facades\Tests\Setup\StorageFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_show_plates_as_pages()
    {
        $storage = StorageFactory::withMonitor($this->signIn())->create();
        $storage->study->sampleTypes()->attach($storage->sample_type_id, ['rows' => 10, 'columns' => 10]);

        $this->getJson("/nova-vendor/lims/storage/{$storage->sample_type_id}")
            ->assertSuccessful()
            ->assertJsonFragment(
                [
                    'sample_id' => $storage->sample->sample_id,
                    'id' => $storage->sample->id,
                    'shipped' => false,
                ]

            );
    }
}