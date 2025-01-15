<?php

namespace App\Entity\Validator;

readonly class UniqueMicroStratigraphicUnit extends AbstractUnique
{
    public int $sample;
    public int $number;

    public function __construct(array $criteria, public bool $unique)
    {
        foreach (['sample', 'number'] as $prop) {
            if (!isset($criteria[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' criteria");
            }
            $this->$prop = $criteria[$prop];
        }
    }

    public function getId(): string|int
    {
        return sprintf('%s.%s', $this->sample, $this->number);
    }
}
