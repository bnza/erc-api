<?php

namespace App\Entity\Validator;

abstract readonly class AbstractUnique implements UniqueInterface
{
    protected bool $unique;

    public function isUnique(): bool
    {
        return $this->unique;
    }
}
