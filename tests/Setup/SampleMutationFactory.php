<?php

namespace Tests\Setup;

use App\Models\Sample;
use App\Models\SampleMutation;
use App\Models\SampleType;
use App\Models\Study;

class SampleMutationFactory
{
    public $sample;

    public $sampleType;

    public $user;

    public function withUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function withSample($sample)
    {
        $this->sample = $sample;

        return $this;
    }

    public function withSampleType($sampleType)
    {
        $this->sampleType = $sampleType;

        return $this;
    }

    public function withoutStorage()
    {
        $this->quantity = 0;

        return $this;
    }

    public function withQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function raw()
    {
        $sample = $this->sample ?? factory(Sample::class)->create([
                'study_id' => $this->user ? $this->user->study_id : factory(Study::class)
            ]);

        return factory(SampleMutation::class)->raw([
            'sample_type_id' => $this->sampleType ?? factory(SampleType::class),
            'sample_id' => $sample->id,
            'study_id' => $sample->study_id,
            'quantity' => $this->quantity ?? 0
        ]);
    }

    public function create()
    {
        return factory(SampleMutation::class)->create($this->raw());
    }
}