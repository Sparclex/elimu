<?php

namespace App\FileTypes;

use App\Collections\RdmlCollection;
use App\Collections\RdmlParameterCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Nathanmac\Utilities\Parser\Facades\Parser;
use Symfony\Component\HttpFoundation\File\File;

class RDML
{
    public const TMP_ZIP_EXTRACT_PATH = 'tmp-rdml';

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    private $file;

    /**
     * Parsed xml data
     *
     * @var RdmlCollection
     */
    private $data;

    /**
     *
     * @var Collection
     */
    private $cyclesOfQuantification;

    /**
     * @var RdmlParameterCollection
     */
    private $inputParameters;

    /**
     *
     * @var array
     */
    private $alphabet;

    private $lastError;

    /**
     * Represents a rdml file
     *
     * @param File $file
     * @param array|null $inputParameters
     */
    public function __construct(File $file, $inputParameters = null)
    {

        $this->file = $file;
        $this->inputParameters = new RdmlParameterCollection($inputParameters);
        $this->alphabet = range('a', 'z');
    }

    public function containsOnlyOneFile()
    {
        $zip = new \ZipArchive();
        if ($zip->open($this->file->getRealPath()) !== true) {
            return false;
        }
        $numberOfFiles = $zip->numFiles;

        $zip->close();

        return $numberOfFiles === 1;
    }

    /**
     * @return RdmlCollection
     */
    public function getData()
    {
        if (!$this->data) {
            $zip = new \ZipArchive();
            if ($zip->open($this->file->getRealPath()) !== true) {
                $this->data = new RdmlCollection();
            }
            if ($zip->numFiles !== 1) {
                $zip->close();
                $this->data = new RdmlCollection();
            }
            $zip->extractTo(storage_path('app/' . self::TMP_ZIP_EXTRACT_PATH));
            $zip->close();

            $xmlPath = Storage::files(self::TMP_ZIP_EXTRACT_PATH)[0];
            try {
                $this->data = Parser::xml(Storage::get($xmlPath));
            } catch (\Exception $e) {
                $this->data = new RdmlCollection();
            } finally {
                Storage::deleteDirectory(self::TMP_ZIP_EXTRACT_PATH);
            }
            $this->data = new RdmlCollection($this->data);
        }
        return $this->data;
    }


    /**
     * Returns the Cq values for all runs
     *
     * @return \Illuminate\Support\Collection
     */
    public function cyclesOfQuantification()
    {
        if (!$this->cyclesOfQuantification) {
            $this->cyclesOfQuantification = $this->getData()->parse($this->inputParameters->thresholds());
        }

        return $this->cyclesOfQuantification;
    }

    /**
     * Returns the Cq values for all runs
     *
     * @return \Illuminate\Support\Collection
     */
    public function cyclesOfQuantificationWithoutControl()
    {
        return $this->cyclesOfQuantification()->reject(
            function ($sample) {
                return in_array(strtolower($sample['sampleId']), RdmlCollection::CONTROL_IDS);
            }
        );
    }

    public function getExperimentData()
    {
        return $this->getData()->experimentData();
    }

    public function getChartDataFor($sampleId, $position, $target)
    {
        return $this->getData()->getChartDataFor($sampleId, $position, $target, $this->inputParameters->thresholds());
    }

    public function hasValidTargets()
    {
        if (!$this->inputParameters) {
            return false;
        }
        $parameterTargets = $this->inputParameters->pluck('target');
        $overfulTargets = collect();
        foreach ($this->getData()->targets()->keys() as $target) {
            $index = $parameterTargets->search($target);
            if ($index === false) {
                $overfulTargets->push($target);
            } else {
                $parameterTargets->splice($index, 1);
            }
        }
        if ($overfulTargets->isNotEmpty() && $parameterTargets->isNotEmpty()) {
            $this->lastError = sprintf(
                'Targets "%s" are present in rdml file but not in the input parameters.
            Targets "%s" are present in the input parameters but not in the rdml file',
                $overfulTargets->implode(', '),
                $parameterTargets->implode(', ')
            );
        } else {
            if ($overfulTargets->isNotEmpty()) {
                $this->lastError = sprintf(
                    'Targets "%s" are present in rdml file but not in the input parameters.',
                    $overfulTargets->implode(', ')
                );
            } else {
                if ($parameterTargets->isNotEmpty()) {
                    $this->lastError = sprintf(
                        'Targets "%s" are present in the input parameters but not in the rdml file',
                        $parameterTargets->implode(', ')
                    );
                }
            }
        }
        if ($this->lastError) {
            return false;
        }
        return true;
    }

    public function hasEnoughRepetitions()
    {
        $this->lastError = '';

        $targets = $this->cyclesOfQuantificationWithoutControl()
            ->groupBy(['target', 'sampleId']);

        $invalidSampleIds = [];
        foreach ($targets as $target => $samples) {
            $requiredRepetitions = $this->inputParameters->firstWhere('target', $targets)['minvalues'];
            foreach ($samples as $sampleId => $sample) {
                if (count($sample) < $requiredRepetitions) {
                    $invalidSampleIds[] = $sampleId;
                }
            }
        }

        if (count($invalidSampleIds)) {
            $this->lastError = 'Not enough values for samples: ' . implode(',', $invalidSampleIds);
        }

        return !((bool) $this->lastError);
    }

    public function hasCorrectDeviation()
    {
        $targets = $this->cyclesOfQuantificationWithoutControl()
            ->groupBy(['target', 'sampleId']);

        $invalidSamples = [];
        foreach ($targets as $target => $samples) {
            $cutoffStandardDeviation = $this->inputParameters->firstWhere('target', $target)['cuttoffstdev'];
            foreach ($samples as $sampleId => $sample) {
                if (collect($sample)->standardDeviation('cq') > $cutoffStandardDeviation) {
                    $invalidSamples[] = sprintf('%s (%s)', $sampleId, $target);
                }
            }
        }

        if (count($invalidSamples)) {
            $this->lastError =
                'The following samples might have an invalid cq value: ' . implode(',', $invalidSamples);
        }

        return !((bool)
        $this->lastError);
    }

    public function hasValidControls()
    {
        $controls = $this->cyclesOfQuantification()
            ->filter(function ($sample) {
                return in_array(strtolower($sample['sampleId']), RdmlCollection::CONTROL_IDS);
            });

        $invalidControls = [];
        foreach ($controls as $control) {
            switch (strtolower($control['sampleId'])) {
                case RdmlCollection::POSITIVE_CONTROL:
                    if (!$control['cq']) {
                        $invalidControls[] = sprintf(
                            '%s (%s, %s)',
                            "Pos",
                            $control['target'],
                            $control['position']
                        );
                    }
                    break;
                case RdmlCollection::NEGATIVE_CONTROL:
                    if ($control['cq']) {
                        $invalidControls[] = sprintf(
                            '%s (%s, %s)',
                            "Pos",
                            $control['target'],
                            $control['position']
                        );
                    }
                    break;
                case RdmlCollection::NTC_CONTROL:
                    if ($control['cq']) {
                        $invalidControls[] = sprintf(
                            '%s (%s, %s)',
                            "Pos",
                            $control['target'],
                            $control['position']
                        );
                    }
                    break;
            }
        }

        if (count($invalidControls)) {
            $this->lastError =
                'The following controls are invalid: ' . implode(',', $invalidControls);
        }

        return (bool)$this->lastError;
    }

    public function getSampleIds()
    {
        return $this->getData()->sampleIds();
    }

    public function getLastError()
    {
        return $this->lastError;
    }
}
