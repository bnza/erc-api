<?php

namespace App\Entity\Validator;

interface UniqueInterface
{
    public function isUnique(): bool;
}
