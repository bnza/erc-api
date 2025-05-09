<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\StratigraphicUnit;
use App\Entity\Data\Vocabulary\StratigraphicUnit\Relationship;

class StratigraphicUnitsRelationship
{
    private int $id;

    public StratigraphicUnit $sxSU;

    public Relationship $relationship;

    public StratigraphicUnit $dxSU;

    public function getId(): int
    {
        return $this->id;
    }
}
