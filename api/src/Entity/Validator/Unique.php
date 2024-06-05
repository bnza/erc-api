<?php

namespace App\Entity\Validator;

readonly class Unique
{
    public function __construct(public string|int $id, public bool $unique)
    {
    }

    public function getId(): string|int
    {
        return $this->id;
    }
}
