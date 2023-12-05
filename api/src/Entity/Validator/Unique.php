<?php

namespace App\Entity\Validator;

readonly class Unique
{
    public function __construct(public bool $value)
    {
    }
}
