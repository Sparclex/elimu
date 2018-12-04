<?php

namespace App\Models;

class Result extends DependsOnStudy
{
    protected $fillable = ['sample_id', 'target', 'assay_id'];

    protected $output = null;

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function resultData()
    {
        return $this->hasMany(ResultData::class);
    }

    /**
     * @return int|string numeric if there is a result and string for error message
     */
    public function getOutput()
    {
        if (!$this->output) {
            $this->output = $this->determineOutput();
        }

        return $this->output;
    }

    private function determineOutput()
    {
        $acceptedResults = $this->resultData->onlyAccepted();

        if (!$acceptedResults->hasEnoughValues($this->inputParameter['minvalues'])) {
            return "Insufficient amount of data";
        }

        if (!$acceptedResults->standardDeviationIsInRange($this->inputParameter['cuttoffstdev'])) {
            return "Standard deviation to higher than ". $this->inputParameter['cuttoffstdev'];
        }

        $result = $acceptedResults->determineResult($this->inputParameter['cutoff']);

        if ($result == -1) {
            return "Needs Repetition";
        }

        return $result;
    }

    public function getStatusAttribute()
    {
        return is_string($this->getOutput()) ? 'Pending' : 'Verified';
    }

    public function getValueAttribute()
    {
        if (is_string($this->getOutput())) {
            return $this->getOutput();
        }
        if ($this->getOutput() === 1 &&
            strtolower(
                $this->inputParameter['quant']
            ) == 'yes') {
            return $this->resultData
                    ->onlyAccepted()
                    ->quantitativeOutput($this->inputParameter['slope'], $this->inputParameter['intercept'])
                     ." (Positive)";
        }
        return $this->getOutput() === 1 ? 'Positive' : 'Negative';
    }

    public function getInputParameterAttribute()
    {
        return collect($this->assay->inputParameter->parameters)
            ->firstWhere('target', $this->target);
    }
}
