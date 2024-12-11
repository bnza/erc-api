<?php

namespace App\Entity\Data;

use DateTimeImmutable;

class Sample
{
    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    public int $number;

    public ?string $collector;

    public ?DateTimeImmutable $takingDate;

    public ?string $description;

    public bool $public = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return sprintf(
            '%s/%u',
            $this->stratigraphicUnit->getCode(),
            $this->number
        );
    }
}
