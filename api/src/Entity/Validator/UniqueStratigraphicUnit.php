<?php

namespace App\Entity\Validator;

readonly class UniqueStratigraphicUnit extends AbstractUnique
{
    public int $site;
    public int $year;
    public int $number;

    public function __construct(array $criteria, public bool $unique)
    {
        foreach (['site', 'year', 'number'] as $prop) {
            if (!isset($criteria[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' criteria");
            }
            $this->$prop = $criteria[$prop];
        }
    }

    public function getId(): string|int
    {
        return sprintf('%s.%s.%s', $this->site, $this->year, $this->number);
    }
}
