<?php

namespace App\Services;

use App\Exceptions\AssayNotFound;
use App\Exceptions\AssayTypeNotSupported;
use App\Experiments\Qpcr\Qualifier;
use App\Experiments\Qpcr\Quantifier;
use App\Models\Study;
use App\Queries\AnalizedResultsQuery;
use App\Queries\GetAssayDefinitionFile;
use Illuminate\Support\Collection;

class ListEvaluatedResults
{
    /** @var GetAssayDefinitionFile */
    protected $getAssayDefinitionFile;

    public function __construct(GetAssayDefinitionFile $getAssayDefinitionFile)
    {
        $this->getAssayDefinitionFile = $getAssayDefinitionFile;
    }

    public function get(Study $study, string $assayName, AnalizedResultsQuery $query): Collection
    {
        $definitionFile = $this->getAssayDefinitionFile->run($study->id, $assayName);

        if (!$definitionFile) {
            throw new AssayNotFound($assayName);
        }

        if ($definitionFile->result_type !== 'qPCR RDML') {
            throw new AssayTypeNotSupported($definitionFile->result_type);
        }

        $samples = $query->run($study->id, $definitionFile->assay->id, $definitionFile->parameters->toArray())->get();

        return $samples->map(static function ($sample) use ($definitionFile) {
            $newSample = [
                'id' => $sample->sample_id,
                'subject_id' => $sample->subject_id,
                'collected_at' => $sample->collected_at,
                'birthdate' => $sample->birthdate,
                'gender' => $sample->gender,
            ];

            foreach ($definitionFile->parameters as $targetParameters) {
                $target = $targetParameters['target'];
                $normalizedTarget = preg_replace('/[\W]/', '', $targetParameters['target']);
                $newSample["replicas_{$target}"] = $sample->{"replicas_{$normalizedTarget}"};
                $newSample["mean_cq_{$target}"] = $sample->{"mean_cq_{$normalizedTarget}"};
                $newSample["sd_cq_{$target}"] = $sample->{"sd_cq_{$normalizedTarget}"};
                $newSample["qual_{$target}"] = (new Qualifier())->qualify(
                    $sample->{"mean_cq_{$normalizedTarget}"},
                    $sample->{"sd_cq_{$normalizedTarget}"},
                    $sample->{"positives_{$normalizedTarget}"},
                    $sample->{"replicas_{$normalizedTarget}"},
                    $targetParameters['minvalues'],
                    $targetParameters['cuttoffstdev']
                )->message();

                $newSample["quant_{$target}"] = $newSample["qual_{$target}"] === Qualifier::POSITIVE
                    ? (new Quantifier())->quantify(
                        $sample->{"mean_cq_{$normalizedTarget}"},
                        $targetParameters['slope'] ?? null,
                        $targetParameters['intercept'] ?? null
                    )
                    : null;
            }

            return $newSample;
        });
    }
}
