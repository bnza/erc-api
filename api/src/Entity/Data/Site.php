<?php

namespace App\Entity\Data;

class Site
{
    private int $id;

    public string $code;

    public string $name;

    public ?string $description;

    public bool $public = false;

    public iterable $users;

    public iterable $areas;

    public iterable $stratigraphicUnits;

    public function getId(): int
    {
        return $this->id;
    }
}
