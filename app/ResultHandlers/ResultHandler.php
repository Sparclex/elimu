<?php

namespace App\ResultHandlers;

abstract class ResultHandler
{
    protected $filename;

    protected $parameters;

    public function __construct($filename, $parameters)
    {
        $this->filename = $filename;
        $this->parameters = $parameters;
    }

    abstract public function handle();
}
