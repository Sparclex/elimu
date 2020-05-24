<?php

namespace App\Experiments\Qpcr;

class QualifyMessage implements QualifiedResponse
{
    /** @var string */
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function message(): string
    {
        return $this->message;
    }
}
