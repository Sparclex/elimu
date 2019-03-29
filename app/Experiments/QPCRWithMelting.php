<?php

namespace App\Experiments;

use App\Exceptions\ExperimentException;
use App\Models\Sample;
use App\Support\QPCRResultSpecifier;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Nathanmac\Utilities\Parser\Facades\Parser;
use ZipArchive;

class QPCRWithMelting extends QPCR
{
    protected $meltingFileContents;

    protected $meltingData;

    public function getDatabaseData($experiment): Collection
    {
        return $this->getResultData()->map(
            function ($data) use ($experiment) {
                $meltingPoint = $this->getMeltingTemperatureFor(
                    $data['sampleId'],
                    $data['target'],
                    $data['position'],
                    $experiment
                );

                return [
                    'sample' => $data['sampleId'],
                    'target' => $data['target'],
                    'primary_value' => $this->meltingTemperatureInRange(
                        $meltingPoint,
                        $experiment,
                        $data['target']
                    ) ? $data['cq'] : null,
                    'secondary_value' => $data['reactId'],
                    'extra' => json_encode(
                        array_merge(
                            Arr::only(
                                array_change_key_case($data, CASE_LOWER),
                                ['reactid', 'fluor', 'content']
                            ),
                            [
                                'melting_temperature' => $meltingPoint,
                            ]
                        )
                    ),
                ];
            }
        );
    }

    protected function meltingTemperatureInRange($meltingPoint, $experiment, $target)
    {
        if ($meltingPoint === null) {
            return false;
        }

        $parameters = $experiment->assay->definitionFile->parameters->first(
            function ($targetParameters) use ($target) {
                return strtolower($targetParameters['target']) == strtolower($target);
            }
        );

        if (! isset($parameters['melt_min']) || ! isset($parameters['melt_max'])) {
            throw ValidationException::withMessages(
                [
                    'result_file' => 'melt_min and melt_max have to be defined in the assay definition file',
                ]
            );
        }

        return $meltingPoint >= $parameters['melt_min'] && $meltingPoint <= $parameters['melt_max'];
    }

    protected function getFileContents()
    {
        if ($this->fileContents == null) {
            $this->retrieveFileContents();
        }

        return $this->fileContents;
    }

    protected function getMeltingFileContents()
    {
        if ($this->meltingFileContents == null) {
            $this->retrieveFileContents();
        }

        return $this->meltingFileContents;
    }

    protected function retrieveFileContents()
    {
        $zip = new ZipArchive();

        if ($zip->open($this->resultFile) !== true) {
            throw new ExperimentException('Invalid result file');
        }

        $rdmlIndex = $this->findIndexFromExtension($zip, '.rdml');
        $meltingDataIndex = $this->findIndexFromExtension($zip, '.xml');

        if ($rdmlIndex === false) {
            throw new ExperimentException('No rdml file given');
        }

        if ($meltingDataIndex === false) {
            throw new ExperimentException('No melting data file given');
        }

        $rdml = new ZipArchive();
        $file = tempnam(sys_get_temp_dir(), 'rdml');
        file_put_contents($file, $zip->getFromIndex($rdmlIndex));
        $rdml->open($file);

        if ($rdml->numFiles == 0) {
            throw new ExperimentException('Invalid rdml file');
        }

        $this->fileContents = $rdml->getFromIndex(0);
        $this->meltingFileContents = $zip->getFromIndex($meltingDataIndex);

        $rdml->close();
        $zip->close();

        if (file_exists($file)) {
            unlink($file);
        }
    }

    protected function findIndexFromExtension(ZipArchive $zip, $extension)
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            if (Str::contains($filename, '/')) { // ignore folders
                continue;
            }
            if (Str::endsWith($filename, $extension)) {
                return $i;
            }
        }

        return false;
    }

    protected function getMeltingData()
    {
        if (! $this->meltingData) {
            $this->meltingData = collect(Parser::xml($this->getMeltingFileContents())['Row'])
                ->filter(
                    function ($row) {
                        return count(array_filter($row));
                    }
                );
        }

        return $this->meltingData;
    }

    public static function headings($assay): array
    {
        $headings = [];

        foreach ($assay->definitionFile->parameters->pluck('target') as $target) {
            $headings[] = 'replicas_'.$target;
            $headings[] = 'mean_cq_'.$target;
            $headings[] = 'sd_cq_'.$target;
            $headings[] = 'qual_'.$target;
            $headings[] = 'quant_'.$target;
            $headings[] = 'melt_temp_'.$target;
        }

        return $headings;
    }

    public static function exportQuery($assay, $resultIds)
    {
        return Sample::whereHas(
            'results',
            function ($query) use ($resultIds) {
                return $query->whereIn('results.id', $resultIds);
            }
        )
            ->with(
                [
                    'results' => function ($query) use ($assay, $resultIds) {
                        $query = $query
                            ->select('results.*')
                            ->selectRaw('avg(primary_value) as avg_cq')
                            ->selectRaw('count(*) as replicas')
                            ->selectRaw('stddev(primary_value) as stddev')
                            ->selectRaw('avg(JSON_EXTRACT(result_data.extra, "$.melting_temperature")) as avg_melt')
                            ->join('result_data', 'results.id', 'result_id')
                            ->where('included', true)
                            ->whereIn('result_id', $resultIds)
                            ->groupBy('result_id');

                        $targetPositives = $assay->definitionFile->parameters->map(
                            function ($targetParameters) {
                                return [
                                    'sql' => '(primary_value <= ? and results.target = ?)',
                                    'bindings' => [$targetParameters['cutoff'], $targetParameters['target']],
                                ];
                            }
                        );

                        $query->selectRaw(
                            sprintf(
                                'count(case when (%s) and primary_value <> 0 then 1 end) as positives',
                                $targetPositives->pluck('sql')->implode(' or ')
                            ),
                            $targetPositives->pluck('bindings')->flatten()->toArray()
                        );

                        return $query;
                    },
                    'sampleTypes' => function ($query) use ($assay) {
                        return $query->where('sample_types.id', $assay->definitionFile->sample_type_id);
                    },
                ]
            );
    }

    public static function exportMap($row, $assay)
    {
        $map = [];

        foreach ($assay->definitionFile->parameters as $targetParameters) {
            $result = $row->results->first(
                function ($result) use ($targetParameters) {
                    return strtolower($result->target) == strtolower($targetParameters['target']);
                }
            );

            if (! $result) {
                $map[] = '';
                $map[] = '';
                $map[] = '';
                $map[] = '';
                $map[] = '';
                $map[] = '';
                continue;
            }

            $specifier = new QPCRResultSpecifier($targetParameters, $result);

            $map[] = $result->replicas;
            $map[] = $result->avg_cq;
            $map[] = $result->stddev;
            $map[] = $specifier->qualitative();
            $map[] = $specifier->quantitative();
            $map[] = $result->avg_melt;
        }

        return $map;
    }

    protected function getMeltingTemperatureFor($sampleId, $target, $position, $experiment)
    {
        $meltingPoint = false;

        foreach ($this->getMeltingData() as $data) {
            if (! isset($data['Sample']) || ! isset($data['Target'])) {
                throw new ExperimentException('Invalid melting data file');
            }
            if (strtolower($data['Sample']) == strtolower($sampleId)
                && strtolower($data['Target']) == strtolower($target)
                && strtolower($data['Well']) == strtolower($position)
            ) {
                $meltingPoint = $data['Melt_Temperature'] == 'None' ? null : $data['Melt_Temperature'];

                if ($this->meltingTemperatureInRange($meltingPoint, $experiment, $target)) {
                    return $meltingPoint;
                }
            }
        }

        if ($meltingPoint !== false) {
            return $meltingPoint;
        }

        throw new ExperimentException(
            sprintf('No melting data found for sample: %s (%s, %s)', $sampleId, $target, $position)
        );
    }
}
