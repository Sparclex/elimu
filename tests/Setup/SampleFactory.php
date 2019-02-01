<?php

namespace Tests\Setup;

use App\Models\Sample;
use App\Models\Study;
use App\User;
use Facades\Tests\Setup\StudyFactory;

class SampleFactory
{

    /**
     * @var Study
     */
    public $study;

    public function forManager($manager)
    {
        $this->study = $manager->study ?? StudyFactory::withManager($manager)->create();

        return $this;
    }

    public function create($attributes = [])
    {
        return factory(Sample::class)
            ->create(array_merge([
                'study_id' => $this->study ?? factory(Study::class)
            ], $attributes));
    }

    public function raw()
    {
        return factory(Sample::class)
            ->raw([
                'study_id' => $this->study ?? factory(Study::class)
            ]);
    }
}