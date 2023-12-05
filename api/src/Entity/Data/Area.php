<?php

namespace App\Entity\Data;

class Area
{
    private int $id;

    public Site $site;

    public string $code;

    public ?string $description;

    public iterable $stratigraphicUnits;

    public bool $public = false;

    public function getId(): int
    {
        return $this->id;
    }
}
