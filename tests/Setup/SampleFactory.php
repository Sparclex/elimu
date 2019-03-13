<?php

namespace Tests\Setup;

use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use Facades\Tests\Setup\StudyFactory;

class SampleFactory
{

    /**
     * @var Study
     */
    public $study;

    public $types = [];

    public function forManager($manager)
    {
        $this->study = $manager->study ?? StudyFactory::withManager($manager)->create();

        return $this;
    }

    public function withType(SampleType $type)
    {
        $this->types[] = $type;

        return $this;
    }

    public function create($attributes = [])
    {
        $sample = factory(Sample::class)
            ->create(array_merge([
                'study_id' => $this->study ?? factory(Study::class)
            ], $attributes));

        if (count($this->types)) {
            $sample->sampleTypes()->attach(collect($this->types)->pluck('id'));
        }

        return $sample;
    }

    public function raw()
    {
        return factory(Sample::class)
            ->raw([
                'study_id' => $this->study ?? factory(Study::class)
            ]);
    }
}