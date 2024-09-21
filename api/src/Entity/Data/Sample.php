<?php

namespace App\Entity\Data;

class Sample
{
    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    public int $number;

    public ?string $description;

    public bool $public = false;

    public function getId(): int
    {
        return $this->id;
    }
}
