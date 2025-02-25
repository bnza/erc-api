<?php

namespace App\Entity\Data\Zooarchaeology;

use App\Entity\Data\StratigraphicUnit;

class Bone
{
    private int $id;

    public StratigraphicUnit $stratigraphicUnit;

    public string $taxonomy;

    public bool $unidentified = false;

    public ?string $notes;

    public function getId(): int
    {
        return $this->id;
    }
}
