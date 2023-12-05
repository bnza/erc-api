<?php

namespace App\Entity\Data;

class StratigraphicUnit
{
    private int $id;

    public Site $site;

    public Area $area;

    public int $number;

    public ?string $areaCode;

    public ?string $interpretation;

    public ?string $description;

    public bool $public = false;

    public function getId(): int
    {
        return $this->id;
    }
}
