<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\MediaObject;
use App\Entity\Data\StratigraphicUnit;

class StratigraphicUnitsMediaObject
{
    private $id;

    public StratigraphicUnit $item;

    public MediaObject $mediaObject;

    public ?string $description;

    public function getId(): int
    {
        return $this->id;
    }
}
