<?php

namespace App\Entity\Validator;

readonly class UniqueSample extends AbstractUnique
{
    public int $stratigraphicUnit;
    public int $number;

    public function __construct(array $criteria, public bool $unique)
    {
        foreach (['stratigraphicUnit', 'number'] as $prop) {
            if (!isset($criteria[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' criteria");
            }
            $this->$prop = $criteria[$prop];
        }
    }

    public function getId(): string|int
    {
        return sprintf('%s.%s', $this->stratigraphicUnit, $this->number);
    }
}
