<?php

namespace Tests\Setup;

use App\Models\Assay;
use App\Models\AssayDefinitionFile;
use App\Models\Experiment;

class ExperimentFactory
{
    public $sampleIds;

    public $type;

    public $parameters;

    public function withSamples($samples)
    {
        if (!is_array($samples)) {
            $samples = [$samples];
        }

        $this->sampleIds = $samples;

        return $this;
    }

    public function qpcrType()
    {
        $this->type = 'qPcr Rdml';

        return $this;
    }

    public function withParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function create()
    {
        Experiment::unsetEventDispatcher();

        $experiment = factory(Experiment::class)->create([
            'result_file' => 'results/result.rdml',
            'assay_id' => factory(Assay::class)->create([
                'assay_definition_file_id' => factory(AssayDefinitionFile::class)->create([
                    'parameters' => $this->parameters,
                    'result_type' => $this->type
                ])->id
            ])->id
        ]);

        if (count($this->sampleIds) > 0) {
            $experiment->samples()->createMany(
                array_map(function ($sampleId) {
                    return [
                        'sample_id' => $sampleId
                    ];
                }, $this->sampleIds)
            );
        }

        return $experiment;
    }
}