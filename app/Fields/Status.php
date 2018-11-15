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

    private $loadingWord;

    private $loadingKey;

    private $successWord;

    private $successKey;

    private $failedWord;

    private $failedKey;

    /**
     * Specify the values that should be considered "loading".
     *
     * @param  string $loadingWord
     * @param mixed $loadingKey
     * @return \App\Fields\Status
     */
    public function loadingWhen($loadingWord, $loadingKey = null)
    {
        $this->loadingWord = $loadingWord;
        $this->loadingKey = $loadingKey ?? $loadingWord;

        return $this;
    }

    /**
     * Specify that null should be considered "loading".
     *
     * @param  string $loadingWord
     * @return \App\Fields\Status
     */
    public function loadingWhenNull($loadingWord)
    {
        $this->loadingWord = $loadingWord;
        $this->loadingKey = null;

        return $this;
    }

    /**
     * Specify the values that should be considered "success".
     *
     * @param  string $successWord
     * @param mixed $successKey
     * @return \App\Fields\Status
     */
    public function successWhen($successWord, $successKey = null)
    {
        $this->successWord = $successWord;
        $this->successKey = $successKey ?? $successWord;

        return $this;
    }

    /**
     * Specify the values that should be considered "failed".
     *
     * @param  string $failedWord
     * @param mixed $failedKey
     * @return \App\Fields\Status
     */
    public function failedWhen($failedWord, $failedKey = null)
    {
        $this->failedWord = $failedWord;
        $this->failedKey = $failedKey ?? $failedWord;

        return $this;
    }

    public function meta()
    {
        return [
            'loadingWord' => $this->loadingKey,
            'successWord' => $this->successKey,
            'failedWord' => $this->failedKey,
            'options' => [
                $this->loadingKey => $this->loadingWord,
                $this->successKey => $this->successWord,
                $this->failedKey => $this->failedWord,
            ],
        ];
    }
}
