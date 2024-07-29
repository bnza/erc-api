<?php

namespace App\Entity\Validator;

readonly class Unique extends AbstractUnique
{
    public function __construct(public string|int $id, protected bool $unique)
    {
    }

    public function getId(): string|int
    {
        return $this->id;
    }
}
