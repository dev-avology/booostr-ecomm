<?php

namespace App\Services;

use RuntimeException;

class PartnerCreateStoreStepException extends RuntimeException
{
    protected string $step;

    public function __construct(string $step, \Throwable $previous)
    {
        $this->step = $step;
        parent::__construct($previous->getMessage(), 0, $previous);
    }

    public function getStep(): string
    {
        return $this->step;
    }
}
