<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class PartnerCreateStoreStepException extends RuntimeException
{
    public function __construct(protected string $step, \Throwable $previous)
    {
        parent::__construct($previous->getMessage(), 0, $previous);
    }

    public function getStep(): string
    {
        return $this->step;
    }
}
