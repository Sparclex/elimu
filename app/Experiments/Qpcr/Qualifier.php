<?php

namespace App\Experiments\Qpcr;

class Qualifier
{
    const POSITIVE = 'positive';

    const NEGATIVE = 'negative';

    const NOT_ENOUGH_DATA = 'Not enough data';

    const NEEDS_REPETITION = 'Needs repetition';

    const STANDARD_DEVIATION_TOO_HIGH = 'Standard deviation too high';

    public function qualify(
        float $cqValue,
        float $stddev,
        int $numberOfPositiveResults,
        int $numberOfResults,
        int $minimalNumberOfResults,
        float $cutoffStdev
    ): QualifiedResponse {
        if ($numberOfResults < $minimalNumberOfResults) {
            return new QualifyError(self::NOT_ENOUGH_DATA);
        }

        if($this->resultsDiverge($numberOfPositiveResults, $numberOfResults)) {
            return new QualifyError(self::NEEDS_REPETITION);
        }

        if($numberOfPositiveResults > 0 && $stddev > $cutoffStdev) {
            return new QualifyError(self::STANDARD_DEVIATION_TOO_HIGH);
        }

        return $cqValue !== null && $numberOfPositiveResults > 0
            ? new QualifyMessage(self::POSITIVE)
            : new QualifyMessage(self::NEGATIVE);
    }

    /**
     *
     *
     * @param int $numberOfPositiveResults
     * @param int $numberOfResults
     * @return bool
     */
    private function resultsDiverge(int $numberOfPositiveResults, int $numberOfResults): bool
    {
        return $numberOfPositiveResults !== $numberOfResults && $numberOfPositiveResults !== 0;
    }
}
