<?php

namespace App\Fields;

use Laravel\Nova\Fields\Field;

class Status extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'status';

    public $loadingWord;

    public $successWord;

    public $failedWord;

    /**
     * Specify the values that should be considered "loading".
     *
     * @param  string $loadingWord
     * @return \App\Fields\Status
     */
    public function loadingWhen($loadingWord)
    {
        $this->loadingWord = $loadingWord;

        return $this;
    }

    /**
     * Specify the values that should be considered "success".
     *
     * @param  string $successWord
     * @return \App\Fields\Status
     */
    public function successWhen($successWord)
    {
        $this->successWord = $successWord;

        return $this;
    }

    /**
     * Specify the values that should be considered "failed".
     *
     * @param  string $failedWord
     * @return \App\Fields\Status
     */
    public function failedWhen($failedWord)
    {
        $this->failedWord = $failedWord;

        return $this;
    }

    public function meta()
    {
        return [
            'loadingWord' => $this->loadingWord,
            'successWord' => $this->successWord,
            'failedWord' => $this->failedWord,
            'options' => [
                $this->loadingWord,
                $this->successWord,
                $this->failedWord,
            ],
        ];
    }
}
