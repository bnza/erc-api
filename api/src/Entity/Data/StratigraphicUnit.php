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

    public bool $public = true;

    public iterable $samples;

    public iterable $potteries;
    public iterable $mediaObjects;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return sprintf('%s.%u.%u', $this->site->code, substr($this->year, -2), $this->number);
    }
}
