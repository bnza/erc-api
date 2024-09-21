<?php

namespace App\Entity\Data;

class StratigraphicUnit
{
    private int $id;

    public Site $site;

    public ?Area $area;

    public int $number;

    public int $year;

    public ?string $interpretation;

    public ?string $description;

    public bool $public = false;

    public iterable $samples;

    public iterable $mediaObjects;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return sprintf('%s.%u.%u', $this->site->code, $this->year, $this->number);
    }
}
