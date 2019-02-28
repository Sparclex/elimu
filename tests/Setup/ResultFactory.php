<?php

namespace Tests\Setup;

use App\Experiments\QPCR;
use App\Models\Assay;
use App\Models\AssayDefinitionFile;
use App\Models\Experiment;
use App\Models\Sample;
use InvalidArgumentException;

class ResultFactory
{
    public $positives = 0;
    public $negatives = 0;
    public $parameters;
    public $errors = [
        QPCR::ERROR_REPLICAS => 0,
        QPCR::ERROR_STDDEV => 0,
        QPCR::ERROR_REPEAT => 0,
    ];

    public function withPositives($quantity = 1)
    {
        $this->positives = $quantity;

        return $this;
    }

    public function withNegatives($quantity = 1)
    {
        $this->negatives = $quantity;

        return $this;
    }

    public function withError($error)
    {
        switch ($error) {
            case QPCR::ERROR_REPLICAS:
            case QPCR::ERROR_STDDEV:
            case QPCR::ERROR_REPEAT:
                $this->errors[$error]++;
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unknown Error code %s', $error));
        }

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
                    'result_type' => 'qPcr Rdml'
                ])->id
            ])->id
        ]);

        $samples = [];

        foreach ($this->parameters->keyBy('target') as $target => $parameters) {
            $this->createData($experiment, $parameters, $samples, $this->positives);
            $this->createData($experiment, $parameters, $samples, $this->negatives, 'negative');

            foreach ($this->errors as $errorType => $quantity) {
                switch ($errorType) {
                    case QPCR::ERROR_REPLICAS:
                        $this->createData($experiment,
                            $parameters,
                            $samples,
                            $quantity,
                            'positives',
                            $parameters['minvalues'] - 1);
                        break;
                    case QPCR::ERROR_STDDEV:
                        $this->createData($experiment,
                            $parameters,
                            $samples,
                            $quantity,
                            array_merge(
                                array_fill(0, $parameters['minvalues'] - 1, 1),
                                [$parameters['cutoff']]
                            ));
                        break;
                    case QPCR::ERROR_REPEAT:
                        $this->createData($experiment,
                            $parameters,
                            $samples,
                            $quantity,
                            array_merge(
                                array_fill(0, $parameters['minvalues'] - 1, 1),
                                [null]
                            ));
                        break;
                }
            }
        }

        $experiment->samples()->attach(array_pluck($samples,'id'));

        return (new QPCR(null, $this->parameters->keyBy('target')))->resultQuery([
            'assay_id' => $experiment->assay_id
        ])->get();
    }

    /**
     * @param $experiment
     * @param $parameters
     * @param array $samples
     * @param $type
     * @param $quantity
     * @param null $replications
     */
    protected function createData($experiment, $parameters, array &$samples, $quantity, $type = 'positive', $replications = null)
    {
        $replications = $replications ?? $parameters['minvalues'];

        for ($i = 0; $i < $quantity; $i++) {
            $sample = factory(Sample::class)->create(['study_id' => $experiment->study_id]);
            $result = $sample->results()->create([
                'study_id' => $experiment->study_id,
                'assay_id' => $experiment->assay_id,
                'target' => $parameters['target']
            ]);

            if (is_array($type)) {
                $value = $type;
            } else {
                $value = array_fill(0,
                    $replications,
                    $type == 'positive' ? rand(1, $parameters['cutoff'])
                        : $this->randomNegativeValue($parameters['cutoff'])
                );
            }
            for ($k = 0; $k < $replications; $k++) {
                $result->resultData()->create([
                    'study_id' => $experiment->study_id,
                    'primary_value' => $value[$k],
                    'secondary_value' => 'A1',
                    'experiment_id' => $experiment->id,
                    'sample_id' => $sample->sample_id,
                    'extra' => [
                        'reactid' => '12'
                    ]
                ]);
            }
            $samples[] = $sample;
        }
    }

    protected function randomNegativeValue($cutoff)
    {
        if (rand(0, 1) == 1) {
            return null;
        }

        return rand($cutoff + 1, $cutoff + 10);
    }
}