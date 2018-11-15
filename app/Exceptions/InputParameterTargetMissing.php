<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;
use Throwable;

class InputParameterTargetMissing extends \Exception
{

    private $target;

    public function __construct($target)
    {
        parent::__construct("", 0, null);
        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }
}
